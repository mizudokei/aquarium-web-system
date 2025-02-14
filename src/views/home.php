<?php
// データベース接続
require_once __DIR__ . '../../models/db_connect.php';

// 今日の日付を取得
$today = date('Y-m-d');

// 今日の営業日情報を取得
$sql = "SELECT is_operational, working_hour_id FROM sales_days WHERE date = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$today]);
$sales_day = $stmt->fetch(PDO::FETCH_ASSOC);

$is_open = false;
$opening_time = null;
$closing_time = null;

if ($sales_day && $sales_day['is_operational'] == 1) {
    $is_open = true;

    // 営業時間を取得
    $sql = "SELECT start_time, end_time FROM working_hours WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$sales_day['working_hour_id']]);
    $working_hours = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($working_hours) {
        $opening_time = date('H:i', strtotime($working_hours['start_time']));
        $closing_time = date('H:i', strtotime($working_hours['end_time']));
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <div id="ticket-banner-container">
        <a href="/?page=admission_ticket_reservation"><img id="ticket-banner" src="assets/images/ticket_banner.png" alt=""></a>
        <button id="close-banner">✖</button>
    </div>

    <div class="video-container">
        <video autoplay loop muted>
            <source src="assets/videos/sample.mp4" type="video/mp4">
        </video>

        <img src="assets/images/logo.png" alt="ロゴ" id="logo-square">

        <a class="scroll" href="#introduction-container">
            <span></span>
        </a>
    </div>

    <div id="nav-info-container">
        <ul>
            <li>
                <img class="li-icon" src="assets/icons/logo.png" alt="">
                <p class="li-icon-heading">蒼海水族館<br>とは</p>
            </li>
            <li>
                <img class="li-icon" src="assets/icons/yen.svg" alt="">
                <p class="li-icon-heading">営業時間<br>料金</p>
            </li>
            <li>
                <img class="li-icon" src="assets/icons/access.svg" alt="">
                <p class="li-icon-heading">アクセス<br>マップ</p>
            </li>
            <li>
                <img class="li-icon" src="assets/icons/tenji.svg" alt="">
                <p class="li-icon-heading">展示<br>エリア</p>
            </li>
            <li>
                <img class="li-icon" src="assets/icons/tickets_color.svg" alt="">
                <p class="li-icon-heading">WEBチケット<br>購入</p>
            </li>
            <li>
                <img class="li-icon" src="assets/icons/event.svg" alt="">
                <p class="li-icon-heading">体験<br>プログラム</p>
            </li>
            <li>
                <img class="li-icon" src="assets/icons/shop.svg" alt="">
                <p class="li-icon-heading">ショップ<br>ガイド</p>
            </li>
            <li>
                <img class="li-icon" src="assets/icons/research.svg" alt="">
                <p class="li-icon-heading">研究<br>教育</p>
            </li>
        </ul>
    </div>

    <div id="time-info-container">
        <div id="sales-info-container">
            <div class="icon-heading-container">
                <h2 class="icon-heading" id="sales-info-heading">本日の営業時間</h2>
            </div>
            <p>
                <?php if ($is_open): ?>
            <div id="active-time">
                <p id="sales-time"><?php echo $opening_time . " ～ " . $closing_time; ?></p>
                <p id="sales-time-lead">入館は閉館時間の1時間前まで</p>
            </div>
        <?php else: ?>
            本日は休業日です。
        <?php endif; ?>
        </p>
        <a href="#" class="sales-time-event-btn btn-primary btn-gra">本日のイベント</a>
        </div>
    </div>

    <div id="pickup-container">
        <div class="heading">
            <h1 class="container-heading text-gra">PICK UP !</h1>
            <h2 class="container-heading-sub text-gra">おすすめ情報</h2>
        </div>
        <ul class="slide">
            <li class="slider-img"><img src="assets/images/banner.png"></li>
            <li class="slider-img"><img src="assets/images/banner.png"></li>
            <li class="slider-img"><img src="assets/images/banner.png"></li>
            <li class="slider-img"><img src="assets/images/banner.png"></li>
            <li class="slider-img"><img src="assets/images/banner.png"></li>
        </ul>
    </div>

    <div id="news-container">
        <div class="container-box">
            <div class="heading">
                <h1 id="news-container-heading" class="container-heading text-gra">NEWS</h1>
                <h2 class="container-heading-sub text-gra">ニュース</h2>
            </div>
            <ul class="news-list">
                <li class="item">
                    <a href="#">
                        <p class="date">2020/4/15</p>
                        <p class="category"><span>お知らせ</span></p>
                        <p class="title">ここにお知らせが入りますここにお知らせが入りますここにお知らせが入ります</p>
                    </a>
                </li>
                <li class="item">
                    <a href="#">
                        <p class="date">2020/4/15</p>
                        <p class="category"><span>お知らせ</span></p>
                        <p class="title">ここにお知らせが入りますここにお知らせが入りますここにお知らせが入ります</p>
                    </a>
                </li>
                <li class="item">
                    <a href="#">
                        <p class="date">2020/4/15</p>
                        <p class="category"><span>お知らせ</span></p>
                        <p class="title">ここにお知らせが入りますここにお知らせが入りますここにお知らせが入ります</p>
                    </a>
                </li>
            </ul>
            <a class="news-transition-btn btn-primary btn-gra">お知らせ一覧</a>
        </div>
    </div>

    <div id="model-course-container">
        <div class="heading">
            <h1 id="news-container-heading" class="container-heading text-gra">MODEL COURSE</h1>
            <h2 class="container-heading-sub text-gra">モデルコース</h2>
        </div>
        <ul>
            <li>
                <a id="alone" class="model-course-transition" href="#">
                    <p>
                        <span>ひとりで</span>
                    </p>
                </a>
            </li>
            <li>
                <a id="couple" class="model-course-transition" href="#">
                    <p>
                        <span>デートで</span>
                    </p>
                </a>
            </li>
            <li>
                <a id="family" class="model-course-transition" href="#">
                    <p>
                        <span>家族で</span>
                    </p>
                </a>
            </li>
        </ul>
    </div>

    <div id="facility-info-container">
        <div class="heading">
            <h1 id="news-container-heading" class="container-heading text-gra">EXHIBITION AREA</h1>
            <h2 class="container-heading-sub text-gra">展示エリア</h2>
        </div>
        <ul>
            <li>
                <a id="facility-info-bg-01" class="facility-info-transition" href="#">
                    <div class="facility-info-description">
                        <h2>大海の世界</h2>
                        <p>巨大なパノラマ水槽で、エイやカラフルな熱帯魚が優雅に泳ぐ姿を間近で観察できます。</p>
                    </div>
                </a>
            </li>
            <li>
                <a id="facility-info-bg-02" class="facility-info-transition" href="#">
                    <div class="facility-info-description">
                        <h2>極寒の地</h2>
                        <p>南極・北極の海を再現。<br>ペンギンの泳ぐ姿やアザラシの独特な動きを楽しめます。</p>
                    </div>
                </a>
            </li>
            <li>
                <a id="facility-info-bg-03" class="facility-info-transition" href="#">
                    <div class="facility-info-description">
                        <h2>深海の淵</h2>
                        <p>光の届かない深海の世界を再現。<br>ダイオウイカの模型やグソクムシなど、不思議な生き物が勢ぞろい！</p>
                    </div>
                </a>
            </li>
            <li>
                <a id="facility-info-bg-04" class="facility-info-transition" href="#">
                    <div class="facility-info-description">
                        <h2>幻想のクラゲドーム</h2>
                        <p>光と共にゆらめくクラゲの神秘的な世界<br>を楽しもう。</p>
                    </div>
                </a>
            </li>
            <li>
                <a id="facility-info-bg-05" class="facility-info-transition" href="#">
                    <div class="facility-info-description">
                        <h2>サンゴ礁の楽園</h2>
                        <p>色鮮やかなサンゴと共生するクマノミ、チョウチョウウオ、ウミガメたちが暮らすエリア。</p>
                    </div>
                </a>
            </li>
            <li>
                <a id="facility-info-bg-06" class="facility-info-transition" href="#">
                    <div class="facility-info-description">
                        <h2>ふれあいタッチプール</h2>
                        <p>ヒトデやウミガメなどに触れられる体験型エリア。小さなお子様にも大人気！</p>
                    </div>
                </a>
            </li>
        </ul>
    </div>
</body>

</html>