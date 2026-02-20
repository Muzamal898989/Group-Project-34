<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])){
    header("location: admin_login.php");
    exit();
}
include 'config/db.php';


// Check if connection exists
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['order_id'], $_POST['status'])) {
    $order_id = (int) $_POST['order_id'];
    $status = $_POST['status'];

    $allowed_status = ['pending', 'completed', 'cancelled'];
    if (in_array($status, $allowed_status)) {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
        $stmt->bind_param("si", $status, $order_id);
        $stmt->execute();
    }
}

$result = $conn->query(
    "SELECT orders.order_id, users.name AS customer,
    orders.total_price, orders.total_calories, orders.created_at, orders.status
    FROM orders
    JOIN users ON orders.user_id = users.user_id
    ORDER BY orders.created_at DESC"
);


// Check if query was successful
if (!$result) {
    die("Query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Orders</title>
</head>
<body>
    <h2>All Orders</h2>
	<a href="admin_dashboard.php">← Back to Dashboard</a>
    <table border="1" cellpadding="10">
        <tr>
            <th>Order ID</th>
    		<th>Student</th>
    		<th>Total Price</th>
    		<th>Total Calories</th>
    		<th>Ordered At</th>
    		<th>Status</th>
    		<th>Actions</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['order_id']) ?></td>
                    <td><?= htmlspecialchars($row['customer']) ?></td>
                    <td>£<?= number_format($row['total_price'], 2) ?></td>
                    <td><?= htmlspecialchars($row['total_calories']) ?></td>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                    <td><?= htmlspecialchars(ucfirst($row['status'])) ?></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                            <select name="status">
                                <option value="pending" <?= $row['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="completed" <?= $row['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="cancelled" <?= $row['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                            <button type="submit">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">No orders found.</td>
            </tr>
        <?php endif; ?>
    </table>
</body>
</html>