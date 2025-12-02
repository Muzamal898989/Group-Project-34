<?php
include __DIR__ . '/db_connect.php';

$result =$conn->query("SELECT COUNT(*) AS meal_count FROM meals");
$row = $result->fetch_assoc();

echo "connected successfully! There are " . $row['meal_count'] . "meals in the database";
?>