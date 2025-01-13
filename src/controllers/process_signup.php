<?php
session_start();
require_once '../../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // ユーザー情報の登録
    $query = "INSERT INTO users (email, password) VALUES (:email, :password)";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['email' => $email, 'password' => $password]);

    $_SESSION['message'] = '会員登録が完了しました。ログインしてください。';
    header('Location: ../../src/views/login.php');
    exit;
}
?>