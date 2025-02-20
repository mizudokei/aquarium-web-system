<?php
include(__DIR__ . '../../models/db_connect.php');
require_once 'qr_code_generator.php'; // QRコード生成用の関数

// セッションからデータを取得
$selectedDate = $_SESSION['selectedDate'];
$formattedDate = preg_replace('/(\d{4})年(\d{2})月(\d{2})日/', '$1-$2-$3', $selectedDate);
$selectedTimeSlot = $_SESSION['selectedTimeSlot'];
$ticketQuantities = $_SESSION['ticketQuantities'] ?? [];
$userId = $_SESSION['user_id']; // ログインしたユーザーIDを取得
$userBirth = $_SESSION['user_birth'];

// 合計枚数と合計金額を計算
$totalQuantity = 0;
$totalPrice = 0;
foreach ($ticketQuantities as $ticket) {
    $totalQuantity += $ticket['quantity'];
    $totalPrice += $ticket['ticketPrice'] * $ticket['quantity'];
}

// 予約情報を `reservations` テーブルに挿入
try {
    $pdo->beginTransaction();

    // ランダムな予約ID（16桁の英数字）を生成
    $reservationId = bin2hex(random_bytes(8)); // 16桁のランダムな予約IDを生成

    // 予約IDが正常に生成されたか確認
    if (empty($reservationId)) {
        throw new Exception('Failed to generate reservation ID.');
    }

    // 予約情報を `reservations` テーブルに追加
    $query = "INSERT INTO reservations (id, user_id, reservation_date, reservation_timeslot, total_price, status)
                VALUES (:id, :user_id, :reservation_date, :reservation_timeslot, :total_price, 'reserved')";
    $stmt = $pdo->prepare($query);
    
    // 挿入処理
    if ($stmt->execute([
        ':id' => $reservationId,
        ':user_id' => $userId,
        ':reservation_date' => $formattedDate,
        ':reservation_timeslot' => $selectedTimeSlot,
        ':total_price' => $totalPrice,
    ])) {
    } else {
        // 挿入失敗時のエラー
        throw new Exception('Failed to insert reservation.');
    }

    // それぞれのチケットを `reservation_tickets` テーブルに追加
    foreach ($ticketQuantities as $ticket) {
        for ($i = 0; $i < $ticket['quantity']; $i++) {
            // reservation_idが正しく渡されているか確認
            if (empty($reservationId)) {
                throw new Exception('Reservation ID is empty.');
            }

            // ランダムなチケットID（16桁の英数字）を生成
            $ticketId = bin2hex(random_bytes(8)); // 16桁のランダムなチケットIDを生成

            // QRコードを生成
            $qrCode = generateQRCode($reservationId, $ticketId); // QRコード生成関数を呼び出し

            // eventIdが設定されていない場合にデフォルト値(null)を設定
            $eventId = isset($ticket['eventId']) ? $ticket['eventId'] : null; // デフォルトはnull

            // reservation_ticketsテーブルに挿入
            $query = "INSERT INTO reservation_tickets (id, reservation_id, ticket_type, admission_fee_type_id, event_id, qr_code)
                        VALUES (:id, :reservation_id, :ticket_type, :admission_fee_type_id, :event_id, :qr_code)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':id' => $ticketId, // ランダムに生成したチケットIDを使用
                ':reservation_id' => $reservationId, // 予約IDを使用
                ':ticket_type' => $ticket['ticketType'],
                ':admission_fee_type_id' => $ticket['admissionFeeTypeId'], // 必要なら適切な値を設定
                ':event_id' => $eventId,
                ':qr_code' => $qrCode,
            ]);
        }
    }


    // トランザクションをコミット
    $pdo->commit();

    // 予約完了後、完了画面へリダイレクト
    header('Location: /?page=finalize_reservation');
    exit;

} catch (PDOException $e) {
    // エラーハンドリング（ロールバック）
    $pdo->rollBack();
    echo '予約処理中にエラーが発生しました。';
} catch (Exception $e) {
    echo '予約処理中にエラーが発生しました。';
}
?>