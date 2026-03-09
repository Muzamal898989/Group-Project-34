<?php
require_once 'config/db.php';

$id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM customers WHERE customer_id = ?");
$stmt->bind_param("i", $id);

$stmt->execute();

header("Location: admin_customers.php");
exit();