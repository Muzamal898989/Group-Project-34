<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

include 'includes/db_connect.php';

$error = "";

// If form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

     echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Check admin
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
            $error = "Incorrect password";
        }

    } else {
        $error = "Admin not found";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Login</title>
</head>
<body>

<h2>Admin Login</h2>

<?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

<form method="POST" action="admin_login.php">
    <input type="text" name="username" placeholder="Enter username" required><br><br>
    <input type="password" name="password" placeholder="Enter password" required><br><br>
    <button type="submit">Login</button>
</form>

</body>
</html>
