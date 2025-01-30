<?php
// public/index.php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>架空水族館｜トップページ</title>
</head>
<body>
    <header>
        <?php
            require_once 'header.php'; // 共通ヘッダーを読み込む
        ?>
    </header>

    <main>
        <h1>ようこそ、架空水族館へ！</h1>
        <p>水族館の最新情報やサービスをご確認ください。</p>
        
        <div>
            <h2>eチケットサービス</h2>
            <a href="../src/views/ticket_overview.php">eチケット概要ページ</a>
            <a href="../src/views/own_tickets.php">所持チケット一覧ページ</a>
        </div>
    </main>
</body>
</html>
