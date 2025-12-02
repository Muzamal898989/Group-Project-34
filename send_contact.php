<?php
require_once 'includes/db_connect.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$name || !$email || !$message) {
        $msg = '<p style="color:#c45f5f;">All fields are required.</p>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = '<p style="color:#c45f5f;">Invalid email format.</p>';
    } else {
        $stmt = $conn->prepare('INSERT INTO contact (name, email, message) VALUES (?,?,?)');
        $stmt->bind_param('sss', $name, $email, $message);
        $stmt->execute();
        $msg = '<p style="color:green;">Message sent! We’ll get back to you soon.</p>';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Contact – Dorm Diner</title>
  <link rel="stylesheet" href="css/signup.css">
  <link rel="stylesheet" href="css/contact.css">
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Roboto&display=swap" rel="stylesheet">
</head>
<body>
<header id="main-header">
  <img src="logo.jpeg" alt="Dorm Diner logo">
  <h1>Contact Us</h1>
</header>

<main>
  <section id="contact-card">
    <h2>Drop us a message</h2>

    <?= $msg ?>

    <form action="send_contact.php" method="POST">
      <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" placeholder="Your name" required>
      </div>
      <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" placeholder="your.email@aston.ac.uk" required>
      </div>
      <div class="mb-3">
        <label>Message</label>
        <textarea name="message" rows="5" placeholder="What's on your mind?" required></textarea>
      </div>
      <button type="submit">Send Message</button>
    </form>
    <p class="extra">Or email us directly: <a href="mailto:mealplans@dormdiner.ac.uk">mealplans@dormdiner.ac.uk</a></p>
  </section>
</main>
</body>
</html>