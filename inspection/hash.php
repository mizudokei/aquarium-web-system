<?php
// テストデータ用のパスワードをハッシュ化
$password = 'X7k9!Fg$T2';  // テスト用の平文パスワード
$hashed_password = password_hash($password, PASSWORD_DEFAULT);  // パスワードをハッシュ化

// ハッシュ化されたパスワードを確認する
echo "ハッシュ化されたパスワード: " . $hashed_password;
