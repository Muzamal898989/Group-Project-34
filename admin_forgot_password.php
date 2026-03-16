<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'config/db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);

    if (empty($username)) {
        $message = "<p style='color:red;'>Please enter your username.</p>";
    } else {
        $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            // Generate secure token
            $token = bin2hex(random_bytes(32));
            $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

            $update = $conn->prepare("UPDATE admin SET reset_token=?, reset_expires=? WHERE username=?");
            $update->bind_param("sss", $token, $expiry, $username);
            $update->execute();

            $resetLink = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/admin_reset_password.php?token=" . $token;

            // ── TESTING ONLY — REMOVE IN PRODUCTION ──────────────────────
            $message = "<p style='color:green;'>Reset link generated! 
                        <a href='$resetLink'>Click here to reset your password</a>.</p>";
            // In production, send $resetLink via email to the admin instead
        } else {
            $message = "<p style='color:green;'>If that username exists, a reset link has been generated.</p>";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="css/stafflogin.css">
  <title>Admin Forgot Password - DormDiner</title>
  <style>
    .forgot-link { display:block; margin-top:10px; font-size:14px; color:#8B4513; text-decoration:none; }
    .forgot-link:hover { text-decoration:underline; }
  </style>
</head>
<body>
  <div class="container">
    <div class="left">
      <img src="logo.jpeg" alt="DormDiner Logo" width="100" height="100">
      <h1>Welcome to DormDiner Staff Website</h1>
      <ul>
        <li><a href="Home.php">Back To Home</a></li>
        <li><a href="contact.html">Customer Communication</a></li>
      </ul>
    </div>
    <div class="right">
      <h2>Forgot Password</h2>
      <p>Enter your admin username to get a password reset link.</p>
      <form method="POST" action="admin_forgot_password.php">
        <input type="text" name="username" placeholder="Enter your username" required />
        <button type="submit">Send Reset Link</button>
      </form>
      <a href="admin_login.php" class="forgot-link">Back to Login</a>
      <?php if ($message) echo $message; ?>
    </div>
  </div>
</body>
</html>
