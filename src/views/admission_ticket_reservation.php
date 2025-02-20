<?php
// db_connect.php でデータベース接続
include(__DIR__ . '../../models/db_connect.php');

// 営業日データを取得
$stmt = $pdo->prepare("SELECT date, working_hour_id FROM sales_days WHERE is_operational = 1");
$stmt->execute();
$sales_days = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 営業日を配列で返す（フロントエンド用）
$sales_days_array = array_map(function ($day) {
	$datetime = new DateTime($day['date'], new DateTimeZone('UTC'));
	return [
		'date' => $datetime->format('Y-m-d'),
		'working_hour_id' => $day['working_hour_id']
	];
}, $sales_days);

// 営業時間データを取得
$working_hours_stmt = $pdo->prepare("SELECT * FROM working_hours");
$working_hours_stmt->execute();
$working_hours = $working_hours_stmt->fetchAll(PDO::FETCH_ASSOC);

// チケット種類データを取得
$admission_fee_types_stmt = $pdo->prepare("SELECT * FROM admission_fee_types");
$admission_fee_types_stmt->execute();
$admission_fee_types = $admission_fee_types_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selectedDate'])) {
	$selectedDate = $_POST['selectedDate'];

	// JSON形式で返す
	echo json_encode(['selectedDate' => $selectedDate]);
}
?>

<body>
	<div class="step-container">
		<div class="step-indicator">
			<svg viewBox="0 0 36 36" class="progress-circle">
				<circle class="bg-circle" cx="18" cy="18" r="16"></circle>
				<circle class="progress-bar" cx="18" cy="18" r="16"></circle>
			</svg>
			<span class="step-text">
				<span class="current-step">1</span>
				<span class="all-steps">/4</span>
			</span>
		</div>
		<span class="step-title">券種情報の入力</span>
	</div>

	<!-- タブメニュー -->
	<div class="tab-menu">
		<div id="tab-date" class="tab active">日付選択</div>
		<div id="tab-time" class="tab disabled">時間選択</div>
		<div id="tab-ticket" class="tab disabled">チケット</div>
	</div>

	<!-- タブコンテンツ -->
	<div id="content-date" class="tab-content active">
		<h2 class="reservation-heading">来館日を選ぶ</h2>
		<p class="reservation-lead">日にちを選択すると来館時間の選択に移行します</p>
		<div id="root"></div> <!-- カレンダー表示 -->
	</div>

	<div id="content-time" class="tab-content">
		<h2 class="reservation-heading">来館時間を選ぶ</h2>
		<p class="reservation-lead">来館時間を選択すると券種・枚数の指定に移行します</p>
		<div id="time-slot-container"></div>
	</div>

	<div id="content-ticket" class="tab-content">
		<h2 class="reservation-heading">券種・枚数を指定する</h2>
		<p class="reservation-lead">指定後に「購入へ進む」ボタンを押してください</p>
		<div id="ticket-info">
			<div id="ticket-types-list"></div>
		</div>
	</div>

	<div class="summary-card">
		<div id="summary-card-datetime">
			<span id="selected-date">日付未選択</span>
			<span id="selected-time">時間未選択</span>
		</div>
		<div id="summary-card-tickets">
			<p>合計枚数　<span id="total-quantity">0</span>枚</p>
			<p>合計金額　¥<span id="total-price">0</span></p>
		</div>
		<hr>
		<a href="/?page=admission_ticket_confirm">
			<button id="confirm-reservation">購入へ進む</button>
		</a>
	</div>

	<script src="/dist/bundle.js"></script>
	<script type="module" src="/assets/js/reservation.js"></script>

	<script>
		const salesDays = <?php echo json_encode($sales_days_array); ?>;
		const workingHours = <?php echo json_encode($working_hours); ?>;
		const ticketTypes = <?php echo json_encode($admission_fee_types); ?>;
	</script>
</body>