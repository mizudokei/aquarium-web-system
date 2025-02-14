<?php
require_once __DIR__ . '/../libs/phpqrcode/qrlib.php';
https://entry.hal.ac.jp/studentsregister
/**
 * QRコードを生成し、指定ディレクトリに保存する関数
 *
 * @param string $reservationId 予約ID
 * @param string $ticketId チケットID
 * @return string 生成されたQRコード画像の保存パス（Web相対パス）
 */
function generateQRCode($reservationId, $ticketId) {
    // 保存ディレクトリ
    $qrCodeDir = __DIR__ . '/../../storage/qr_codes/';
    if (!is_dir($qrCodeDir)) {
        mkdir($qrCodeDir, 0755, true); // ディレクトリが存在しない場合は作成
    }

    // データを結合してQRコード内容にする
    $data = $reservationId . '-' . $ticketId;

    // 一意のファイル名を生成
    $fileName = uniqid('qrcode_', true) . '.png';
    $filePath = $qrCodeDir . $fileName;

    // QRコードを生成して保存
    QRcode::png($data, $filePath, QR_ECLEVEL_L, 4, 2); // (データ, ファイルパス, 誤り訂正レベル, サイズ, マージン)
    $filePath = '/../../storage/qr_codes/' . $fileName;
    // Web相対パスを返す
    return $filePath; // 保存パスではなく、Webでアクセス可能な相対パスを返す
}
?>
