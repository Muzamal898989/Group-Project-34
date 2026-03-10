<?php
// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once('config/db.php'); // PDO connection

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$stmt = $pdo->prepare("SELECT name, email FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found.";
    exit();
}

// -------------------------
// UPDATE PROFILE
// -------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $new_name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $new_email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

    if ($new_name && $new_email) {
        $update = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE user_id = ?");
        $update->execute([$new_name, $new_email, $user_id]);

        $_SESSION['user_name'] = $new_name;
        $_SESSION['email'] = $new_email;

        $success_message = "Profile updated successfully.";
    } else {
        $error_message = "Invalid input. Please check your details.";
    }
}

// -------------------------
// CHANGE PASSWORD
// -------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if ($new_pass === $confirm_pass && strlen($new_pass) >= 8) {
        $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $stmt->execute([$hashed, $user_id]);
        $success_message = "Password updated successfully.";
    } else {
        $error_message = "Passwords do not match or are too short (min 8 characters).";
    }
}

// -------------------------
// DELETE ACCOUNT
// -------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
    $delete = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
    $delete->execute([$user_id]);

    session_destroy();
    header("Location: goodbye.php");
    exit();
}

// -------------------------
// FETCH RECENT ORDERS
// -------------------------
$stmt = $pdo->prepare("
    SELECT 
        order_id, 
        created_at AS order_date, 
        status, 
        total_price AS total_amount 
    FROM orders 
    WHERE user_id = ? 
    ORDER BY created_at DESC 
    LIMIT 5
");
$stmt->execute([$user_id]);
$recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard - Dorm Diner</title>
    <link rel="stylesheet" href="css/dashboard-style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; margin:0; padding:0; }
        header { background:#004aad; color:white; padding:15px; display:flex; justify-content:space-between; align-items:center; }
        header a { color:white; text-decoration:none; margin-left:15px; }
        .container { max-width: 1200px; margin: 30px auto; background:white; padding:20px; border-radius:10px; box-shadow:0 4px 10px rgba(0,0,0,0.1);}
        h1, h2 { color:#004aad; }
        table { width:100%; border-collapse:collapse; }
        th, td { padding:12px; border-bottom:1px solid #ddd; text-align:left; }
        th { background:#004aad; color:white; }
        .status-pending { color:orange; font-weight:bold; }
        .status-completed { color:green; font-weight:bold; }
        .status-cancelled { color:red; font-weight:bold; }
        button { padding:8px 12px; border:none; border-radius:5px; cursor:pointer; }
        .delete-btn { background:red; color:white; }
        .update-btn { background:#004aad; color:white; }
    </style>
</head>
<body>

<header>
    <div>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
    <nav>
        <a href="menu.php">Menu</a>
        <a href="basket.php">Basket</a>
        <a href="user_logout.php">Logout</a>
    </nav>
</header>

<div class="container">
    <h1>User Dashboard</h1>

    <?php if(isset($success_message)) echo "<p style='color:green;'>$success_message</p>"; ?>
    <?php if(isset($error_message)) echo "<p style='color:red;'>$error_message</p>"; ?>

    <!-- USER DETAILS -->
    <h2>Your Details</h2>
    <form method="POST">
        <label>Name:</label><br>
        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br><br>

        <button type="submit" name="update_profile" class="update-btn">Update Details</button>
    </form>

    <hr>

    <!-- CHANGE PASSWORD -->
    <h2>Change Password</h2>
    <form method="POST">
        <label>New Password (min 8 chars):</label><br>
        <input type="password" name="new_password" required><br><br>

        <label>Confirm Password:</label><br>
        <input type="password" name="confirm_password" required><br><br>

        <button type="submit" name="change_password" class="update-btn">Update Password</button>
    </form>

    <hr>

    <!-- DELETE ACCOUNT -->
    <h2>Delete Account</h2>
    <form method="POST" onsubmit="return confirm('Are you sure you want to delete your account? This cannot be undone.');">
        <button type="submit" name="delete_account" class="delete-btn">Delete Account</button>
    </form>

    <hr>

    <!-- RECENT ORDERS -->
    <h2>Recent Orders</h2>
    <?php if($recent_orders): ?>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Total (£)</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($recent_orders as $order): ?>
            <tr>
                <td><?php echo $order['order_id']; ?></td>
                <td><?php echo date('d M Y H:i', strtotime($order['order_date'])); ?></td>
                <td><?php echo number_format($order['total_amount'], 2); ?></td>
                <td class="status-<?php echo strtolower($order['status']); ?>">
                    <?php echo ucfirst($order['status']); ?>
                </td>
                <td>
                    <?php if($order['status'] === 'completed'): ?>
                        <form method="POST" action="reorder.php" style="margin:0;">
                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                            <button type="submit" class="update-btn">Reorder</button>
                        </form>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p>No recent orders found.</p>
    <?php endif; ?>

</div>
</body>
</html>
