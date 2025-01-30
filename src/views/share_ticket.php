<?php
session_start();
require_once '../../config/db_connect.php';

// ユーザーIDをセッションから取得
$user_id = $_SESSION['user_id'];

// 所持チケットを取得
$query = "
    SELECT 
        rt.id AS ticket_id,
        r.reservation_date,
        af.type AS ticket_type,
        rt.qr_code
    FROM 
        reservations r
    JOIN 
        reservation_tickets rt ON r.id = rt.reservation_id
    JOIN 
        admission_fee_types af ON rt.admission_fee_type_id = af.id
    WHERE 
        r.user_id = :user_id
        AND r.status = 'reserved'
        AND rt.recipient_email IS NULL
";

$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $user_id]);
$tickets = $stmt->fetchAll();

// 分配URLを生成
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_id'])) {
    $ticket_id = $_POST['ticket_id'];

    // トークン生成
    $token = bin2hex(random_bytes(16));  // 16バイトのランダムトークンを生成

    // トークンをreservation_ticketsテーブルに保存
    $updateQuery = "UPDATE reservation_tickets SET share_token = :token WHERE id = :ticket_id";
    $updateStmt = $pdo->prepare($updateQuery);
    $updateStmt->execute([
        'token' => $token,
        'ticket_id' => $ticket_id,
    ]);

    // 分配URLを生成
    $share_url = "http://localhost:3000/src/views/receive_ticket.php?token=" . urlencode($token);
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>チケット分配</title>
</head>
<body>
    <header>
        <?php require_once '../../public/header.php'; ?>
    </header>

    <h1>チケット分配</h1>
    
    <?php if (isset($share_url)): ?>
        <p>以下のURLを相手に送信してください：</p>
        <input type="text" value="<?= htmlspecialchars($share_url) ?>" readonly>
    <?php endif; ?>
    
    <form method="post">
        <label for="ticket_id">分配するチケットを選択:</label>
        <select name="ticket_id" required>
            <?php foreach ($tickets as $ticket): ?>
                <option value="<?= htmlspecialchars($ticket['ticket_id']) ?>">
                    <?= htmlspecialchars($ticket['ticket_id'] . ' - ' . $ticket['reservation_date'] . ' - ' . $ticket['ticket_type']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">分配URLを生成</button>
    </form>
</body>
</html>
