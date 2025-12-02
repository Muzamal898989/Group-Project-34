<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])){
    header("location: admin_login.php");
    exit();
}
 include 'includes/db_connect.php';

 $result = $conn->query(
    "SELECT orders.order_id, users.name AS customer,
    orders.total_price, orders.total_calories,orders.created_at
    FROM orders
    JOIN users ON orders.user_id = users.user_id
    ORDER BY orders.created_at DESC"
 );
?>
<h2>All Orders</h2>
<table border="1" cellpadding+"10">
    <tr>
        <th>Order ID</th>
        <th>Student</th>
        <th>Total Price</th>
        <th>Total Calories</th>
        <th>Ordered At</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['order_id'] ?></td>
            <td><?= $row['customer'] ?></td>
            <td><?= $row['total_price'] ?></td>
            <td><?= $row['total_calories'] ?></td>
            <td><?= $row['created_at'] ?></td>
    </tr>
    <?php endwhile; ?>
</table>