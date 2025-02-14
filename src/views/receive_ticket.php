<?php
require_once __DIR__ . '../../models/db_connect.php';

// 分配URLのトークンを取得
$token = $_GET['token'] ?? '';
if (!$token) {
	die('無効なURLです。');
}

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
		rt.ticket_type,
		aft.type AS ticket_name  -- カラム名を 'type' に修正
	FROM 
		reservation_tickets rt
	JOIN 
		reservations r ON rt.reservation_id = r.id
	LEFT JOIN 
		admission_fee_types aft ON rt.admission_fee_type_id = aft.id
	WHERE 
		rt.share_token = :token
";
$stmt = $pdo->prepare($query);
$stmt->execute(['token' => $token]);
$ticket = $stmt->fetch();


error_log($token);
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

		// 更新成功後にリダイレクト
		header("Location: " . $_SERVER['REQUEST_URI']);
		exit;
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
	<div class="icon-heading-container">
		<h2 class="icon-heading">チケット受け取り</h2>
	</div>
	
	<?php if (isset($error)): ?>
		<p style="color: red;"> <?= htmlspecialchars($error) ?> </p>
	<?php endif; ?>
	
	<?php if (!$ticket['recipient_lastname'] && !$ticket['recipient_firstname'] && !$ticket['recipient_email']): ?>
		<form method="POST" class="form">
			<div class="name-group">
				<div class="form-group" style="flex: 1;">
					<input type="text" id="lastname" name="lastname" class="form-input" placeholder=" " required>
					<label for="lastname" class="form-label">姓</label>
				</div>
				<div class="form-group" style="flex: 1;">
					<input type="text" id="firstname" name="firstname" class="form-input" placeholder=" " required>
					<label for="firstname" class="form-label">名</label>
				</div>
			</div>
			<div class="form-group">
				<input type="email" id="email" name="email" class="form-input" placeholder=" " required>
				<label for="email" class="form-label">メールアドレス</label>
			</div>
			<button type="submit" class="submit-btn">チケットを受け取る</button>
		</form>

	<?php else: ?>
		<?php 
			$date = date_create($ticket['reservation_date']);
			$formatted_date = date_format($date, 'Y年m月d日') . '（' . mb_substr('日月火水木金土', (int)date_format($date, 'w'), 1) . '）';
		?>
		<div class="own-tickets-container">
			<a class="button button--1" onclick="openModal('<?= htmlspecialchars($ticket['id']) ?>', '<?= $formatted_date ?>', '<?= htmlspecialchars($ticket['reservation_timeslot']) ?>', '<?= htmlspecialchars($ticket['ticket_name']) ?>', '<?= urlencode(basename($ticket['qr_code'])) ?>')">
				<div class="ticket-left">
					<span class="ticket-datetime"><?= $formatted_date ?><?= htmlspecialchars($ticket['reservation_timeslot']) ?></span>
					<span class="ticket-type-text">
						<?= htmlspecialchars($ticket['ticket_name']) ?>
					</span>
					<span class="ticket-id"><?= htmlspecialchars($ticket['id']) ?></span>
				</div>
				<div class="ticket-right">
					<span class="ticket-materials"></span>
				</div>
				<div class="arrow"></div>
			</a>
	</div>
	<?php endif; ?>

	<div id="ticketModal" class="modal" style="display: none;">
		<div class="modal-content">
			<p><span id="modal-type"></span></p>
			<div id="modal-datetime">
				<p><span id="modal-date"></span></p>
				<p><span id="modal-timeslot"></span></p>
			</div>
			<p id="modal-ticket-id">ID：<span id="modal-ticket-id"></span></p>

			<img id="modal-qr" src="" alt="QRコード"><br>
			<button class="close-button" onclick="closeModal()"><img src="assets/icons/cross.svg" alt=""></button>
			<button class="save-button" onclick="saveModalAsImage()">QRコードを保存</button> <!-- 保存ボタン -->
		</div>
	</div>
</body>
</html>
