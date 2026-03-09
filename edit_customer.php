<?php
require_once 'config/db.php';

$id = $_GET['id'];

$result = $conn->query("SELECT * FROM customers WHERE customer_id = $id");
$customer = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

$first_name = $_POST['first_name'];
$surname = $_POST['surname'];
$email = $_POST['email'];
$date_of_birth = $_POST['date_of_birth'];
$sms_opt_in = isset($_POST['sms_opt_in']) ? 1 : 0;
$email_opt_in = isset($_POST['email_opt_in']) ? 1 : 0;

$stmt = $conn->prepare("UPDATE customers SET 
first_name=?, surname=?, email=?, date_of_birth=?, sms_opt_in=?, email_opt_in=? 
WHERE customer_id=?");

$stmt->bind_param("ssssiii", $first_name, $surname, $email, $date_of_birth, $sms_opt_in, $email_opt_in, $id);

$stmt->execute();

header("Location: admin_customers.php");
exit();
}
?>

<h2>Edit Customer</h2>

<form method="POST">

First Name:<br>
<input type="text" name="first_name" value="<?= $customer['first_name'] ?>" required><br><br>

Surname:<br>
<input type="text" name="surname" value="<?= $customer['surname'] ?>" required><br><br>

Email:<br>
<input type="email" name="email" value="<?= $customer['email'] ?>" required><br><br>

Date of Birth:<br>
<input type="date" name="date_of_birth" value="<?= $customer['date_of_birth'] ?>"><br><br>

SMS Notifications:
<input type="checkbox" name="sms_opt_in" <?= $customer['sms_opt_in'] ? "checked" : "" ?>><br><br>

Email Notifications:
<input type="checkbox" name="email_opt_in" <?= $customer['email_opt_in'] ? "checked" : "" ?>><br><br>

<button type="submit">Update Customer</button>

</form>