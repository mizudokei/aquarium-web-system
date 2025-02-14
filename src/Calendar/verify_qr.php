<?php
session_start();
require_once '../models/db_connect.php';
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['qr_data'])) {
	echo json_encode(["success" => false, "message" => "不正なリクエスト"]);
	exit;
}

// QRコードのデータ (形式: "予約ID-チケットID")
$qrData = $_POST['qr_data'];
list($reservation_id, $ticket_id) = explode("-", $qrData);

// 該当のチケットが存在するか確認
$query = "SELECT id, used_at FROM reservation_tickets WHERE reservation_id = :reservation_id AND id = :ticket_id";
$stmt = $pdo->prepare($query);
$stmt->execute([
	'reservation_id' => $reservation_id,
	'ticket_id' => $ticket_id
]);
$ticket = $stmt->fetch();

if (!$ticket) {
	echo json_encode(["success" => false, "message" => "チケットが見つかりません"]);
	exit;
}

// 既に使用済みか確認
if ($ticket['used_at'] !== null) {
	echo json_encode(["success" => false, "message" => "このチケットは既に使用済みです"]);
	exit;
}

// 使用日時を更新
$updateQuery = "UPDATE reservation_tickets SET used_at = NOW() WHERE id = :ticket_id";
$updateStmt = $pdo->prepare($updateQuery);
$updateStmt->execute(['ticket_id' => $ticket_id]);

echo json_encode(["success" => true, "message" => "認証成功"]);
exit;
?>
