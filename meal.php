<?php
session_start();
include __DIR__ . '/config/db.php';

if (!isset($_GET['id'])) {
    die("Meal not found.");
}

$meal_id = (int) $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM meals WHERE meal_id = ?");
$stmt->execute([$meal_id]);
$meal = $stmt->fetch();

if (!$meal) {
    die("Meal not found.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($meal['name']); ?></title>
    <link rel="stylesheet" href="css/meal.css">
</head>

<body>

    <header class="page-header">
        <h1><?php echo htmlspecialchars($meal['name']); ?> - <?php echo $meal['calories']; ?> kcal</h1>
        <p class="meal-desc"><?php echo htmlspecialchars($meal['description']); ?></p>
    </header>

    <main>
        <div class="meal-card">

            <!-- IMAGE -->
            <div class="meal-image">
                <img src="images/<?php echo htmlspecialchars($meal['image']); ?>"
                    alt="<?php echo htmlspecialchars($meal['name']); ?>">
            </div>

            <!-- DETAILS -->
            <div class="meal-info">

                <span class="meal-price">£<?php echo number_format($meal['price'], 2); ?></span>

                <!-- Add to Basket -->
                <?php if ($meal['stock'] > 0): ?>
                    <form class="add-form" method="POST" action="add_to_basket.php">
                        <input type="hidden" name="meal_id" value="<?php echo $meal['meal_id']; ?>">
                        <label>Qty:</label>
                        <input type="number" name="quantity" value="1" min="1">
                        <button type="submit">Add to Basket</button>
                    </form>
                <?php else: ?>
                    <p class="out-of-stock">Out of Stock</p>
                <?php endif; ?>

                <a class="back-link" href="menu.php">← Back to menu</a>
            </div>

            <!-- NUTRITION TABLE -->
            <div class="nutrition-box">
                <h2>Nutritional Information</h2>

                <table class="nutrition-table">
                    <tr>
                        <th>Protein</th>
                        <td><?php echo $meal['protein']; ?> g</td>
                    </tr>
                    <tr>
                        <th>Carbohydrates</th>
                        <td><?php echo $meal['carbs']; ?> g</td>
                    </tr>
                    <tr>
                        <th>of which sugars</th>
                        <td><?php echo $meal['sugars']; ?> g</td>
                    </tr>
                    <tr>
                        <th>Fat</th>
                        <td><?php echo $meal['fat']; ?> g</td>
                    </tr>
                    <tr>
                        <th>of which saturates</th>
                        <td><?php echo $meal['saturates']; ?> g</td>
                    </tr>
                    <tr>
                        <th>Fibre</th>
                        <td><?php echo $meal['fiber']; ?> g</td>
                    </tr>
                    <tr>
                        <th>Salt</th>
                        <td><?php echo $meal['salt']; ?> g</td>
                    </tr>
                </table>
            </div>

        </div>
    </main>

</body>

</html>