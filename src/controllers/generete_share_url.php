<?php
require_once __DIR__ . '../../models/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_id'])) {
	$ticket_id = $_POST['ticket_id'];

	// トークン生成
	$token = bin2hex(random_bytes(16));

	// トークンをDBに保存
	$updateQuery = "UPDATE reservation_tickets SET share_token = :token WHERE id = :ticket_id";
	$updateStmt = $pdo->prepare($updateQuery);
	$updateStmt->execute([
		'token' => $token,
		'ticket_id' => $ticket_id,
	]);

	// プレーンテキストでURLのみ出力
	header('Content-Type: text/plain');
	echo "https://192.168.3.10/?page=receive_ticket&token=" . urlencode($token);
	exit;
}
?>
