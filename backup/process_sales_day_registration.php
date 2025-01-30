<?php
require_once('../../config/db_connect.php');

$date = $_POST['date'] ?? null;
$working_hour_id = $_POST['working_hour_id'] ?? null;

if (!$date || !$working_hour_id) {
    echo json_encode(['status' => 'error', 'message' => 'データが不足しています']);
    exit;
}

try {
    // SQL文の修正: プレースホルダを確認して一致させる
    $stmt = $pdo->prepare("
        INSERT INTO sales_days (date, is_operational, working_hour_id)
        VALUES (:date, 1, :working_hour_id)
        ON DUPLICATE KEY UPDATE
            is_operational = VALUES(is_operational),
            working_hour_id = VALUES(working_hour_id)
    ");

    // 正しいパラメータをバインドする
    $stmt->bindParam(':date', $date, PDO::PARAM_STR);
    $stmt->bindParam(':working_hour_id', $working_hour_id, PDO::PARAM_INT);

    // SQLを実行
    $stmt->execute();

    echo json_encode(['status' => 'success', 'message' => '登録成功']);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'エラー: ' . $e->getMessage() . ' (' . $e->getCode() . ')'
    ]);
    error_log($e->getMessage());
}
