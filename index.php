<?php
header("Location: Home.html");
session_start();

$isAdminViewingCustomer = 
    isset($_SESSION['admin_logged_in']) &&
    isset($_SESSION['view_mode']) &&
    $_SESSION['view_mode'] === 'customer';
exit;
?>
