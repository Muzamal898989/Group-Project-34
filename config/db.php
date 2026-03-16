<?php
$host = 'localhost';
$dbname = 'cs2team34_db';
$username = 'root';
$password = '';

// PDO connection (used by user_dashboard.php)
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("PDO connection failed: " . $e->getMessage());
}

// mysqli connection (used by admin_login.php and other files)
$conn = mysqli_connect($host, $username, $password, $dbname);
if (!$conn) {
    die("mysqli connection failed: " . mysqli_connect_error());
}
?>