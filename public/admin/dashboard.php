<?php
// public/admin/dashboard.php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者ダッシュボード</title>
</head>
<body>
    <h1>管理者ダッシュボード</h1>
    <p>ようこそ、<?php echo htmlspecialchars($_SESSION['admin']); ?>さん。</p>
    <a href="../../src/controllers/admin_logout.php">ログアウト</a>

    <a href="../views/sales_day_registration.php">営業日登録ページ</a>

</body>
</html>
