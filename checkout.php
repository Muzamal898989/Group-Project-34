<?php
session_start();
include __DIR__ . '/config/db.php';

// Make sure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// If form submitted → process order
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $postcode = trim($_POST['postcode']);
    $delivery = $_POST['delivery'];
    $payment = $_POST['payment'];

    // Verify email matches the logged-in user's email
    $stmt = $pdo->prepare("SELECT email FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user || $user['email'] !== $email) {
        die("<h1>Error:</h1><p>The email you entered does not match your registered email.</p><p><a href='checkout.php'>Go back</a></p>");
    }

    $total_price = 0;
    $total_calories = 0;

    // Calculate totals
    foreach ($_SESSION['basket'] as $item) {
        $stmt = $pdo->prepare("SELECT price, calories, stock FROM meals WHERE meal_id = ?");
        $stmt->execute([$item['meal_id']]);
        $meal = $stmt->fetch();

        if ($meal) {
            $subtotal = $meal['price'] * $item['quantity'];
            $subcal = $meal['calories'] * $item['quantity'];

            $total_price += $subtotal;
            $total_calories += $subcal;
        }
    }

	// Prevent checkout if basket is empty or total is 0
    if ($total_price <= 0 || empty($_SESSION['basket'])) {
        die("<h1>Error:</h1><p>Your basket is empty. You cannot place an order with total £0.</p><p><a href='basket.php'>Return to Basket</a></p>");
    }

    // Insert into orders
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price, total_calories, status) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $total_price, $total_calories, 'pending']);
    $order_id = $pdo->lastInsertId();

    // Insert order items + update stock
    foreach ($_SESSION['basket'] as $item) {
        $stmt = $pdo->prepare("SELECT price, calories, stock FROM meals WHERE meal_id = ?");
        $stmt->execute([$item['meal_id']]);
        $meal = $stmt->fetch();

        if ($meal) {
            // Save order item
            $stmt2 = $pdo->prepare("INSERT INTO order_items (order_id, meal_id, quantity, item_price, item_calories) VALUES (?, ?, ?, ?, ?)");
            $stmt2->execute([
                $order_id,
                $item['meal_id'],
                $item['quantity'],
                $meal['price'],
                $meal['calories']
            ]);

            // Update stock
            $newStock = $meal['stock'] - $item['quantity'];
            $stmt3 = $pdo->prepare("UPDATE meals SET stock = ? WHERE meal_id = ?");
            $stmt3->execute([$newStock, $item['meal_id']]);
        }
    }

	// Clear basket
	$_SESSION['basket'] = [];

	// Log the user out
	session_unset(); // Unset all session variables
	session_destroy(); // Destroy the session

	echo "<h1>Order placed successfully!</h1>";
	echo "<p>You have been logged out for security reasons.</p>";
	echo "<p><a href='login.php'>Log in again</a> or <a href='Home.html'>Return to homepage</a></p>";
	exit;	
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout – DormDiner</title>
    <style>
    /* Your original styling remains unchanged */
    body { font-family: 'Roboto', Arial, sans-serif; background-color: #fff9ea; margin: 0; padding: 0; color: #333; }
    .container { display: flex; flex-wrap: wrap; max-width: 1200px; margin: 40px auto; background: #fff; border-radius: 12px; border: 2px solid #d88764; box-shadow: 0 4px 12px rgba(0,0,0,0.1); overflow: hidden; }
    .left, .right { flex: 1; padding: 30px; }
    .left { background: #004aad; color: #fff; }
    .left h1 { font-family: 'Anton', sans-serif; font-size: 2.2em; margin-bottom: 15px; color: #fff; }
    .left ul { list-style: none; padding: 0; margin: 20px 0; }
    .left ul li { margin-bottom: 10px; font-weight: bold; }
    .left a { display: inline-block; margin-top: 20px; color: #fff; text-decoration: none; font-weight: bold; }
    .left a:hover { text-decoration: underline; }
    .right h2 { color: #004aad; margin-top: 20px; margin-bottom: 10px; }
    form label { display: block; margin-top: 12px; font-weight: bold; color: #333; }
    form input, form select { width: 100%; padding: 10px; margin-top: 6px; border: 1px solid #ccc; border-radius: 6px; font-size: 0.95em; }
    .order-summary { background: #fff9ea; border: 1px solid #d88764; border-radius: 8px; padding: 15px; margin-top: 10px; }
    .summary-item { display: flex; justify-content: space-between; margin-bottom: 8px; }
    .total { text-align: right; font-weight: bold; font-size: 1.2em; color: #004aad; margin-top: 12px; }
    button[type="submit"] { display: block; width: 100%; padding: 14px; margin-top: 25px; background: #004aad; color: #fff; border: none; border-radius: 6px; font-size: 1.1em; font-weight: bold; cursor: pointer; transition: background 0.2s ease; }
    button[type="submit"]:hover { background: #003080; }
    </style>
</head>
<body>
<div class="container">

    <!-- LEFT PANEL -->
    <div class="left">
        <h1>Checkout</h1>
        <p>Review your order and enter your details to complete your purchase.</p>
        <ul>
            <li>Fast Delivery</li>
            <li>Secure Payment</li>
            <li>Fresh, Hot Food</li>
        </ul>
        <a href="basket.php">← Back to Basket</a>
    </div>

    <!-- RIGHT PANEL -->
    <div class="right">
        <h2>Customer Details</h2>
        <form method="POST" action="checkout.php">
            <label>Full Name *</label>
            <input type="text" name="fullname" required>

            <label>Email Address *</label>
            <input type="email" name="email" required>

            <label>Phone Number *</label>
            <input type="text" name="phone" required>

            <label>Delivery Address *</label>
            <input type="text" name="address" placeholder="Street Address" required>
            <input type="text" name="city" placeholder="City" required>
            <input type="text" name="postcode" placeholder="Postcode" required>

            <h2>Delivery Method</h2>
            <select name="delivery" required>
                <option value="">Choose a delivery option</option>
                <option>Standard Delivery (1-2 Working Days) – £1.99</option>
                <option>Express Delivery (30 - 45 mins) – £2.99</option>
            </select>

            <h2>Payment Method</h2>
            <select name="payment" required>
                <option value="">Choose a payment method</option>
                <option>Credit/Debit Card</option>
                <option>PayPal</option>
                <option>Apple Pay</option>
            </select>

            <h2>Your Order Summary</h2>
            <div class="order-summary">
                <?php
                $total = 0;
                foreach ($_SESSION['basket'] as $item) {
                    $stmt = $pdo->prepare("SELECT name, price FROM meals WHERE meal_id = ?");
                    $stmt->execute([$item['meal_id']]);
                    $meal = $stmt->fetch();

                    if ($meal) {
                        $line_total = $meal['price'] * $item['quantity'];
                        $total += $line_total;
                        echo "<div class='summary-item'>
                                <span>" . htmlspecialchars($meal['name']) . " (x" . $item['quantity'] . ")</span>
                                <span>£" . number_format($line_total, 2) . "</span>
                              </div>";
                    }
                }
                echo "<div class='total'>Total: £" . number_format($total, 2) . "</div>";
                ?>
            </div>
            <br><br>
            <button type="submit">Place Order</button>
        </form>
    </div>

</div>
</body>
</html>
