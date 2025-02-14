<?php
// src/controllers/process_login.php
require_once __DIR__ . '../../models/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // ユーザー情報の認証
    $query = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // セッションにユーザーのメールアドレスとIDを格納
        $_SESSION['user'] = $user['email'];
        $_SESSION['user_id'] = $user['id'];  // IDもセッションに格納
        $_SESSION['user_birth'] = $user['birth'];  // IDもセッションに格納
        $_SESSION['message'] = 'Login successful!';
        header('Location: /?page=home');  // トップページにリダイレクト
        exit;
    }

    $_SESSION['message'] = 'Invalid email or password.';
    header('Location: /?page=login');
    
    exit;
}
?>
