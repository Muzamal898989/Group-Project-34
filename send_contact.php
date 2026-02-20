<?php
require_once 'config/db.php';
session_start(); // Add session to preserve messages

$msg = '';
$name_value = '';
$email_value = '';
$message_value = '';

// Check for success message from redirect
if(isset($_SESSION['contact_success'])){
    $msg = '<p style="color:green;">Message sent! We\'ll get back to you soon.</p>';
    unset($_SESSION['contact_success']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Preserve form values in case of error
    $name_value = htmlspecialchars($name);
    $email_value = htmlspecialchars($email);
    $message_value = htmlspecialchars($message);
    
    if (empty($name) || empty($email) || empty($message)) {
        $msg = '<p style="color:#c45f5f;">All fields are required.</p>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = '<p style="color:#c45f5f;">Invalid email format.</p>';
    } else {
        $stmt = $conn->prepare('INSERT INTO contact (name, email, message) VALUES (?,?,?)');
        $stmt->bind_param('sss', $name, $email, $message);
        
        if($stmt->execute()){
            // Use session to preserve success message across redirect
            $_SESSION['contact_success'] = true;
            $stmt->close();
            // Redirect to clear form (prevents duplicate submissions)
            header("Location: send_contact.php");
            exit();
        } else {
            $msg = '<p style="color:#c45f5f;">Error: ' . $stmt->error . '</p>';
            $stmt->close();
        }
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
  <img src="images/logo.jpeg" alt="Dorm Diner logo">
  <h1>Contact Us</h1>
</header>
<main>
  <section id="contact-card">
    <h2>Drop us a message</h2>
    
    <?= $msg ?>
    
    <form action="send_contact.php" method="POST">
      <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" value="<?= $name_value ?>" placeholder="Your name" required>
      </div>
      <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" value="<?= $email_value ?>" placeholder="your.email@aston.ac.uk" required>
      </div>
      <div class="mb-3">
        <label>Message</label>
        <textarea name="message" rows="5" placeholder="What's on your mind?" required><?= $message_value ?></textarea>
      </div>
      <button type="submit">Send Message</button>
    </form>
    <p class="extra">Or email us directly: <a href="mailto:mealplans@dormdiner.ac.uk">mealplans@dormdiner.ac.uk</a></p>
  </section>
</main>
</body>
</html>