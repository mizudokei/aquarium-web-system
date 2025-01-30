<?php
// src/controllers/admin_process_login.php
session_start();
require_once '../../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // 管理者情報の認証
    $query = "SELECT * FROM admins WHERE email = :email";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['email' => $email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin'] = $admin['email'];
        $_SESSION['message'] = 'Login successful!';
        header('Location: ../../public/admin/dashboard.php');  // 管理者ダッシュボード
        exit;
    }

    $_SESSION['message'] = 'Invalid email or password.';
    header('Location: ../../public/admin/login.php');  // ログインページにリダイレクト
    exit;
}
