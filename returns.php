
<?php
session_start();
require_once("functions.php");
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$db = db_connect();
$message = "";

function get_current_user_id(mysqli $db, string $email): ?int {
    $stmt = mysqli_prepare($db, "SELECT user_id AS uid FROM users WHERE email=? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = $res ? mysqli_fetch_assoc($res) : null;
    mysqli_stmt_close($stmt);

    if ($row && isset($row['uid']) && $row['uid'] !== null) {
        return (int)$row['uid'];
    }

    $stmt = mysqli_prepare($db, "SELECT id AS uid FROM users WHERE email=? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = $res ? mysqli_fetch_assoc($res) : null;
    mysqli_stmt_close($stmt);

    if ($row && isset($row['uid']) && $row['uid'] !== null) {
        return (int)$row['uid'];
    }
    return null;
}

$currentEmail = $_SESSION['email'];
$currentUserId = get_current_user_id($db, $currentEmail);
if ($currentUserId === null) {
    $message = "<p style='color:red;'>Could not find your account ID. Please log out and log back in.</p>";
}

$MEALS_TABLE = "meals";

$mealsTableExists = false;
if ($currentUserId !== null) {
    $check = mysqli_query($db, "SHOW TABLES LIKE 'meals'");
    $mealsTableExists = $check && mysqli_num_rows($check) > 0;
}

if ($currentUserId !== null && isset($_POST['submit_return'])) {

    $orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
    $reason  = isset($_POST['reason']) ? trim($_POST['reason']) : "";
    $items   = isset($_POST['items']) && is_array($_POST['items']) ? array_map('intval', $_POST['items']) : [];

    if ($orderId <= 0) {
        $message = "<p style='color:red;'>Please select a valid order.</p>";
    } elseif (empty($items)) {
        $message = "<p style='color:red;'>Please select at least one item to return.</p>";
    } else {
        $stmt = mysqli_prepare($db, "SELECT order_id, status FROM orders WHERE order_id=? AND user_id=? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "ii", $orderId, $currentUserId);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $orderRow = $res ? mysqli_fetch_assoc($res) : null;
        mysqli_stmt_close($stmt);

        if (!$orderRow) {
            $message = "<p style='color:red;'>This order does not belong to your account.</p>";
        } else {
            if (count($items) > 0) {
                $placeholders = implode(',', array_fill(0, count($items), '?'));
                $types = str_repeat('i', count($items));

                $sql = "SELECT id FROM order_items WHERE order_id=? AND id IN ($placeholders)";
                $stmt = mysqli_prepare($db, $sql);
                $bindParams = [];
                $bindTypes = 'i' . $types;
                $bindParams[] = &$bindTypes;
                $bindParams[] = &$orderId;
                foreach ($items as $k => $val) {
                    $bindParams[] = &$items[$k];
                }
                call_user_func_array('mysqli_stmt_bind_param', array_merge([$stmt], $bindParams));

                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                $validIds = [];
                while ($row = mysqli_fetch_assoc($res)) {
                    $validIds[] = (int)$row['id'];
                }
                mysqli_stmt_close($stmt);

                sort($validIds);
                $itemsSorted = $items;
                sort($itemsSorted);

                if ($validIds !== $itemsSorted) {
                    $message = "<p style='color:red;'>Some selected items were invalid for this order.</p>";
                } else {
                    if ($orderRow['status'] === 'return_requested') {
                        $message = "<p style='color:red;'>A return for this order has already been requested.</p>";
                    } else {
                        $stmt = mysqli_prepare($db, "UPDATE orders SET status='return_requested' WHERE order_id=?");
                        mysqli_stmt_bind_param($stmt, "i", $orderId);
                        $ok1 = mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);
                        $itemsJson = json_encode(array_values($items));
                        $stmt = mysqli_prepare($db, "INSERT INTO returns (order_id, user_id, items_json, reason, status) VALUES (?, ?, ?, ?, 'return_requested')");
                        mysqli_stmt_bind_param($stmt, "iiss", $orderId, $currentUserId, $itemsJson, $reason);
                        $ok2 = mysqli_stmt_execute($stmt);
                        $err  = $ok2 ? "" : mysqli_error($db);
                        mysqli_stmt_close($stmt);

                        if ($ok1 && $ok2) {
                            $message = "<p style='color:green;'>Return request submitted. Staff will review it soon.</p>";
                        } else {
                            mysqli_query($db, "UPDATE orders SET status='pending' WHERE order_id=".(int)$orderId);
                            $message = "<p style='color:red;'>Something went wrong submitting your request. $err</p>";
                        }
                    }
                }
            } else {
                $message = "<p style='color:red;'>Please select items to return.</p>";
            }
        }
    }
}

$userOrders = [];
if ($currentUserId !== null) {
    $stmt = mysqli_prepare($db, "SELECT order_id, status, created_at, total_price FROM orders WHERE user_id=? ORDER BY created_at DESC");
    mysqli_stmt_bind_param($stmt, "i", $currentUserId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($res)) {
        $userOrders[] = $row;
    }
    mysqli_stmt_close($stmt);
}


$selectedOrderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
if ($selectedOrderId <= 0 && !empty($userOrders)) {
    $selectedOrderId = 0;
}

$orderItems = [];
if ($selectedOrderId > 0) {
    if ($mealsTableExists) {
        $sql = "SELECT oi.id, oi.meal_id, oi.quantity, m.name
                FROM order_items oi
                LEFT JOIN $MEALS_TABLE m ON m.meal_id = oi.meal_id
                WHERE oi.order_id=?";
    } else {
        $sql = "SELECT oi.id, oi.meal_id, oi.quantity, NULL AS name
                FROM order_items oi
                WHERE oi.order_id=?";
    }
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "i", $selectedOrderId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($res)) {
        $orderItems[] = $row;
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Return - Dorm Diner</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .hint { color:#666; font-size:0.9rem; margin:6px 0 0; }
        .items-list { margin-top:10px; }
        .items-list label { display:flex; align-items:center; gap:8px; margin-bottom:8px; }
        .order-meta { color:#555; font-size:0.9rem; margin-top:6px; }
        .divider { height:1px; background:#eee; margin:12px 0; }
        .btn { background:#34769f; color:#fff; border:none; border-radius:8px; padding:10px 14px; cursor:pointer; }
        .btn:hover { background:#e6c38d; color:#000; }
        select, textarea { width:100%; padding:10px; border:1px solid #ccc; border-radius:8px; }
    </style>
</head>
<body>

<header>
    <img src="logo.jpeg" style="height: 110px;">
</header>

<main>
    <section id="login" style="max-width: 560px;">
        <h2>Request a Return</h2>
        <p>Select one of your orders and tick the specific items you want to return.</p>

        <form method="GET" action="">
            <div class="form-group">
                <label for="order_id_select">Choose Order</label>
                <select id="order_id_select" name="order_id" onchange="this.form.submit()">
                    <option value="">-- Select your order --</option>
                    <?php foreach ($userOrders as $o): 
                        $oid = (int)$o['order_id'];
                        $sel = ($selectedOrderId === $oid) ? "selected" : "";
                        $created = htmlspecialchars($o['created_at'] ?? '');
                        $status  = htmlspecialchars($o['status'] ?? '');
                        $price   = htmlspecialchars($o['total_price'] ?? '');
                    ?>
                        <option value="<?php echo $oid; ?>" <?php echo $sel; ?>>
                            #<?php echo $oid; ?> — £<?php echo $price; ?> — <?php echo $status; ?> — <?php echo $created; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="hint">This list only shows your own orders.</p>
            </div>
        </form>

        <div class="divider"></div>

        <?php if ($selectedOrderId > 0): ?>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>?order_id=<?php echo (int)$selectedOrderId; ?>">
                <input type="hidden" name="order_id" value="<?php echo (int)$selectedOrderId; ?>">

                <div class="form-group">
                    <label>Items in Order #<?php echo (int)$selectedOrderId; ?></label>
                    <div class="items-list">
                        <?php if (empty($orderItems)): ?>
                            <p>No items found for this order.</p>
                        <?php else: ?>
                            <?php foreach ($orderItems as $it): 
                                $label = $it['name'] ? ($it['name'] . " (x" . (int)$it['quantity'] . ")") 
                                                     : ("Meal #" . (int)$it['meal_id'] . " (x" . (int)$it['quantity'] . ")");
                            ?>
                                <label>
                                    <input type="checkbox" name="items[]" value="<?php echo (int)$it['id']; ?>">
                                    <span><?php echo htmlspecialchars($label); ?></span>
                                </label>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <p class="hint">Tick the specific items you want to return. (Full quantity per item will be considered.)</p>
                </div>

                <div class="form-group">
                    <label for="reason">Reason for return (optional)</label>
                    <textarea id="reason" name="reason" rows="3" placeholder="Write a brief reason..."></textarea>
                </div>

                <div class="form-group">
                    <input class="btn" type="submit" name="submit_return" value="Submit Return Request">
                </div>

                <div class="form-group">
                    <p><a href="index.php">Back to Home</a></p>
                </div>
            </form>
        <?php else: ?>
            <p class="hint">Pick an order from the dropdown to choose items.</p>
        <?php endif; ?>

        <?php if ($message) echo $message; ?>
    </section>
</main>

<footer>
    <p>&copy; 2025 Dorm Diner <a href="mailto:240146234@aston.ac.uk">Contact Us</a></p>
</footer>

</body>
</html>
