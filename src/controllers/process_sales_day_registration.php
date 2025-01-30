<?php
require_once('../../config/db_connect.php');

// POSTデータを受け取る
$date = $_POST['date'];
$working_hour_id = $_POST['working_hour_id'];

// 日付を調整して保存（タイムゾーンをAsia/Tokyoに設定）
$date = new DateTime($date, new DateTimeZone('Asia/Tokyo'));
$date->setTime(0, 0); // 時刻部分を00:00に設定

// 正しく調整された日付を保存
$formatted_date = $date->format('Y-m-d');

// 営業日をDBに登録
$stmt = $pdo->prepare("INSERT INTO sales_days (date, working_hour_id) VALUES (?, ?)");
$stmt->execute([$formatted_date, $working_hour_id]);

// 登録した営業日のデータを返す
echo json_encode([
    'status' => 'success',
    'message' => '営業日が登録されました。',
    'date' => $formatted_date  // 登録した日付を返す
]);
?>
