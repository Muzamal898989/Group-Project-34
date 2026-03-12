<?php
session_start();

//check if admin is logged in
if(!isset($_SESSION['admin_logged_in'])){
    header("location: admin_login.php");
    exit();
}

include 'config/db.php';

// Get latest stock update
$latest_stock_change = $conn->query("
SELECT a.admin_username, m.name, a.old_stock, a.new_stock, a.created_at
FROM activity_log a
JOIN meals m ON a.meal_id = m.meal_id
WHERE a.action='Stock Updated'
ORDER BY a.created_at DESC
LIMIT 1
")->fetch_assoc();

//swicth back to admin from customer
unset($_SESSION['view_mode']);

//DELETE MEAL 
if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])){
    $meal_id = intval($_GET['id']);
 $stmt = $conn->prepare("DELETE FROM meals WHERE meal_id = ?");
 $stmt->bind_param("i", $meal_id);
 $stmt->execute();
 
header("Location: admin_dashboard.php");
 exit();
 }

 //ADD MEAL
 if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['action']) && $_GET['action'] == 'add'){

    $name = $_POST['name'];
    $price = $_POST['price'];
    $calories = $_POST['calories'];
    $stock = $_POST['stock'];
    
    $sql= "INSERT INTO meals (name, price, calories, stock) VALUES (?,?,?,?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdii", $name,$price, $calories, $stock);
    $stmt->execute();
    echo "meal has been added successfully WHOOP!";

    header("Location: admin_dashboard.php");
 exit();
 }

 //UPDATE EXISITING MEAL
 if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['action']) && $_GET['action'] == 'edit'){

$name = $_POST['name'];
$price = $_POST['price'];
$calories = $_POST['calories'];
$stock = $_POST['stock'];
$comment = $_POST['comment'];

$meal_id = intval($_GET['id']);

// Get old stock
$old_data = $conn->query("SELECT stock FROM meals WHERE meal_id = $meal_id")->fetch_assoc();
$old_stock = $old_data['stock'];

// Update meal
$stmt = $conn->prepare("UPDATE meals SET name=?, price=?, calories=?, stock=? WHERE meal_id=?");
$stmt->bind_param("sdiii", $name, $price, $calories, $stock, $meal_id);
$stmt->execute();

// Log activity if stock changed
if($old_stock != $stock){

$admin = $_SESSION['admin_username'];

$log_stmt = $conn->prepare("INSERT INTO activity_log 
(admin_username, action, meal_id, old_stock, new_stock, comment) 
VALUES (?, 'Stock Updated', ?, ?, ?, ?)");

$log_stmt->bind_param("siiis", $admin, $meal_id, $old_stock, $stock, $comment);
$log_stmt->execute();

}

header("Location: admin_dashboard.php");
exit();
}
 
// Fetch all meals for the table
//$result = $conn->query("SELECT * FROM meals ORDER BY meal_id DESC");
// Build search/filter query
$query = "SELECT * FROM meals WHERE 1=1";

if(!empty($_GET['search'])){
    $search = $conn->real_escape_string($_GET['search']);
    $query .= " AND name LIKE '%$search%'";
}

if(!empty($_GET['min_price'])){
    $query .= " AND price >= " . floatval($_GET['min_price']);
}

if(!empty($_GET['max_price'])){
    $query .= " AND price <= " . floatval($_GET['max_price']);
}

if(!empty($_GET['calories'])){
    $query .= " AND calories <= " . intval($_GET['calories']);
}

if(isset($_GET['stock'])){
    if($_GET['stock'] == "in"){
        $query .= " AND stock > 0";
    }
    if($_GET['stock'] == "out"){
        $query .= " AND stock = 0";
    }
}

$query .= " ORDER BY meal_id DESC";

$result = $conn->query($query);
 //VIEW
?>
<!DOCTYPE html>
<html lang= "en">
    <head>
        <link rel="stylesheet" href="css/admin.css">
         
        <title>admin dashboard</title>
</head>
<body>
    <div class="dashboard-wrapper">
        <!--SIDE BAR -->
     <nav class="sidebar">
        <div class="sidebar-header">
            <img src="images/LogoHeader.jpg" alt="Dorm Diner Logo" style="height: 110px;">
        </div>
        <div class="nav-links">
    <a href="admin_dashboard.php?action=add">Add Meal</a>
    <a href="view_orders.php">View Orders</a>
    <a href="admin_activity.php">View Activity Log</a>
    <a href="logout.php">Logout</a>
    <a href="admin_customers.php">Manage Customers</a>
   </div>
   <hr style="border: 0.5px solid #e0be8a; margin: 20px 0;">

   <div class="sidebar-filters">
<h4>Filters</h4>
<form method="GET">
    <input type="text" name="search" placeholder="Search meals...">
   <input type="number" step="0.01" name="min_price" placeholder="Min Price">
    <input type="number" step="0.01" name="max_price" placeholder="Max Price">
    <select name="stock">
        <option value="">All</option>
        <option value="out">Out of Stock</option>
        <option value="in">In Stock</option>
    </select>
    <input type="number" name="calories" placeholder="Max Calories">
    <button type="submit">Search</button>
    <a href="admin_dashboard.php">Reset Filters</a>
</form>
</div>
   </nav>

   <!--MAIN CONTENT -->
   <div class="main-content">
     <header class="top-header">
        
        <h1>Welcome, <?= $_SESSION['admin_username'] ?> </h1>
      
    </header>
    
    
    <?php if($latest_stock_change): ?>
<div style="background:#fff3cd; padding:10px; border:1px solid #ffeeba; margin-bottom:15px;">
Stock Updated: <b><?= $latest_stock_change['admin_username'] ?></b>
changed <b><?= $latest_stock_change['name'] ?></b> stock 
from <b><?= $latest_stock_change['old_stock'] ?></b>
to <b><?= $latest_stock_change['new_stock'] ?></b>
</div>
<?php endif; ?>

   
    <!--ADD BUTTON FOR CUSTOMER MODE-->
<form method="POST" action="switch_mode.php">
    <button type="submit" name="mode" value="customer">
        Switch to Customer Mode
    </button>
</form>
  <br>
<!--ADD MEAL -->
<?php if(isset($_GET['action']) && $_GET['action'] == "add"): ?>
<h2>Add Meal</h2>
<form method="POST" action="admin_dashboard.php?action=add">
<label>Name:</label>
        <input type="text" name="name" required>
          <label>Price:</label>
          <input type="number" name="price" step="0.01" required>
                <label>Calories:</label>
        <input type="number" name="calories" required>
              <label>Stock:</label>
        <input type="number" name="stock" required>
        <button type="submit">Add Meal</button>
</form>

<hr>
<?php endif ?>
 
<!--EDIT MEAL -->
<?php if(isset($_GET['action']) && $_GET['action'] == "edit" && isset($_GET['id'])): ?>
    <?php $meal_id = intval($_GET['id']);
    $meal_data = $conn->query("SELECT * FROM meals WHERE meal_id = $meal_id")->fetch_assoc();
     ?>
     <h2>Edit Meal</h2>
    <form method="POST" action="admin_dashboard.php?action=edit&id=<?= $meal_id ?>">
<label>Name:</label>
<input type="text" name="name" value="<?= $meal_data['name'] ?>" required>
          <label>Price:</label>
          <input type="number" name="price" steps="0.01" value="<?= $meal_data['price'] ?>" required>
                <label>Calories:</label>
        <input type="number" name="calories" value="<?= $meal_data['calories'] ?>" required>
              <label>Stock:</label>
        <input type="number" name="stock" value="<?= $meal_data['stock'] ?>" required >
        <label>Admin Comment (optional):</label>
<textarea name="comment" placeholder="Reason for stock change"></textarea>
        <button type="submit">Edit Meal</button>
</form>
<hr>
<?php endif; ?>
<!--SEARCH AND FILTER -->
 <!--<section class="card">
    <h3>Filters</h3>
<form method="GET">
    <input type="text" name="search" placeholder="Search meals...">
   <input type="number" step="0.01" name="min_price" placeholder="Min Price">
    <input type="number" step="0.01" name="max_price" placeholder="Max Price">
    <select name="stock">
        <option value="">All</option>
        <option value="out">Out of Stock</option>
        <option value="in">In Stock</option>
    </select>
    <input type="number" name="calories" placeholder="Max Calories">
    <button type="submit">Search</button>
    <a href="admin_dashboard.php">Reset Filters</a>
</form>
</section>-->
<br>

<!--MEAL TABLE -->
<section class="card">
    <div class="table-header">
<h2>All Meals</h2>
</div>
<table>
    <tr>
        <th>Name</th>
        <th>Price</th>
        <th>Calories</th>
        <th>Stock</th>
        <th>Actions</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['name'] ?></td>
            <td><?= $row['price'] ?></td>
            <td><?= $row['calories'] ?></td>
            <td><?= $row['stock'] ?></td>
        <td>
            <a href="admin_dashboard.php?action=edit&id=<?= $row['meal_id'] ?>">Edit</a>
            <a href="admin_dashboard.php?action=delete&id=<?= $row['meal_id'] ?>">Delete</a>
        </td>
        </tr>
        <?php endwhile; ?>
</table>
<footer>
    Dorm Diner Admin Dashboard
</footer>
</body>
</html>