<?php
require_once 'includes/db_connect.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = trim($_POST['firstName'] ?? '');
    $last  = trim($_POST['lastName']  ?? '');
    $email = trim($_POST['email']     ?? '');
    $pass  = trim($_POST['password']  ?? '');
    $conf  = trim($_POST['confirmPassword'] ?? '');
    $dob   = $_POST['birthday'] ?: null;


    if (!$first || !$last || !$email || !$pass || !$conf) {
        $msg = '<p style="color:#c45f5f;">All fields are required.</p>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = '<p style="color:#c45f5f;">Invalid email format.</p>';
    } elseif ($pass !== $conf) {
        $msg = '<p style="color:#c45f5f;">Passwords do not match.</p>';
    } else {

        $stmt = $conn->prepare('SELECT 1 FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows) {
            $msg = '<p style="color:#c45f5f;">Email already registered.</p>';
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $ins  = $conn->prepare('INSERT INTO users (first_name, last_name, email, password, birthday) VALUES (?,?,?,?,?)');
            $ins->bind_param('sssss', $first, $last, $email, $hash, $dob);
            $ins->execute();
            $msg = '<p style="color:green;">Account created! You can now <a href="login.php">log in</a>.</p>';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Sign Up - Dorm Diner</title>
  <link rel="stylesheet" href="css/signup.css">
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Roboto&display=swap" rel="stylesheet">
</head>
<body>
<header id="main-header">
  <img src="logo.jpeg" alt="Dorm Diner logo">
  <h1>Register for Dorm Diner</h1>
</header>

<main>
  <section id="signUp-form">
    <h2>Create An Account</h2>

    <?= $msg ?>

    <form method="POST">
      <div class="mb-3">
        <label>First Name</label>
        <input type="text" name="firstName" placeholder="Enter your first name" required>
      </div>
      <div class="mb-3">
        <label>Last Name</label>
        <input type="text" name="lastName" placeholder="Enter your last name" required>
      </div>
      <div class="mb-3">
        <label>Student Email</label>
        <input type="email" name="email" placeholder="Enter your email" required>
      </div>
      <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" placeholder="Enter your password" required>
      </div>
      <div class="mb-3">
        <label>Confirm Password</label>
        <input type="password" name="confirmPassword" placeholder="Confirm your password" required>
      </div>
      <div class="mb-3">
        <label>Birthday</label>
        <input type="date" name="birthday">
      </div>
      <div class="mb-3">
        <label><input type="checkbox" name="notificationType" value="sms"> Receive SMS notifications</label>
      </div>
      <div class="mb-3">
        <label><input type="checkbox" name="notificationType" value="email"> Receive Email notifications</label>
      </div>
      <button type="submit" class="btn btn-primary">Register</button>
      <p>Already have an account? <a href="login.php">Login Here</a></p>
    </form>
  </section>
</main>

<footer>
  <p>&copy;2025 Dorm Diner <a href="mailto:240146234@aston.ac.uk">Contact Us</a></p>
</footer>
</body>
</html>