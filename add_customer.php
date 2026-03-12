<?php
require_once 'config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

$first_name = $_POST['first_name'];
$surname = $_POST['surname'];
$email = $_POST['email'];
$date_of_birth = $_POST['date_of_birth'];
$sms_opt_in = isset($_POST['sms_opt_in']) ? 1 : 0;
$email_opt_in = isset($_POST['email_opt_in']) ? 1 : 0;

$stmt = $conn->prepare("INSERT INTO customers 
(first_name, surname, email, date_of_birth, sms_opt_in, email_opt_in)
VALUES (?, ?, ?, ?, ?, ?)");

$stmt->bind_param("ssssii", $first_name, $surname, $email, $date_of_birth, $sms_opt_in, $email_opt_in);

$stmt->execute();

header("Location: admin_customers.php");
exit();
}
?>
<head><link rel="stylesheet" href="css/admin.css"></head>
<h2>Add Customer</h2>

<form method="POST">

First Name:<br>
<input type="text" name="first_name" required><br><br>

Surname:<br>
<input type="text" name="surname" required><br><br>

Email:<br>
<input type="email" name="email" required><br><br>

Date of Birth:<br>
<input type="date" name="date_of_birth"><br><br>

SMS Notifications:
<input type="checkbox" name="sms_opt_in"><br><br>

Email Notifications:
<input type="checkbox" name="email_opt_in"><br><br>

<button type="submit">Add Customer</button>

</form>