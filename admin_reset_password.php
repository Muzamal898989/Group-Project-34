<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'config/db.php';

$message = "";
$token = isset($_GET['token']) ? trim($_GET['token']) : "";

// Validate token before showing form
$tokenValid = false;
if ($token) {
    $safeToken = $conn->real_escape_string($token);
    $check = $conn->query("SELECT * FROM admin WHERE reset_token='$safeToken' AND reset_expires > NOW()");
    $tokenValid = ($check->num_rows === 1);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = trim($_POST['token']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($new_password) || empty($confirm_password)) {
        $message = "<p style='color:red;'>Please fill in all fields.</p>";
    } elseif (strlen($new_password) < 8) {
        $message = "<p style='color:red;'>Password must be at least 8 characters.</p>";
    } elseif ($new_password !== $confirm_password) {
        $message = "<p style='color:red;'>Passwords do not match.</p>";
    } else {
        $safeToken = $conn->real_escape_string($token);
        $check = $conn->query("SELECT * FROM admin WHERE reset_token='$safeToken' AND reset_expires > NOW()");

        if ($check->num_rows === 1) {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE admin SET password=?, reset_token=NULL, reset_expires=NULL WHERE reset_token=?");
            $stmt->bind_param("ss", $hashed, $token);
            $stmt->execute();
            $message = "<p style='color:green;'>Password reset successfully! <a href='admin_login.php'>Login here</a>.</p>";
            $tokenValid = false; // Hide form after success
        } else {
            $message = "<p style='color:red;'>This reset link is invalid or has expired. <a href='admin_forgot_password.php'>Request a new one</a>.</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="css/stafflogin.css">
  <title>Admin Reset Password - DormDiner</title>
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
      <h2>Reset Password</h2>

      <?php if (!$token || !$tokenValid): ?>
        <?php if ($message): ?>
          <?php echo $message; ?>
        <?php else: ?>
          <p style="color:red;">This reset link is invalid or has expired. 
          <a href="admin_forgot_password.php">Request a new one</a>.</p>
        <?php endif; ?>
      <?php else: ?>
        <form method="POST" action="admin_reset_password.php?token=<?php echo htmlspecialchars($token); ?>">
          <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
          <input type="password" name="new_password" placeholder="New password" minlength="8" required />
          <input type="password" name="confirm_password" placeholder="Confirm new password" minlength="8" required />
          <small style="color:#5a5a5a; display:block; margin-bottom:10px;">Must be at least 8 characters.</small>
          <button type="submit">Reset Password</button>
        </form>
        <a href="admin_login.php" class="forgot-link">Back to Login</a>
        <?php if ($message) echo $message; ?>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
