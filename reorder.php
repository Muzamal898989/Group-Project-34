<?php
session_start();
require_once(__DIR__ . '/config/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Accept order_id from POST or GET
$order_id = $_POST['order_id'] ?? $_GET['order_id'] ?? null;
if (!$order_id) {
    die("No order selected. <a href='user_dashboard.php'>Go back</a>");
}
$order_id = (int)$order_id;

// Fetch order items along with current stock
$stmt = $pdo->prepare("
    SELECT oi.meal_id, oi.quantity, m.name, m.stock
    FROM order_items oi
    JOIN meals m ON oi.meal_id = m.meal_id
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$items) {
    die("No items found for this order. <a href='user_dashboard.php'>Go back</a>");
}

// Initialize basket if not set
if (!isset($_SESSION['basket'])) {
    $_SESSION['basket'] = [];
}

$added_items = [];
$not_added_items = [];

foreach ($items as $item) {
    $available = (int)$item['stock'];
    $desired = (int)$item['quantity'];

    // Check current basket for existing quantity
    $existing_qty = 0;
    foreach ($_SESSION['basket'] as $b_item) {
        if ($b_item['meal_id'] == $item['meal_id']) {
            $existing_qty = $b_item['quantity'];
            break;
        }
    }

    // Max quantity we can add without exceeding stock
    $qty_to_add = min($desired, max(0, $available - $existing_qty));

    if ($qty_to_add <= 0) {
        $not_added_items[] = $item['name'];
        continue;
    }

    // Merge with existing basket if meal already exists
    $found = false;
    foreach ($_SESSION['basket'] as &$b_item) {
        if ($b_item['meal_id'] == $item['meal_id']) {
            $b_item['quantity'] += $qty_to_add;
            $found = true;
            break;
        }
    }
    unset($b_item);

    if (!$found) {
        $_SESSION['basket'][] = [
            'meal_id' => $item['meal_id'],
            'quantity' => $qty_to_add
        ];
    }

    $added_items[] = $item['name'] . ($qty_to_add < $desired ? " (only $qty_to_add added)" : '');
}

// Feedback message
echo "<h2>Reorder Summary for Order #$order_id</h2>";

if ($added_items) {
    echo "<p>Added to basket: " . implode(', ', $added_items) . "</p>";
}

if ($not_added_items) {
    echo "<p style='color:red;'>Could not add due to insufficient stock: " . implode(', ', $not_added_items) . "</p>";
}

echo "<p><a href='basket.php'>View Basket</a> | <a href='user_dashboard.php'>Back to Dashboard</a></p>";
