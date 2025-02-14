<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>架空水族館｜eチケット概要</title>
</head>
<body>
    <main>
        <h1>eチケット概要ページ</h1>
        <p>ご希望のチケットを選択してください。</p>

        <?php if (!isset($_SESSION['user'])): ?>
            <p style="color: red;">eチケットの購入には新規会員登録・ログインが必要です。</p>
        <?php endif; ?>
        
        <div>
            <h2>入館用チケット</h2>
            <?php if (isset($_SESSION['user'])): ?>
                <!-- ログイン済みで遷移可能 -->
                <a href="/?page=admission_ticket_reservation">入館用チケット予約</a>
            <?php else: ?>
                <!-- ログインしていない場合はグレーアウト -->
                <a href="#" style="color: gray; pointer-events: none; text-decoration: none;">入館用チケット予約</a>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
