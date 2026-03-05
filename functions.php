<?php

// Connect to the database
function db_connect() {
    $connection = mysqli_connect("localhost", "root", "", "healthy_food_app");

    if (!$connection) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    return $connection;
}

// Validate email must end with .ac.uk
function verify_email($email) {
    if (preg_match("/@.*\.ac\.uk$/", $email)) {
        return true;
    }
    return false;
}

// Register a new user
function registerUser() {
    $db = db_connect();

    $firstName = trim($_POST['firstName']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);

    // 1. Check required fields
    if (empty($firstName) || empty($email) || empty($password) || empty($confirmPassword)) {
        return "<p style='color:red;'>Please fill in all required fields.</p>";
    }

    // 2. Check password length
    if (strlen($password) < 8) {
        return "<p style='color:red;'>Password must be at least 8 characters.</p>";
    }

    // 3. Check password match
    if ($password !== $confirmPassword) {
        return "<p style='color:red;'>Passwords do not match.</p>";
    }

    // 3. Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "<p style='color:red;'>Invalid email format.</p>";
    }

    // 4. Validate .ac.uk domain
    if (!verify_email($email)) {
        return "<p style='color:red;'>Only student emails ending with .ac.uk are allowed.</p>";
    }

    // 5. Check if email already exists (using prepared statement)
    $stmt = mysqli_prepare($db, "SELECT id FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    if (mysqli_stmt_num_rows($stmt) > 0) {
        return "<p style='color:red;'>Email already in use.</p>";
    }
    mysqli_stmt_close($stmt);

    // 6. Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // 7. Insert user (using prepared statement)
    $stmt = mysqli_prepare($db, "INSERT INTO users (name, email, password, student_verified) VALUES (?, ?, ?, 1)");
    mysqli_stmt_bind_param($stmt, "sss", $firstName, $email, $hashedPassword);

    if (mysqli_stmt_execute($stmt)) {
        return "<p style='color:green;'>Registered successfully! <a href='login.php'>Login here</a>.</p>";
    } else {
        return "<p style='color:red;'>Error: " . mysqli_error($db) . "</p>";
    }
}

// Login user
function loginUser() {
    $db_connection = db_connect();

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Use prepared statement to prevent SQL injection
    $stmt = mysqli_prepare($db_connection, "SELECT * FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    // Verify password using password_verify (not md5)
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['email'] = $email;
        header("Location: index.php");
        exit();
    } else {
        return "<p style='color:red;'>Invalid email or password.</p>";
    }
}


function forgotPassword() {
    $db = db_connect();

    $email = trim($_POST['email']);

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "<p style='color:red;'>Please enter a valid email address.</p>";
    }

    // Check if email exists
    $stmt = mysqli_prepare($db, "SELECT id FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    // Always show the same message to prevent email enumeration attacks
    if (mysqli_stmt_num_rows($stmt) == 0) {
        return "<p style='color:green;'>If that email is registered, you'll receive a reset link shortly.</p>";
    }
    mysqli_stmt_close($stmt);

    // Generate a secure random token
    $token = bin2hex(random_bytes(32)); // 64-char hex string
    $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

    // Save token to DB
    $stmt = mysqli_prepare($db, "UPDATE users SET reset_token=?, reset_expires=? WHERE email=?");
    mysqli_stmt_bind_param($stmt, "sss", $token, $expiry, $email);
    mysqli_stmt_execute($stmt);

    // Build the reset link
    $resetLink = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=" . $token;

 
}

// ─────────────────────────────────────────────
// RESET PASSWORD — validates token, updates pw
// ─────────────────────────────────────────────
function resetPassword() {
    $db = db_connect();

    $token = trim($_POST['token']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if (empty($token) || empty($password) || empty($confirmPassword)) {
        return "<p style='color:red;'>Please fill in all fields.</p>";
    }

    if ($password !== $confirmPassword) {
        return "<p style='color:red;'>Passwords do not match.</p>";
    }

    if (strlen($password) < 8) {
        return "<p style='color:red;'>Password must be at least 8 characters.</p>";
    }

    // Validate the token and check it hasn't expired
    $stmt = mysqli_prepare($db, "SELECT id FROM users WHERE reset_token=? AND reset_expires > NOW()");
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) != 1) {
        return "<p style='color:red;'>This reset link is invalid or has expired. <a href='forgot_password.php'>Request a new one</a>.</p>";
    }

    // Hash the new password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Update password and clear the reset token
    $stmt = mysqli_prepare($db, "UPDATE users SET password=?, reset_token=NULL, reset_expires=NULL WHERE reset_token=?");
    mysqli_stmt_bind_param($stmt, "ss", $hashedPassword, $token);
    mysqli_stmt_execute($stmt);

    return "<p style='color:green;'>Password reset successfully! <a href='login.php'>Login here</a>.</p>";
}

?>