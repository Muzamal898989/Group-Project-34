<?php
session_start();
if (isset($_GET['submitted'])){
    require_once('config/connectdb.php');

    $name = $_GET['search'] ?? '';
    $filter = $_GET['filter'] ?? 'relevance';
    $category = $_GET['categories'] ?? 'all_categories';

    $sql = "SELECT * FROM meals WHERE name LIKE ?";
    $parameters = ["%$name%"];

    if ($category !== 'all_categories') {
    $sql .= " AND category = ?";
    $parameters[] = $category;
    }

    switch ($filter) {
        case 'price_low_to_high':
            $sql .= " ORDER BY price ASC";
        break;

        case 'price_high_to_low':
            $sql .= " ORDER BY price DESC";
        break;

        case 'calories_low_to_high':
            $sql .= " ORDER BY calories ASC";
        break;

        case 'calories_high_to_low':
            $sql .= " ORDER BY calories DESC";
        break;
    }

    $statement = $db->prepare($sql);
    $statement->execute($parameters);
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
        <link rel="icon" type="image/png" href="images/favicon.png">
        <link rel = "stylesheet" type="text/css" href="css/header-style.css" />
        <link rel = "stylesheet" type="text/css" href="css/footer-style.css" />
        <link rel = "stylesheet" type="text/css" href="css/search.css" />
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=person,search,shopping_cart" />
        <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Google+Sans+Flex:opsz,wght@6..144,1..1000&display=swap" rel="stylesheet">
    </head>
    <body>
        <header>
            <div class="header">
                <a href="home.php">  <img src="images/LogoHeader.jpg" alt="Logo" id="logo-header"> </a> <!--Navigates the user to the home page-->
                <form class="search" method = "get" action="search.php"> <!--Users can use this to search the website for meals-->
                    <span class="searchicon material-symbols-outlined">search</span>
                    <input class="searchinput" type="search" name="search" placeholder="Find your next craving..." value="<?= htmlspecialchars($name ?? '') ?>" required>
                    <input type="hidden" name="submitted" value="true"/>
                </form>
                <nav class="header-nav">
                    <div class="shopping-cart">
                        <a href="basket.php"> <span class="basketicon material-symbols-outlined">shopping_cart</span> </a> <!--Placeholder URL-->
                    </div>
                    <a href="About-Us.html"> <img src="images/jukeboxicon.png" alt="About" class="jukebox"> </a> <!--Navigates the user to the about us page-->
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="logout.php" class="signin-header">Logout</a>
                    <?php else: ?>
                    <a href="login.php" class="signin-header">Sign In</a>
                    <?php endif; ?>
                    <div class="profile-header">
                        <a href="Profile.html"> <span class="profileicon material-symbols-outlined">person</span> </a> <!--Placeholder URL-->
                    </div>
                </nav>
            </div>
        </header>
        <?php if (isset($recipes)): ?>
            <div class="search-header">
                <h2>Search Results:</h2>
                <form class= "filter_search" method= "get" action= "search.php">
                    <input type="hidden" name="submitted" value="true">
                    <input type="hidden" name="search" value="<?= htmlspecialchars($name ?? '') ?>">
                    <select name="filter" id="filter_options" onchange="this.form.submit()">
                        <option value="relevance" <?= ($filter ?? '') === 'relevance' ? 'selected' : '' ?>>Sort By: Relevance</option>
                        <option value="price_low_to_high" <?= ($filter ?? '') === 'price_low_to_high' ? 'selected' : '' ?>>Sort By: Price (Low to High)</option>
                        <option value="price_high_to_low" <?= ($filter ?? '') === 'price_high_to_low' ? 'selected' : '' ?>>Sort By: Price (High to Low)</option>
                        <option value="calories_low_to_high" <?= ($filter ?? '') === 'calories_low_to_high' ? 'selected' : '' ?>>Sort By: Calories (Low to High)</option>
                        <option value="calories_high_to_low" <?= ($filter ?? '') === 'calories_high_to_low' ? 'selected' : '' ?>>Sort By: Calories (High to Low)</option>
                    </select>
            
                    <select name= "categories" id="category_options" onchange="this.form.submit()">
                        <option value="all_categories" <?= ($category ?? '') === 'all_categories' ? 'selected' : '' ?>>All Categories</option>
                        <option value="breakfast" <?= ($category ?? '') === 'breakfast' ? 'selected' : '' ?>>Breakfast</option>
                        <option value="lunch" <?= ($category ?? '') === 'lunch' ? 'selected' : '' ?>>Lunch</option>
                        <option value="dinner" <?= ($category ?? '') === 'dinner' ? 'selected' : '' ?>>Dinner</option>
                        <option value="snack" <?= ($category ?? '') === 'snack' ? 'selected' : '' ?>>Snack</option>
                        <option value="dessert" <?= ($category ?? '') === 'dessert' ? 'selected' : '' ?>>Dessert</option>
                        <option value="bento" <?= ($category ?? '') === 'bento' ? 'selected' : '' ?>>Bento</option>
                    </select>
                </form>
            </div>
            <div class="menu-grid">
                <?php if (!empty($recipes)): ?>
                    <?php foreach ($recipes as $recipe): ?>
                        <div class="menu-item">
                            <div class="item-details">
                                <?= htmlspecialchars($recipe['name']) ?><br>
                                <?= htmlspecialchars($recipe['category']) ?><br>
                                £<?= htmlspecialchars($recipe['price']) ?><br>
                                <?= htmlspecialchars($recipe['calories']) ?> calories<br>
                                <?= htmlspecialchars($recipe['description']) ?><br>
                                <a href="meal.php?id=<?= $recipe['meal_id'] ?>">View</a><br><br>
                            </div>
                        </div>  
                    <?php endforeach; ?>
                <?php endif; ?>          
            </div>
        <?php endif; ?>
    </body>
</html>