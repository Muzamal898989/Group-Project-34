<?php
session_start();
require_once("functions.php");

if(!isset($_SESSION['mfa_email'])){
    header("Location: login.php");
    exit();
}

if(isset($_POST['verify'])){
    $verify_status = verifyOTP();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<h2>Enter OTP</h2>

<form method="POST">
    <input type="text" name="otp" placeholder="Enter 6-digit OTP" required>
    <input type="submit" name="verify" value="Verify OTP">
</form>

<?php
if(isset($verify_status)){
    echo $verify_status;
}
?>

</body>
</html>
