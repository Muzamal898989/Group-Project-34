<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once('config/db.php'); // PDO connection

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'Student';
$user_email = $_SESSION['email'] ?? '';

// Handle change password form submission
$password_message = "";
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $password_message = "<p style='color:red;'>Please fill in all password fields.</p>";
    } elseif (strlen($new_password) < 8) {
        $password_message = "<p style='color:red;'>New password must be at least 8 characters.</p>";
    } elseif ($new_password !== $confirm_password) {
        $password_message = "<p style='color:red;'>New passwords do not match.</p>";
    } else {
        // Fetch current hashed password from DB
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($current_password, $user['password'])) {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update->execute([$hashed, $user_id]);
            $password_message = "<p style='color:green;'>Password changed successfully!</p>";
        } else {
            $password_message = "<p style='color:red;'>Current password is incorrect.</p>";
        }
    }
}

// Fetch recent orders for this user
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
        h1 { color:#004aad; }
        table { width:100%; border-collapse:collapse; }
        th, td { padding:12px; border-bottom:1px solid #ddd; text-align:left; }
        th { background:#004aad; color:white; }
        .status-pending { color:orange; font-weight:bold; }
        .status-completed { color:green; font-weight:bold; }
        .status-cancelled { color:red; font-weight:bold; }
        a.reorder-btn { color:#004aad; text-decoration:none; font-weight:bold; }
        a.reorder-btn:hover { text-decoration:underline; }

        /* Change password section */
        .change-password { margin-top: 40px; border-top: 2px solid #e0e0e0; padding-top: 25px; }
        .change-password h2 { color: #004aad; }
        .change-password .form-group { margin-bottom: 15px; }
        .change-password label { display:block; margin-bottom:5px; font-weight:bold; color:#333; }
        .change-password input[type="password"] {
            width: 100%;
            max-width: 400px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }
        .change-password small { color: grey; display:block; margin-top:4px; }
        .change-password button {
            background: #004aad;
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            cursor: pointer;
            margin-top: 5px;
        }
        .change-password button:hover { background: #003080; }
    </style>
</head>
<body>
<header>
    <div>Welcome, <?php echo htmlspecialchars($user_name); ?></div>
    <nav>
        <a href="menu.php">Menu</a>
        <a href="basket.php">Basket</a>
        <a href="user_logout.php">Logout</a>
    </nav>
</header>

<div class="container">
    <h1>User Dashboard</h1>
    <p>Email: <?php echo htmlspecialchars($user_email); ?></p>

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
                <td class="status-<?php echo strtolower($order['status']); ?>"><?php echo ucfirst($order['status']); ?></td>
                <td>
                    <?php if($order['status'] === 'completed'): ?>
                        <form method="POST" action="reorder.php" style="margin:0;">
                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                            <button type="submit" class="reorder-btn">Reorder</button>
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

    <!-- Change Password Section -->
    <div class="change-password">
        <h2>Change Password</h2>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="form-group">
                <label>Current Password</label>
                <input type="password" name="current_password" placeholder="Enter your current password" required>
            </div>

            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" placeholder="Enter new password" minlength="8" required>
                <small>Must be at least 8 characters.</small>
            </div>

            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" placeholder="Confirm new password" minlength="8" required>
            </div>

            <button type="submit" name="change_password">Update Password</button>
        </form>

        <?php if ($password_message) echo $password_message; ?>
    </div>

</div>
</body>
</html>