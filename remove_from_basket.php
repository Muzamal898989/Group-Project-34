<?php
session_start();
if (isset($_POST['index'])) {
    $index = (int) $_POST['index'];
    if (isset($_SESSION['basket'][$index])) {
        unset($_SESSION['basket'][$index]);
        $_SESSION['basket'] = array_values($_SESSION['basket']); // reindex
    }
}
header("Location: basket.php");
exit;
