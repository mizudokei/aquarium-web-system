<?php
session_start();
require_once '../../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lastName = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
    $firstName = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
    $birth = filter_input(INPUT_POST, 'birth', FILTER_SANITIZE_STRING); // 入力値をサニタイズ
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // ユーザー情報の登録
    $query = "INSERT INTO users (last_name, first_name, birth, email, password) 
              VALUES (:last_name, :first_name, :birth, :email, :password)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':last_name' => $lastName,
        ':first_name' => $firstName,
        ':birth' => $birth,
        ':email' => $email,
        ':password' => $password,
    ]);

    $_SESSION['message'] = '会員登録が完了しました。ログインしてください。';
    header('Location: ../../src/views/login.php');
    exit;
}
?>
