<?php
// sales_days.php

// データベース接続の設定や営業日取得ロジック
include(__DIR__ . '../../models/db_connect.php');

header('Content-Type: application/json');

// 営業日データを取得
$query = "SELECT date FROM sales_days WHERE is_operational = 1";
$stmt = $pdo->prepare($query);
$stmt->execute();
$salesDays = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 営業日の配列を返す
$days = array_map(function ($row) {
    return $row['date'];
}, $salesDays);

echo json_encode($days);
?>
