<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>架空水族館</title>
    <link rel="stylesheet" href="/public/assets/css/header.css">
    <script>
        function confirmLogout() {
            if (confirm('ログアウトしますか？')) {
                document.getElementById('logout-form').submit();
            }
        }
    </script>
</head>
<body>
    <header>
        <h1>
        <nav>
            <?php if (isset($_SESSION['user'])): ?>
                <p>ログイン中: <?php echo htmlspecialchars($_SESSION['user'], ENT_QUOTES, 'UTF-8'); ?></p>
                <form id="logout-form" action="/src/controllers/logout.php" method="POST" style="display: inline;">
                    <a href="#" onclick="confirmLogout()">ログアウト</a>
                </form>
            <?php else: ?>
                <a href="/src/views/signup.php">新規会員登録</a>
                <a href="/src/views/login.php">ログイン</a>
            <?php endif; ?>
        </nav>
    </header>
</body>
