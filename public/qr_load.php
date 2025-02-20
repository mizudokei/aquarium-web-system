<?php
$file = $_GET['file'] ?? '';
$filepath = __DIR__ . '/../storage/qr_codes/' . basename($file);

if (!file_exists($filepath) || pathinfo($filepath, PATHINFO_EXTENSION) !== 'png') {
    header("HTTP/1.0 404 Not Found");
    exit("File not found.");
}

header("Content-Type: image/png");
readfile($filepath);
?>