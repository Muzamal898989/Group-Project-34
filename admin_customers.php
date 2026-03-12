<?php
require_once 'config/db.php';
//using customer id (not shown to customer) to fetch customer data 
$result = $conn->query("SELECT * FROM customers ORDER BY customer_id DESC");
?>

<head><link rel="stylesheet" href="css/admin.css"></head>
<h2>Customers</h2>
<br>
<a href="add_customer.php">Add New Customer</a>
<br><br>
<table border="1">

<tr>
<th>First Name</th>
<th>Surname</th>
<th>Email</th>
<th>Date of Birth</th>
<th>SMS Notifications</th>
<th>Email Notifications</th>
<th>Actions</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>

<tr>
<td><?= htmlspecialchars($row['first_name']) ?></td>
<td><?= htmlspecialchars($row['surname']) ?></td>
<td><?= htmlspecialchars($row['email']) ?></td>
<td><?= $row['date_of_birth'] ?></td>
<td><?= $row['sms_opt_in'] ? "Yes" : "No" ?></td>
<td><?= $row['email_opt_in'] ? "Yes" : "No" ?></td>

<td>
<a href="edit_customer.php?id=<?= $row['customer_id'] ?>">Edit</a>
<a href="delete_customer.php?id=<?= $row['customer_id'] ?>" 
onclick="return confirm('Delete this customer?')">Delete</a>
</tr>

<?php endwhile; ?>

</table>