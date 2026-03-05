<?php
session_start();
require_once("functions.php");

$message = "";
if (isset($_POST['forgot'])) {
    $message = forgotPassword();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Dorm Diner</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <img src="logo.jpeg" style="height: 110px;">
</header>

<main>
    <section id="login">
        <h2>Forgot Password</h2>
        <p>Enter your registered email address and we'll send you a password reset link.</p>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" placeholder="Enter your .ac.uk email" required>
            </div>
            <div class="form-group">
                <input type="submit" name="forgot" value="Send Reset Link" class="btn btn-primary">
            </div>
            <div class="form-group">
                <p><a href="login.php">Back to Login</a></p>
            </div>
        </form>

        <?php if ($message) echo $message; ?>
    </section>
</main>

<footer>
    <p>&copy; 2025 Dorm Diner <a href="mailto:240146234@aston.ac.uk">Contact Us</a></p>
</footer>

</body>
</html>