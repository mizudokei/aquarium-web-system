<?php
// db_connect.php でデータベース接続
include('../../config/db_connect.php');
session_start();

// 営業日データを取得
$stmt = $pdo->prepare("SELECT date, working_hour_id FROM sales_days WHERE is_operational = 1");
$stmt->execute();
$sales_days = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 営業日を配列で返す（フロントエンド用）
$sales_days_array = array_map(function($day) {
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

    // ログに確認用メッセージを出力
    error_log("選択された日付: " . print_r($selectedDate, true));

    // JSON形式で返す
    echo json_encode(['selectedDate' => $selectedDate]);
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>架空水族館｜入場eチケット予約</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link rel="stylesheet" href="../../public/assets/css/admission_ticket_reservation.css">
</head>
<body>

    <header>
        <?php
            require_once '../../public/header.php'; // 共通ヘッダーを読み込む
        ?>
    </header>

    <main>
        <!-- カレンダーUI表示 -->
        <div id="root"></div>

        <!-- タイムスロットUI表示 -->
        <div id="selected-date-info"style="display:none;">
            <select id="time-slot"></select>
        </div>

        <!-- 券種・枚数指定UI表示 -->
        <div id="ticket-info" style="display:none;">
            <table class="ticket-table">
                <thead>
                    <tr>
                        <th>券種</th>
                        <th>価格</th>
                        <th>枚数</th>
                    </tr>
                </thead>
                <tbody id="ticket-types-list">
                    <!-- チケットの種類テーブルがここに動的に挿入される -->
                </tbody>
            </table>
        </div>

        <div class="summary_card">
            <!-- 選択された日 -->
            <span id="selected-date"></span>
            <!-- 選択された時間帯 -->
            <span id="selected-time"></span>
            <p>合計枚数: <span id="total-quantity">0</span></p>
            <p>合計金額: ¥<span id="total-price">0</span></p>
            <a href="admission_ticket_confirm.php"><button id="confirm-reservation">購入へ進む</button></a>
        </div>
    </main>

    <script src="/dist/bundle.js"></script>
    <script type="module" src="/public/assets/js/reservation.js"></script>

    <script>
        const salesDays = <?php echo json_encode($sales_days_array); ?>;
        const workingHours = <?php echo json_encode($working_hours); ?>;
        const ticketTypes = <?php echo json_encode($admission_fee_types); ?>;
    </script>
</body>
</html>
