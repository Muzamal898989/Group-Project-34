<?php
session_start();
include __DIR__ . '/config/db.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Menu Page - Ready Made Meals</title>
    <link rel="stylesheet" href="css/menu.css">
</head>

<body>

    <header class="page-header">
        <h1>Our Meals</h1>
        <p>Freshly prepared meals delivered straight to your door!</p>
    </header>

    <main class="menu-container">

        <!-- ========== CATEGORY TEMPLATE FUNCTION ========== -->
        <?php
        function renderCategory($pdo, $categoryName, $displayTitle)
        {
            echo "<section class='menu-category'>";
            echo "<h2>{$displayTitle}</h2>";
            echo "<div class='menu-grid'>";

            $stmt = $pdo->prepare("SELECT * FROM meals WHERE category = ? ORDER BY name");
            $stmt->execute([$categoryName]);

            foreach ($stmt as $meal) {
                echo "<a href='meal.php?id=" . $meal['meal_id'] . "' class='menu-link'>";
                echo "<div class='menu-item'>";


                // Image (load from images folder - assume it is named menu item name)
                echo "<img src='images/" . htmlspecialchars($meal['image']) . "' alt='" . htmlspecialchars($meal['name']) . "'>";

                echo "<div class='item-details'>";
                echo "<span class='item-price'>£" . number_format($meal['price'], 2) . "</span>";

                echo "<h3>" . htmlspecialchars($meal['name']) . "</h3>";
                echo "<p>" . htmlspecialchars($meal['description']) . "</p>";

                if ($meal['stock'] > 0) {
    				echo "<p class='in-stock'>In Stock</p>";
				} else {
    				echo "<p class='out-of-stock'>Out of Stock</p>";
				}

                echo "</div></div></a>";
            }

            echo "</div></section>";
        }
        ?>

        <!-- BREAKFAST-->
        <?php renderCategory($pdo, "breakfast", "Breakfast Options"); ?>

        <!-- LUNCH -->
        <?php renderCategory($pdo, "lunch", "Lunch Options"); ?>

        <!-- DINNER -->
        <?php renderCategory($pdo, "dinner", "Dinner Options"); ?>

        <!-- BENTO -->
        <?php renderCategory($pdo, "bento", "Dinner Options"); ?>

        <!-- SNACKS -->
        <?php renderCategory($pdo, "snack", "Snacks"); ?>

        <!-- DESSERTS -->
        <?php renderCategory($pdo, "dessert", "Snacks"); ?>

        <br><br>
        <a href="Home.html" style="text-decoration:none; color:#333; font-weight:bold;">
            Back to Homepage
        </a>

    </main>

</body>

</html>