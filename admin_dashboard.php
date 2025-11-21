<?php
session_start();

//check if admin is logged in
if(!isset($_SESSION['admin_logged_in'])){
    header("location: admin_login.php");
    exit();
}


include 'includes/db_connect.php';

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
    $cuisine = $_POST['cuisine'];
    $stock = $_POST['stock'];
    
    $sql= "INSERT INTO meals (name, price, calories, cuisine, stock) VALUES (?,?,?,?,?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdisi", $name,$price, $calories, $cuisine, $stock);
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
    $cuisine = $_POST['cuisine'];
    $stock = $_POST['stock'];
    
    $meal_id = intval($_GET['id']);
    $stmt = $conn->prepare("UPDATE meals SET name=?, price=?, calories=?, cuisine=?, stock=? WHERE meal_id=?");
    $stmt->bind_param("sdisii", $name, $price, $calories, $cuisine, $stock, $meal_id);
    $stmt->execute();
    echo "meal has been updated successfully WHOOP!";

    header("Location: admin_dashboard.php");
 exit();
 }
// Fetch all meals for the table
$result = $conn->query("SELECT * FROM meals ORDER BY meal_id DESC");

 //VIEW
?>
<!DOCTYPE html>
<html lang= "en">
    <head>
        <title>admin dashboard</title>
</head>
<body>
    <h1>welcome, <?= $_SESSION['admin_username'] ?> </h1>
    <a href="admin_dashboard.php?action=add">Add Meal</a>
    <a href="view_orders.php">View Orders</a>
    <a href="logout.php">Logout</a>
  <hr>
<!--ADD MEAL -->
<?php if(isset($_GET['action']) && $_GET['action'] == "add"): ?>
<h2>Add Meal</h2>
<form method="POST" action="admin_dashboard.php?action=add">
<label>Name:</label>
        <input type="text" name="name" required>
          <label>Price:</label>
          <input type="number" name="price" steps="0.01" required>
                <label>Calories:</label>
        <input type="number" name="calories" required>
              <label>Cuisine:</label>
        <input type="text" name="cuisine" required>
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
              <label>Cuisine:</label>
        <input type="text" name="cuisine" value="<?= $meal_data['cuisine'] ?>" required >
              <label>Stock:</label>
        <input type="number" name="stock" value="<?= $meal_data['stock'] ?>" required >
        <button type="submit">Edit Meal</button>
</form>
<hr>
<?php endif; ?>

<!--MEAL TABLE -->
<h2>All Meals</h2>
<table border="1" cellpadding="10">
    <tr>
        <th>Name</th>
        <th>Price</th>
        <th>Calories</th>
        <th>Cuisine</th>
        <th>Stock</th>
        <th>Actions</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['name'] ?></td>
            <td><?= $row['price'] ?></td>
            <td><?= $row['calories'] ?></td>
            <td><?= $row['cuisine'] ?></td>
            <td><?= $row['stock'] ?></td>
        <td>
            <a href="admin_dashboard.php?action=edit&id=<?= $row['meal_id'] ?>">Edit</a>
            <a href="admin_dashboard.php?action=delete&id=<?= $row['meal_id'] ?>">Delete</a>
        </td>
        </tr>
        <?php endwhile; ?>
</table>
</body>
</html>