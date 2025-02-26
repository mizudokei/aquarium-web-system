<?php
require_once(__DIR__ . '../../models/db_connect.php'); // db_connect.php をインクルード

header('Content-Type: application/json');

// リクエストを取得
$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['qr_data'])) {
    echo json_encode(['success' => false, 'message' => 'QRコードが読み取れませんでした']);
    exit;
}

list($reservation_id, $ticket_id) = explode('-', $data['qr_data']);

// 予約とチケットの情報を取得
$sql = "
    SELECT r.status, t.used_at, t.recipient_lastname, t.recipient_firstname, t.recipient_email, r.reservation_date, r.reservation_timeslot
    FROM reservations r
    JOIN reservation_tickets t ON r.id = t.reservation_id
    WHERE r.id = :reservation_id AND t.id = :ticket_id";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':reservation_id', $reservation_id, PDO::PARAM_STR);
$stmt->bindParam(':ticket_id', $ticket_id, PDO::PARAM_STR);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$result) {
    echo json_encode(['success' => false, 'message' => '無効なQRコードです']);
    exit;
}

if ($result['status'] !== 'reserved') {
    echo json_encode(['success' => false, 'message' => 'この予約は無効です']);
    exit;
}

if (!is_null($result['used_at'])) {
    echo json_encode(['success' => false, 'message' => 'このチケットはすでに使用されています']);
    exit;
}

// 名前またはメールアドレスが未設定の場合は無効とする
if (empty($result['recipient_lastname']) || empty($result['recipient_firstname']) || empty($result['recipient_email'])) {
    echo json_encode(['success' => false, 'message' => 'このQRコードには利用者情報が設定されていません']);
    exit;
}

// 予約のタイムスロットを処理（開始時間のみを抽出）
$reservation_timeslot = $result['reservation_timeslot'];
$timeslot_parts = explode('～', $reservation_timeslot);
$reservation_start_time = trim($timeslot_parts[0]);

// 固定日時として「2025-02-26 09:00」を設定
$fixed_time = new DateTime('2025-02-26 09:00');

// 予約日時と固定日時を比較
$reservation_time = new DateTime($result['reservation_date'] . ' ' . $reservation_start_time);

if ($fixed_time < $reservation_time) {
    echo json_encode(['success' => false, 'message' => 'まだ入場可能な時間ではありません']);
    exit;
}

$reservation_end_time = clone $reservation_time;
$reservation_end_time->add(new DateInterval('PT30M')); // 30分間の予約時間を設定

if ($fixed_time > $reservation_end_time) {
    echo json_encode(['success' => false, 'message' => '予約時間を過ぎています']);
    exit;
}

// 使用済みとして記録
$update_sql = "UPDATE reservation_tickets SET used_at = NOW() WHERE id = :ticket_id";
$update_stmt = $pdo->prepare($update_sql);
$update_stmt->bindParam(':ticket_id', $ticket_id, PDO::PARAM_STR);
$update_stmt->execute();

// 入場記録を追加
$log_sql = "INSERT INTO entry_logs (qr_code, ticket_id, status, timestamp) VALUES (:qr_code, :ticket_id, 'entry', NOW())";
$log_stmt = $pdo->prepare($log_sql);
$log_stmt->bindParam(':qr_code', $data['qr_data'], PDO::PARAM_STR);
$log_stmt->bindParam(':ticket_id', $ticket_id, PDO::PARAM_STR);
$log_stmt->execute();

echo json_encode(['success' => true, 'message' => '入場認証が成功しました']);
exit;
?>
