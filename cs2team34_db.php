<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'config/db.php';
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $admin['username'];
            header("Location: admin_dashboard.php");
            exit;
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "Admin not found.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="css/stafflogin.css">
  <title>DormDiner Staff Login</title>
  <style>
    .forgot-link {
      display: block;
      margin-top: 10px;
      font-size: 14px;
      color: #8B4513;
      text-decoration: none;
    }
    .forgot-link:hover { text-decoration: underline; }
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
      <h2>Staff Login</h2>
      <form method="POST" action="admin_login.php">
        <input type="text" name="username" id="username" placeholder="Username" required />
        <input type="password" name="password" id="password" placeholder="Password" required />
        <button type="submit">Login</button>
      </form>
      <a href="admin_forgot_password.php" class="forgot-link">Forgot your password?</a>
      <?php if(!empty($error)): ?>
        <p class="message" id="message"><?= $error ?></p>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
