<?php
require_once __DIR__ . '../../models/db_connect.php';

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
		rt.qr_code,
		rt.used_at,
		rt.recipient_lastname,
		rt.recipient_firstname,
		rt.recipient_email,
		rt.share_token
	FROM 
		reservations r
	JOIN 
		reservation_tickets rt ON r.id = rt.reservation_id
	JOIN 
		admission_fee_types af ON rt.admission_fee_type_id = af.id
	WHERE 
		r.user_id = :user_id
		AND r.status = 'reserved'
		AND rt.share_token IS NULL
		AND rt.used_at IS NULL
";

$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $user_id]);
$tickets = $stmt->fetchAll();
?>

<head>
	<style>
		.disabled {
			opacity: 0.5;
			pointer-events: none;
		}

		.selected {
			border: 2px solid #2730d9;
		}
	</style>
</head>

<body translate="no">
	<div class="icon-heading-container">
		<h2 class="icon-heading">所持チケット</h2>
	</div>

	<div class="btn-container">
		<button class="btn-flat-blue bind-users-ticket" onclick="openBindUsersModal()">自分が利用する</button>
		<button class="btn-flat-blue share-ticket" onclick="openShareModal()">同伴者に分配</button>
	</div>

	<div class="own-tickets-container">
		<?php if (count($tickets) > 0): ?>
			<?php foreach ($tickets as $ticket): ?>
				<?php
				$date = date_create($ticket['reservation_date']);
				$formatted_date = date_format($date, 'Y年m月d日') . '（' . mb_substr('日月火水木金土', (int)date_format($date, 'w'), 1) . '）';
				// チケットが紐づけられているかどうかを確認
				$isBound = !is_null($ticket['recipient_lastname']) && !is_null($ticket['recipient_firstname']) && !is_null($ticket['recipient_email']);
				?>
				<a class="button button--1" onclick="openModal(
	'<?= htmlspecialchars($ticket['ticket_id']) ?>', 
	'<?= $formatted_date ?>', 
	'<?= htmlspecialchars($ticket['reservation_timeslot']) ?>', 
	'<?= htmlspecialchars($ticket['ticket_type']) ?>', 
	'<?= htmlspecialchars($ticket['qr_code']) ?>', 
	'<?= isset($ticket['recipient_lastname']) ? htmlspecialchars($ticket['recipient_lastname']) : '' ?>', 
	'<?= isset($ticket['recipient_firstname']) ? htmlspecialchars($ticket['recipient_firstname']) : '' ?>'
)">

					<div class="ticket-left">
						<span class="ticket-datetime"><?= $formatted_date ?><?= htmlspecialchars($ticket['reservation_timeslot']) ?></span>
						<span class="ticket-type-text"> <?= htmlspecialchars($ticket['ticket_type']) ?></span>
						<span class="ticket-id"><?= htmlspecialchars($ticket['ticket_id']) ?></span>
					</div>
					<div class="ticket-right">
						<?php if ($isBound): ?>
							<span class="ticket-materials">MINE</span>
						<?php endif; ?>
					</div>
					<div class="arrow"></div>
				</a>
			<?php endforeach; ?>
		<?php else: ?>
			<p>所持しているチケットはありません。</p>
		<?php endif; ?>
	</div>

	<div id="ticketModal" class="modal" style="display: none;">
		<div class="modal-content">
			<p><span id="modal-type"></span></p>
			<div id="modal-datetime">
				<p><span id="modal-date"></span></p>
				<p><span id="modal-timeslot"></span></p>
			</div>
			<p><span id="modal-recipient"></span></p>
			<p id="modal-ticket-id">ID：<span id="modal-ticket-id"></span></p>
			<img id="modal-qr" src="" alt="QRコード"><br>
			<button class="close-button" onclick="closeModal()"><img src="assets/icons/cross.svg" alt=""></button>
			<button class="save-button" onclick="saveModalAsImage()">QRコードを保存</button> <!-- 保存ボタン -->
		</div>
	</div>

	<div id="shareModal" class="modal" style="display: none;">
		<div class="modal-content">
			<h2>チケット分配</h2>
			<p>分配するチケットを選択してください。</p>
			<div id="ticketList">
				<?php if (count($tickets) > 0): ?>
					<?php foreach ($tickets as $ticket): ?>
						<?php
						$date = date_create($ticket['reservation_date']);
						$formatted_date = date_format($date, 'Y年m月d日') . '（' . mb_substr('日月火水木金土', (int)date_format($date, 'w'), 1) . '）';
						$isBound = !is_null($ticket['recipient_lastname']) && !is_null($ticket['recipient_firstname']) && !is_null($ticket['recipient_email']);
						if ($isBound) {
							continue;  // ユーザー情報が紐づいている場合はスキップ
						}
						?>
						<div class="select-ticket-card" onclick="selectTicket('<?= htmlspecialchars($ticket['ticket_id']) ?>', this)">
							<span class="select-ticket-card-datetime"><?= $formatted_date ?><?= htmlspecialchars($ticket['reservation_timeslot']) ?></span>
							<span class="select-ticket-card-type"><?= htmlspecialchars($ticket['ticket_type']) ?></span>
							<span class="select-ticket-card-id">ID: <?= htmlspecialchars($ticket['ticket_id']) ?></span>
						</div>
					<?php endforeach; ?>
				<?php else: ?>
					<p>分配可能なチケットはありません。</p>
				<?php endif; ?>
			</div>
			<button id="generateShareURL" class="disabled" onclick="generateShareURL()" disabled>分配URL発行</button>
			<div id="shareURL-container">
				<input type="text" id="shareURL" readonly class="disabled"
					placeholder="ここに分配URLが表示されます">
				<button onclick="copyToClipboard()" title="コピー" id="copy-icon" class="disabled">
					<i class="fas fa-copy fa-fw"></i>
				</button>
			</div>
			<button class="close-button" onclick="closeShareModal()"><img src="assets/icons/cross.svg" alt=""></button>
		</div>
	</div>


	<div id="bindUsersModal" class="modal" style="display: none;">
		<div class="modal-content">
			<h2>ユーザー情報の紐づけ</h2>
			<p>自分用のチケットを選択してください。</p>
			<div id="ticketList">
				<?php if (count($tickets) > 0): ?>
					<?php foreach ($tickets as $ticket): ?>
						<?php
						$date = date_create($ticket['reservation_date']);
						$formatted_date = date_format($date, 'Y年m月d日') . '（' . mb_substr('日月火水木金土', (int)date_format($date, 'w'), 1) . '）';
						?>
						<div class="select-ticket-card" onclick="selectTicketUserBind('<?= htmlspecialchars($ticket['ticket_id']) ?>', this)">
							<span class="select-ticket-card-datetime"><?= $formatted_date ?><?= htmlspecialchars($ticket['reservation_timeslot']) ?></span>
							<span class="select-ticket-card-type"><?= htmlspecialchars($ticket['ticket_type']) ?></span>
							<span class="select-ticket-card-id">ID: <?= htmlspecialchars($ticket['ticket_id']) ?></span>
						</div>
					<?php endforeach; ?>
				<?php else: ?>
					<p>指定可能なチケットはありません。</p>
				<?php endif; ?>
			</div>
			<button id="bindUsers" class="disabled" onclick="bindUserToTicket()" disabled>自分の情報と紐づけ</button>
			<button class="close-button" onclick="closeBindUsersModal()"><img src="assets/icons/cross.svg" alt=""></button>
		</div>
	</div>
</body>