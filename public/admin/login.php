<?php
// public/admin/login.php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>架空水族館｜管理者ログイン</title>
</head>
<body>

    <header>
        <?php
            // require_once '../../public/header.php';
        ?>
    </header>

    <h1>管理者ログインフォーム</h1>
    <form action="../../src/controllers/admin_process_login.php" method="POST">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        <button type="submit">ログイン</button>
    </form>
</body>
</html>
