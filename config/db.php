<?php
// includes/db_connect.php - Unified database connection
// This file supports BOTH mysqli (teammate's code) and PDO (your code)

// Database credentials
/*$host = "localhost";
$user = "cs2team34";
$pass = "IJx3HLbYg1PvUpQbYerU0Y4eo";
$db = "cs2team34_db";
*/
$host = "localhost";
$user = "root";
$pass = "";  // empty password for XAMPP
$dbname = "dorm_diner";  // real local DB name

// MySQLi connection (for teammate's admin files)
$conn = new mysqli($host, $user, $pass, $dbname);

// Check mysqli connection
if($conn->connect_error){
    die("Database connection failed: " . $conn->connect_error);
}

// PDO connection (for your customer-facing files)
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("PDO connection failed: " . $e->getMessage());
}

// Now both $conn (mysqli) and $pdo are available in any file that includes this
?>