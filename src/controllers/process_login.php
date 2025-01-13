<?php
// src/controllers/process_login.php
session_start();
require_once '../../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // ユーザー情報の認証
    $query = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user['email'];
        $_SESSION['message'] = 'Login successful!';
        header('Location: ../../public/index.php');
        exit;
    }

    $_SESSION['message'] = 'Invalid email or password.';
    header('Location: ../../src/views/login.php');
    exit;
}
?>