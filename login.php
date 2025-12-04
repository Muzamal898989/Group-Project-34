<?php
session_start();
require_once 'includes/db_connect.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']   ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$email || !$password) {
        $error = 'Please fill in both fields.';
    } else {
        $stmt = $conn->prepare('SELECT user_id, password FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($uid, $hash);
            $stmt->fetch();
            if (password_verify($password, $hash)) {
                $_SESSION['user_id'] = $uid;
                $_SESSION['email']   = $email;
                header('Location: menu.html');
                exit;
            }
        }
        $error = 'Email or password incorrect.';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Login - Dorm Diner</title>
  <link rel="stylesheet" href="css/login.css">
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Roboto&display=swap" rel="stylesheet">
</head>
<body>
<header id="main-header">
  <nav class="top-nav">
    <a href="menu.html">Menu</a>
  </nav>
  <div class="header-content">
    <img src="logo.jpeg" alt="Dorm Diner logo">
    <h1>Welcome to Dorm Diner</h1>
  </div>
</header>

<main>
  <section id="login">
    <h2>Login Here</h2>

    <?php if ($error): ?>
      <p style="color:#c45f5f;text-align:center;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" name="email" placeholder="Email" required>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" name="password" placeholder="Password" required>
      </div>
      <div class="form-group">
        <input type="submit" value="Login" class="btn btn-primary">
      </div>
      <div class="form-group">
        <p>Don't have an account? <a href="signUp.php">Sign Up Here</a></p>
      </div>
    </form>
  </section>
</main>

<footer>
  <p>&copy;2025 Dorm Diner <a href="contact.html">Contact Us</a></p>
</footer>
</body>
</html>