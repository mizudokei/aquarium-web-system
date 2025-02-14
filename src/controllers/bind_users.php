<?php
require_once __DIR__ . '../../models/db_connect.php';

$user_id = $_SESSION['user_id'];
// チケットIDの取得（AJAXで渡されたもの）
$ticket_id = $_POST['ticket_id'];

// ユーザー情報をusersテーブルから取得
$query = "SELECT last_name, first_name, email FROM users WHERE id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch();

// チケット情報を更新
if ($user) {
    $query = "
        UPDATE reservation_tickets 
        SET 
            recipient_lastname = :lastname, 
            recipient_firstname = :firstname, 
            recipient_email = :email 
        WHERE 
            id = :ticket_id
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'lastname' => $user['last_name'],  // 正しいカラム名
        'firstname' => $user['first_name'],  // 正しいカラム名
        'email' => $user['email'],  // 正しいカラム名
        'ticket_id' => $ticket_id
    ]);

    header('Content-Type: text/plain');
    echo "success";
} else {
    echo "error";
}
exit;
?>
