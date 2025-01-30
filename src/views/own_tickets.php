<?php
// src/views/own_tickets.php
session_start();
require_once '../../config/db_connect.php';

// ユーザーIDをセッションから取得（ログインユーザーに紐づくチケットを表示）
$user_id = $_SESSION['user_id'];  // ログイン情報に基づき、ユーザーIDをセッションから取得

// 所持しているチケットを取得するクエリ
$query = "
    SELECT 
        r.id AS reservation_id,
        r.reservation_date,
        r.reservation_timeslot,
        rt.id AS ticket_id,
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
";

$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $user_id]);
$tickets = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>所持チケット一覧</title>
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

<header>
    <?php require_once '../../public/header.php'; ?>
</header>

<h1>所持しているチケット</h1>

<a href="share_ticket.php"><button id="confirm-reservation">購入へ進む</button></a>

<?php if (count($tickets) > 0): ?>
    <?php foreach ($tickets as $ticket): ?>
        <div class="ticket-card">
            <p><strong>予約ID:</strong> <?= htmlspecialchars($ticket['reservation_id']) ?></p>
            <p><strong>チケットID:</strong> <?= htmlspecialchars($ticket['ticket_id']) ?></p>
            <p><strong>予約日:</strong> <?= htmlspecialchars($ticket['reservation_date']) ?></p>
            <p><strong>予約時間帯:</strong> <?= htmlspecialchars($ticket['reservation_timeslot']) ?></p>
            <p><strong>券種:</strong> <?= htmlspecialchars($ticket['ticket_type']) ?></p>
            <div>
                <img src="<?= htmlspecialchars($ticket['qr_code']) ?>" alt="QRコード">
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>所持しているチケットはありません。</p>
<?php endif; ?>

</body>
</html>
