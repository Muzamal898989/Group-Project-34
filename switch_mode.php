<?php
session_start();

//only allow when admin is logged in
if(!isset($_SESSION['admin_logged_in'])){
    header("location: admin_login.php");
    exit();
}

if($_POST['mode'] == 'customer'){
   $_SESSION['view_mode'] = 'customer';
    header("location: index.php");//homepage?
    exit();
}


/*session_start();

$isAdminViewingCustomer = 
    isset($_SESSION['admin_logged_in']) &&
    isset($_SESSION['view_mode']) &&
    $_SESSION['view_mode'] === 'customer';
*/