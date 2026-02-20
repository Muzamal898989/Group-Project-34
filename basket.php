<?php
session_start();
include __DIR__ . '/config/db.php';
include __DIR__ . '/config/calorie_helper.php';

$message = '';
$message_type = '';

$cart = $_SESSION['basket']; // their basket

$total_calories = calculate_cart_calories($conn, $cart);

echo "<p>Total Calories: {$total_calories} kcal</p>";

// Display any messages from add_to_basket
if (isset($_SESSION['basket_message'])) {
    $message = $_SESSION['basket_message'];
    $message_type = $_SESSION['basket_message_type'] ?? 'info';
    unset($_SESSION['basket_message']);
    unset($_SESSION['basket_message_type']);
}

// Handle quantity update
if (isset($_POST['update_index'], $_POST['new_quantity'])) {
    $index = (int)$_POST['update_index'];
    $new_quantity = (int)$_POST['new_quantity'];

    if (isset($_SESSION['basket'][$index]) && $new_quantity > 0) {
        $meal_id = $_SESSION['basket'][$index]['meal_id'];
        
        // Check current stock
        $stmt = $pdo->prepare("SELECT name, stock FROM meals WHERE meal_id = ?");
        $stmt->execute([$meal_id]);
        $meal = $stmt->fetch();
        
        if ($meal) {
            $available_stock = (int)$meal['stock'];
            
            if ($new_quantity > $available_stock) {
                $message = "Only {$available_stock} of '{$meal['name']}' available. Quantity set to {$available_stock}.";
                $message_type = 'warning';
                $_SESSION['basket'][$index]['quantity'] = $available_stock;
            } else {
                $_SESSION['basket'][$index]['quantity'] = $new_quantity;
                $message = "Quantity updated successfully!";
                $message_type = 'success';
            }
        }
    }
    
    header("Location: basket.php");
    exit;
}

// Handle item removal
if (isset($_POST['remove_index'])) {
    $index = (int)$_POST['remove_index'];
    if (isset($_SESSION['basket'][$index])) {
        unset($_SESSION['basket'][$index]);
        $_SESSION['basket'] = array_values($_SESSION['basket']); // reindex
        $message = "Item removed from basket.";
        $message_type = 'success';
    }
    header("Location: basket.php");
    exit;
}

// Validate all basket items against current stock
$stock_warnings = [];
if (!empty($_SESSION['basket'])) {
    foreach ($_SESSION['basket'] as $index => &$item) {
        $stmt = $pdo->prepare("SELECT name, stock FROM meals WHERE meal_id = ?");
        $stmt->execute([$item['meal_id']]);
        $meal = $stmt->fetch();
        
        if ($meal) {
            $available_stock = (int)$meal['stock'];
            
            if ($available_stock <= 0) {
                $stock_warnings[] = "'{$meal['name']}' is now out of stock and has been removed from your basket.";
                unset($_SESSION['basket'][$index]);
            } elseif ($item['quantity'] > $available_stock) {
                $stock_warnings[] = "'{$meal['name']}' quantity reduced to {$available_stock} (current stock).";
                $item['quantity'] = $available_stock;
            }
        }
    }
    unset($item);
    $_SESSION['basket'] = array_values($_SESSION['basket']); // reindex
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Basket – Dorm Diner</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #fff9ea;
            color: #333;
        }
        h1 {
            text-align: center;
            color: #004aad;
            margin-bottom: 25px;
        }
        .message {
            max-width: 900px;
            margin: 0 auto 20px auto;
            padding: 15px;
            border-radius: 8px;
            font-weight: bold;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .basket-container {
            max-width: 900px;
            margin: 0 auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            border: 2px solid #d88764;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }
        .basket-item {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr auto auto;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding: 12px 0;
            transition: background 0.2s ease;
        }
        .basket-item:hover {
            background: #fff9ea;
        }
        .basket-item span {
            font-size: 0.95em;
        }
        .basket-item input[type="number"] {
            width: 60px;
            padding: 6px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-right: 12px;
        }
        .basket-item button {
            background: #004aad;
            color: #fff;
            border: none;
            padding: 8px 14px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .basket-item button:hover {
            background: #003080;
        }
        .remove-btn {
            background: #d88764;
        }
        .remove-btn:hover {
            background: #b96a4f;
        }
        .stock-info {
            font-size: 0.85em;
            color: #666;
            font-style: italic;
        }
        .total {
            text-align: right;
            font-weight: bold;
            font-size: 1.2em;
            margin-top: 20px;
            color: #004aad;
        }
        .actions {
            text-align: center;
            margin-top: 30px;
        }
        .actions a,
        .actions button {
            display: inline-block;
            min-width: 180px;
            text-align: center;
            background: #004aad;
            color: #fff;
            text-decoration: none;
            padding: 12px 18px;
            border-radius: 6px;
            margin: 0 10px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.2s ease;
        }
        .actions a:hover,
        .actions button:hover {
            background: #003080;
        }
        .actions button:disabled {
            background: #ccc;
            color: #666;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <h1>Your Basket</h1>
    
    <?php if ($message): ?>
        <div class="message <?= $message_type ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($stock_warnings)): ?>
        <div class="message warning">
            <?php foreach ($stock_warnings as $warning): ?>
                <?= htmlspecialchars($warning) ?><br>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <div class="basket-container">
        <?php
        $total = 0;
        if (!empty($_SESSION['basket'])) {
            foreach ($_SESSION['basket'] as $index => $item) {
                $stmt = $pdo->prepare("SELECT name, price, stock FROM meals WHERE meal_id = ?");
                $stmt->execute([$item['meal_id']]);
                $meal = $stmt->fetch();

                if ($meal) {
                    $subtotal = $meal['price'] * $item['quantity'];
                    $total += $subtotal;
                    $stock = (int)$meal['stock'];

                    echo "<div class='basket-item'>
                            <div>
                                <span>" . htmlspecialchars($meal['name']) . "</span><br>
                                <span class='stock-info'>{$stock} in stock</span>
                            </div>
                            <span>£" . number_format($meal['price'], 2) . " each</span>
                            <span>Subtotal: £" . number_format($subtotal, 2) . "</span>
                            <form method='POST' action='basket.php'>
                                <input type='hidden' name='update_index' value='" . $index . "'>
                                <input type='number' name='new_quantity' value='" . $item['quantity'] . "' 
                                       min='1' max='{$stock}'
                                       onchange='this.form.submit()'>
                            </form>
                            <form method='POST' action='basket.php' 
                                  onsubmit=\"return confirm('Are you sure you want to remove this item?');\">
                                <input type='hidden' name='remove_index' value='" . $index . "'>
                                <button type='submit' class='remove-btn'>Remove</button>
                            </form>
                          </div>";
                }
            }
        } else {
            echo "<p>Your basket is empty.</p>";
        }
        ?>
    </div>

    <div class="total">
        Total: £<?php echo number_format($total, 2); ?>
    </div>

    <div class="actions">
        <a href="menu.php">Continue Shopping</a>
        <?php if (!empty($_SESSION['basket'])): ?>
            <button onclick="window.location.href='checkout.php'">Proceed to Checkout</button>
        <?php else: ?>
            <button disabled style="background:#ccc; cursor:not-allowed;">Proceed to Checkout</button>
        <?php endif; ?>
    </div>
</body>
</html>