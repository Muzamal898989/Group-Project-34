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
</div>
</body>
</html>
