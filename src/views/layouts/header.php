<?php
ob_start(); // 出力バッファリングを開始

// 現在のURIを取得
$request_uri = $_SERVER['REQUEST_URI'];

// home.php 以外の場合、ヘッダーにスタイルを追加
if (strpos($request_uri, '/') === false) {
    echo '<style>
        header {
            background-color: #fdfdfd !important;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1) !important;
        }
    </style>';
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
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
        <a href="/?page=home"><img src="assets/images/header_logo_after.png" alt="" id="header-logo"></a>
        <div id="hamburger-menu">
            <div class="burger burger-squeeze" onclick="this.classList.toggle('open')">
                <div class="burger-lines"></div>
            </div>
        </div>
    </header>

    <div id="menu-overlay"></div>
    <nav class="menu">
        <div id="menu-btn-container">
            <a href="/?page=admission_ticket_reservation" class="btn-primary menu-btn">チケットご購入のご案内</a>
            <a href="/?page=own_tickets" class="btn-primary menu-btn">所持チケット</a>
        </div>
        <div id="menu-container">
            <div class="lg-flex md-justify-between">
                <div class="grid">
                    <div>
                        <p class="footer__navi-heading">水族館について</p>
                        <ul class="footer__navi">
                            <li><a href="/?page=login">蒼海水族館とは</a></li> <!-- 仮のログイン遷移 -->
                            <li><a href="#">営業時間・料金</a></li>
                            <li><a href="#">アクセスマップ</a></li>
                            <li><a href="#">ショップガイド</a></li>
                            <li><a href="#">WEBチケット</a></li>
                            <li><a href="#">研究/教育</a></li>
                        </ul>
                    </div>
                    <div>
                        <p class="footer__navi-heading">展示エリア</p>
                        <ul class="footer__navi">
                            <li><a href="#">大海の世界</a></li>
                            <li><a href="#">極寒の地</a></li>
                            <li><a href="#">深海の淵</a></li>
                            <li><a href="#">幻想のクラゲドーム</a></li>
                            <li><a href="#">サンゴ礁の楽園</a></li>
                            <li><a href="#">ふれあいタッチプール</a></li>
                        </ul>
                    </div>
                    <div>
                        <p class="footer__navi-heading">体験/プログラム</p>
                        <ul class="footer__navi">
                            <li><a href="#">イルカショー</a></li>
                            <li><a href="#">ペンギンのゴハンタイム</a></li>
                            <li><a href="#">アクアアカデミー</a></li>
                            <li><a href="#">ナイトツアー</a></li>
                            <li><a href="#">バックヤードツアー</a></li>
                        </ul>
                    </div>
                    <div>
                        <p class="footer__navi-heading">モデルコース</p>
                        <ul class="footer__navi">
                            <li><a href="#">ひとりで</a></li>
                            <li><a href="#">デートで</a></li>
                            <li><a href="#">家族で</a></li>
                        </ul>
                    </div>
                    <div>
                        <p class="footer__navi-heading">企業情報</p>
                        <ul class="footer__navi">
                            <li><a href="#">会社概要</a></li>
                            <li><a href="#">お問い合わせ</a></li>
                            <li><a href="#">サイトマップ</a></li>
                            <li><a href="#">プライバシーポリシー</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <hr />
            <div>
                <a href="#" class="footer__logo">
                    <img id="footer__logo__img" src="assets/images/logo_white.png" alt="Logo" />
                </a>
                <address class="footer__address">
                    〒530-0001 大阪府大阪市北区梅田３丁目３−１<br />
                    TEL：<a href="tel:">06-6347-0001</a> / FAX：0120-123-456<br />
                </address>
            </div>
        </div>
    </nav>

</body>

<?php
ob_end_flush(); // バッファの内容を出力
?>
