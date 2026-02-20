<?php
session_start();
require_once 'config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Store the item they wanted to add
    $_SESSION['pending_add'] = [
        'meal_id' => $_GET['meal_id'] ?? $_POST['meal_id'] ?? 0,
        'quantity' => $_GET['quantity'] ?? $_POST['quantity'] ?? 1
    ];
    header("Location: login.php");
    exit();
}

$meal_id = (int)($_GET['meal_id'] ?? $_POST['meal_id'] ?? 0);
$desired_qty = (int)($_GET['quantity'] ?? $_POST['quantity'] ?? 1);

if ($meal_id <= 0 || $desired_qty <= 0) {
    die("Invalid meal or quantity.");
}

// Fetch current stock for the meal
$stmt = $pdo->prepare("SELECT name, stock, price FROM meals WHERE meal_id = ?");
$stmt->execute([$meal_id]);
$meal = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$meal) {
    die("Meal not found.");
}

$available_stock = (int)$meal['stock'];
$meal_name = $meal['name'];

// Check if meal is out of stock
if ($available_stock <= 0) {
    $_SESSION['basket_message'] = "Sorry, '{$meal_name}' is currently out of stock.";
    $_SESSION['basket_message_type'] = 'error';
    header("Location: menu.php");
    exit();
}

// Initialize basket if not set
if (!isset($_SESSION['basket'])) {
    $_SESSION['basket'] = [];
}

// Check how many of this item are already in basket
$current_in_basket = 0;
foreach ($_SESSION['basket'] as $item) {
    if ($item['meal_id'] === $meal_id) {
        $current_in_basket = $item['quantity'];
        break;
    }
}

// Calculate how many we can actually add
$remaining_stock = $available_stock - $current_in_basket;

if ($remaining_stock <= 0) {
    $_SESSION['basket_message'] = "You already have all available stock of '{$meal_name}' in your basket.";
    $_SESSION['basket_message_type'] = 'error';
    header("Location: basket.php");
    exit();
}

// Determine quantity to add (can't exceed remaining stock)
$qty_to_add = min($desired_qty, $remaining_stock);

// Check if meal already exists in basket
$found = false;
foreach ($_SESSION['basket'] as &$item) {
    if ($item['meal_id'] === $meal_id) {
        $item['quantity'] += $qty_to_add;
        $found = true;
        break;
    }
}
unset($item);

// If not in basket, add it
if (!$found) {
    $_SESSION['basket'][] = [
        'meal_id' => $meal_id,
        'quantity' => $qty_to_add
    ];
}

// Set appropriate message
if ($qty_to_add < $desired_qty) {
    $_SESSION['basket_message'] = "Only {$qty_to_add} of '{$meal_name}' available. Added {$qty_to_add} to your basket.";
    $_SESSION['basket_message_type'] = 'warning';
} else {
    $_SESSION['basket_message'] = "'{$meal_name}' added to basket successfully!";
    $_SESSION['basket_message_type'] = 'success';
}

// Redirect to basket
header("Location: basket.php");
exit();
?>