<?php
// src/controllers/logout.php
session_start();
session_unset();
session_destroy();
header('Location: /?page=home');
exit;
?>
