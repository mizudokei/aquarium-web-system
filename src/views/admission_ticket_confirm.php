<?php
session_start();

// セッションからデータを取得
$selectedDate = $_SESSION['selectedDate'];
$selectedTimeSlot = $_SESSION['selectedTimeSlot'];
$ticketQuantities = $_SESSION['ticketQuantities'] ?? [];

// 合計枚数と合計金額を計算
$totalQuantity = 0;
$totalPrice = 0;
foreach ($ticketQuantities as $ticket) {
    $totalQuantity += $ticket['quantity'];
    $totalPrice += $ticket['ticketPrice'] * $ticket['quantity'];
}

// ログに確認用メッセージを出力
error_log("チケット: " . print_r($ticketQuantities, true));
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>予約確認</title>
    <link rel="stylesheet" href="../../public/assets/css/admission_ticket_reservation.css">
</head>
<body>

    <header>
        <?php
            require_once '../../public/header.php'; // 共通ヘッダーを読み込む
        ?>
    </header>

    <main>
        <h1>予約内容確認</h1>
        <div class="summary_card">
            <p>選択された日: <?php echo htmlspecialchars($selectedDate); ?></p>
            <p>選択された時間帯: <?php echo htmlspecialchars($selectedTimeSlot); ?></p>

            <h3>チケット情報</h3>
            <table class="ticket-table">
                <thead>
                    <tr>
                        <th>券種</th>
                        <th>価格</th>
                        <th>枚数</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ticketQuantities as $ticket) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($ticket['ticketType']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['ticketPrice']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['quantity']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <p>合計枚数: <?php echo $totalQuantity; ?></p>
            <p>合計金額: ¥<?php echo number_format($totalPrice); ?></p>
        </div>

        <!-- 予約の確定ボタン -->
        <form action="../../src/controllers/process_admission_ticket_reservation.php" method="POST">
            <button type="submit">予約を確定する</button>
        </form>
    </main>

</body>
</html>
