<?php
// public/index.php
session_start();

// `page` パラメータを取得（デフォルトは home）
$page = $_GET['page'] ?? 'home';
$process = $_GET['process'] ?? '';

// 許可されたページリスト
$allowed_pages = [
	'home',
	'ticket_overview',
	'own_tickets',
	'login',
	'signup',
	'admission_ticket_confirm',
	'admission_ticket_reservation',
	'finalize_reservation',
	'receive_ticket',
	'share_ticket',
    'scan_qr'
];

$allowed_processes = [
	'process_login',
	'process_signup',
	'logout',
	'sales_days',
	'set_session',
	'process_admission_ticket_reservation',
	'qr_code_generator',
	'generete_share_url',
	'bind_users',
    'verify_qr'
];

if (!in_array($page, $allowed_pages)) {
	$page = 'home';
}

if (!in_array($process, $allowed_processes)) {
	$process = '';  // 無効な場合は処理しない
}

$view_path = __DIR__ . "/../src/views/{$page}.php";
$controller_path = __DIR__ . "/../src/controllers/{$process}.php";

// ファイルが存在しない場合は 404 エラーメッセージを表示
if (!file_exists($view_path)) {
	die("Error: The requested page '{$page}' was not found. Expected path: {$view_path}");
}

// コントローラーが存在する場合に実行
if ($process && file_exists($controller_path)) {
	require_once $controller_path;
}

// ページ名を日本語にマッピング
$page_titles = [
	'home' => 'トップページ',
	'ticket_overview' => 'eチケット概要',
	'own_tickets' => '所持チケット一覧',
	'login' => 'ログイン',
	'signup' => '会員登録',
	'admission_ticket_reservation' => '入場チケット予約',
	'admission_ticket_confirm' => '入場チケット予約内容確認',
	'finalize_reservation' => '入場チケット予約完了',
	'share_ticket' => 'チケット分配',
	'receive_ticket' => 'チケット受取',
    'scan_qr' => 'QRコードスキャン'
];

$title = $page_titles[$page];
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>架空水族館｜<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v5.15.3/css/all.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/burger_squeeze.css">
    <link rel="stylesheet" href="assets/css/destyle.css">
    <link rel="stylesheet" href="assets/css/common.css">
    <link rel="stylesheet" href="assets/css/text.css">
    <link rel="stylesheet" href="assets/css/hamburger_menu.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/home.css">
    <link rel="stylesheet" href="assets/css/tab.css">
    <link rel="stylesheet" href="assets/css/content_separator_wave.css">
    <link rel="stylesheet" href="assets/css/admission_ticket_reservation.css">
    <link rel="stylesheet" href="assets/css/admission_ticket_confirm.css">
    <link rel="stylesheet" href="assets/css/summary_card.css">
    <link rel="stylesheet" href="assets/css/button.css">
    <link rel="stylesheet" href="assets/css/step_indicator.css">
    <link rel="stylesheet" href="assets/css/ticket.css">
    <link rel="stylesheet" href="assets/css/ticket_modal.css">
    <link rel="stylesheet" href="assets/css/share_modal.css">
    <link rel="stylesheet" href="assets/css/receive_ticket.css">
    <link rel="stylesheet" href="assets/css/slick.css">
    <link rel="stylesheet" href="assets/css/slick-theme.css">
    <link rel="stylesheet" href="assets/css/wave.css">
    <link rel="stylesheet" href="assets/css/blob.css">
    <link rel="stylesheet" href="assets/css/scan_qr.css">
    <link rel="stylesheet" href="assets/css/finalize_reservation.css">

</head>

<body>

    <header>
        <?php require_once '../src/views/layouts/header.php'; ?>
    </header>

    <main>
        <?php require_once $view_path; ?>
    </main>

    <footer>
        <?php require_once '../src/views/layouts/footer.php'; ?>
    </footer>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.0.0-rc.5/dist/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="assets/js/jsQR.js"></script>
    <script src="assets/js/header.js"></script>
    <script src="assets/js/banner.js"></script>
    <script src="assets/js/tab.js"></script>
    <script src="assets/js/blobAnimation.js"></script>
    <script src="assets/js/ownTicket.js"></script>
    <script src="assets/js/slick.min.js"></script>
    <script src="assets/js/slider.js"></script>
    <script src="assets/js/pagetop.js"></script>
</body>

</html>