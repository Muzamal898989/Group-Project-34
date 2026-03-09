<?php
session_start();
include 'config/db.php';

$result = $conn->query("SELECT * FROM activity_log ORDER BY created_at DESC");
?>

<h2>Admin Activity Log</h2>

<table border="1" cellpadding="10">

<tr>
<th>Admin</th>
<th>Action</th>
<th>Meal ID</th>
<th>Old Stock</th>
<th>New Stock</th>
<th>Comment</th>
<th>Date</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>

<tr>
<td><?= $row['admin_username'] ?></td>
<td><?= $row['action'] ?></td>
<td><?= $row['meal_id'] ?></td>
<td><?= $row['old_stock'] ?></td>
<td><?= $row['new_stock'] ?></td>
<td><?= htmlspecialchars($row['comment']) ?></td>
<td><?= $row['created_at'] ?></td>
</tr>

<?php endwhile; ?>

</table>