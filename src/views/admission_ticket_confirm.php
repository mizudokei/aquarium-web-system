<?php
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
?>

<body>
	<div class="step-container">
		<div class="step-indicator">
			<svg viewBox="0 0 36 36" class="progress-circle">
				<circle class="bg-circle" cx="18" cy="18" r="16"></circle>
				<circle class="progress-bar" cx="18" cy="18" r="16" id="step-three"></circle>
			</svg>
			<span class="step-text">
				<span class="current-step">3</span>
				<span class="all-steps">/4</span>
			</span>
		</div>
		<span class="step-title">購入内容の確認</span>
	</div>

	<div id="order-summary">
		<div class="order-div">
			<h3 class="order-info">来館予定日</h3>
			<p class="order-detail"><?php echo htmlspecialchars($selectedDate); ?></p>
		</div>

		<div class="order-div">
			<h3 class="order-info">来館予定時間</h3>
			<p class="order-detail"><?php echo htmlspecialchars($selectedTimeSlot); ?></p>
		</div>

		<div class="order-div">
			<h3 class="order-info">購入詳細</h3>
			<table id="ticket-table">
				<tbody>
					<?php foreach ($ticketQuantities as $ticket) : ?>
						<tr>
							<td id="ticket-type"><?php echo htmlspecialchars($ticket['ticketType']); ?></td>
							<td id="ticket-price">¥<?php echo number_format(htmlspecialchars($ticket['ticketPrice'])); ?></td>
							<td id="ticket-quantity"><?php echo htmlspecialchars($ticket['quantity']); ?>枚</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		<div class="order-div">
			<h3 class="order-info">合計枚数</h3>
			<p class="order-detail"><?php echo $totalQuantity; ?>枚</p>
		</div>

		<div class="order-div">
			<h3 class="order-info">合計金額</h3>
			<p class="order-detail">¥<?php echo number_format($totalPrice); ?></p>
		</div>
	</div>

	<form action="/?process=process_admission_ticket_reservation" method="POST">
		<button type="submit" id="confirm-button" class="btn-primary btn-gra">予約を確定する</button>
	</form>
</body>