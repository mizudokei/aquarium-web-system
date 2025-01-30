<?php
session_start();
require_once '../../config/db_connect.php';

// 分配URLのトークンを取得
$token = $_GET['token'] ?? '';
if (!$token) {
    die('無効なURLです。');
}

// トークンに対応するチケット情報を取得
$query = "
    SELECT 
        rt.id, 
        rt.reservation_id, 
        rt.qr_code, 
        rt.recipient_lastname, 
        rt.recipient_firstname, 
        rt.recipient_email, 
        r.reservation_date, 
        r.reservation_timeslot, 
        rt.ticket_type 
    FROM 
        reservation_tickets rt
    JOIN 
        reservations r ON rt.reservation_id = r.id
    WHERE 
        rt.share_token = :token
";
$stmt = $pdo->prepare($query);
$stmt->execute(['token' => $token]);
$ticket = $stmt->fetch();

if (!$ticket) {
    die('このチケットは既に受け取られたか、有効期限が切れています。');
}

// 受取処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lastname = trim($_POST['lastname'] ?? '');
    $firstname = trim($_POST['firstname'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (!$lastname || !$firstname || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = '入力内容が正しくありません。';
    } else {
        // チケットの受取人情報を更新
        $updateQuery = "UPDATE reservation_tickets SET recipient_lastname = :lastname, recipient_firstname = :firstname, recipient_email = :email WHERE id = :ticket_id";
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->execute([
            'lastname' => $lastname,
            'firstname' => $firstname,
            'email' => $email,
            'ticket_id' => $ticket['id'],
        ]);
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>受け取りチケット</title>
    <style>
        .ticket-card {
            border: 1px solid #ccc;
            padding: 15px;
            margin: 10px;
            width: 300px;
            box-sizing: border-box;
        }
        .ticket-card img {
            width: 100px;
        }
    </style>
</head>
<body>
    <h1>チケット受取</h1>
    
    <?php if (isset($error)): ?>
        <p style="color: red;"> <?= htmlspecialchars($error) ?> </p>
    <?php endif; ?>
    
    <?php if (!$ticket['recipient_lastname'] && !$ticket['recipient_firstname'] && !$ticket['recipient_email']): ?>
        <form method="POST">
            <label>姓: <input type="text" name="lastname" required></label><br>
            <label>名: <input type="text" name="firstname" required></label><br>
            <label>メールアドレス: <input type="email" name="email" required></label><br>
            <button type="submit">チケットを受け取る</button>
        </form>
    <?php else: ?>
        <div class="ticket-card">
            <p><strong>予約ID:</strong> <?= htmlspecialchars($ticket['reservation_id']) ?></p>
            <p><strong>チケットID:</strong> <?= htmlspecialchars($ticket['id']) ?></p>
            <p><strong>予約日:</strong> <?= htmlspecialchars($ticket['reservation_date']) ?></p>
            <p><strong>予約時間帯:</strong> <?= htmlspecialchars($ticket['reservation_timeslot']) ?></p>
            <p><strong>券種:</strong> <?= htmlspecialchars($ticket['ticket_type']) ?></p>
            <div>
                <img src="<?= htmlspecialchars($ticket['qr_code']) ?>" alt="QRコード">
            </div>
        </div>
    <?php endif; ?>
</body>
</html>
