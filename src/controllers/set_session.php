<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // フロントエンドから送信されたデータをセッションに保存
    $_SESSION['selectedDate'] = $_POST['selectedDate'];
    $_SESSION['selectedTimeSlot'] = $_POST['selectedTimeSlot'];
    $_SESSION['ticketQuantities'] = json_decode($_POST['ticketQuantities'], true);

    echo "Session saved";
}
?>
