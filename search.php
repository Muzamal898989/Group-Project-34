<?php
session_start();
if (isset($_GET['submitted'])){
    require_once('config/db.php');

    $name = $_GET['search'];

    $statement = $db->prepare("SELECT * FROM recipes WHERE name LIKE ?");
    $statement->execute(["%$name%"]);
    $recipes = $statement->fetchAll();    

    if (!$recipes) {
        echo "No recipes exist that fit the search categories";
    }
}
?>

<!DOCTYPE HTML>
<html>
    <head>
        <title> Search </title>
    </head>
    <body>
        <header>

        </header>
        <?php if (isset($recipes)): ?>
            <h2>Search Results:</h2>
            <?php if (!empty($recipes)): ?>
                <?php foreach ($recipes as $recipe): ?>
                    <?= htmlspecialchars($recipe['name']) ?><br>
                    <?= htmlspecialchars($recipe['category']) ?><br>
                    <?= htmlspecialchars($recipe['calories']) ?><br>
                    <?= htmlspecialchars($recipe['description']) ?><br>
                    <a href="meal.php?meal_id=<?= $recipe['meal_id'] ?>">View</a>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>
    </body>
</html>