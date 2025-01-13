<?php
// public/register.php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>架空水族館｜新規会員登録ページ</title>
</head>
<body>
    <h1>新規会員登録フォーム</h1>
    <form action="../../src/controllers/process_signup.php" method="POST">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        <button type="submit">登録</button>
    </form>
    <a href="../../public/index.php">ホームへ</a>
</body>
</html>
