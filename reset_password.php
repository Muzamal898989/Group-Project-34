<?php
session_start();
require_once("functions.php");

$message = "";
$token = isset($_GET['token']) ? trim($_GET['token']) : "";

// Validate token before showing form
$tokenValid = false;
if ($token) {
    $db = db_connect();
    $safeToken = mysqli_real_escape_string($db, $token);
    $check = mysqli_query($db, "SELECT * FROM users WHERE reset_token='$safeToken' AND reset_expires > NOW()");
    $tokenValid = (mysqli_num_rows($check) == 1);
}

if (isset($_POST['reset'])) {
    $message = resetPassword();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Dorm Diner</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <img src="logo.jpeg" style="height: 110px;">
</header>

<main>
    <section id="login">
        <h2>Reset Password</h2>

        <?php if (!$token || !$tokenValid): ?>
            <p style="color:red;">This reset link is invalid or has expired. Please <a href="forgot_password.php">request a new one</a>.</p>
        <?php else: ?>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . '?token=' . htmlspecialchars($token); ?>" method="POST">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="password" placeholder="Enter new password" required>
                </div>

                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirmPassword" placeholder="Confirm new password" required>
                </div>

                <div class="form-group">
                    <input type="submit" name="reset" value="Reset Password" class="btn btn-primary">
                </div>
            </form>

            <?php if ($message) echo $message; ?>
        <?php endif; ?>
    </section>
</main>

<footer>
    <p>&copy; 2025 Dorm Diner <a href="mailto:240146234@aston.ac.uk">Contact Us</a></p>
</footer>

</body>
</html>