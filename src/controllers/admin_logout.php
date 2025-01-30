<?php
// src/controllers/admin_logout.php
session_start();
session_unset();
session_destroy();
header('Location: ../../public/admin/login.php');
exit;
?>
