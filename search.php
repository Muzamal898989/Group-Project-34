<?php
session_start();
if (isset($_GET['submitted'])){
    require_once('config/connectdb.php');

    $name = $_GET['search'];

    $statement = $db->prepare("SELECT * FROM meals WHERE name LIKE ?");
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
        <link rel="icon" type="image/png" href="images/favicon.png">
        <link rel = "stylesheet" type="text/css" href="css/header-style.css" />
        <link rel = "stylesheet" type="text/css" href="css/footer-style.css" />
        <link rel = "stylesheet" type="text/css" href="css/search-style.css" />
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
                    <input class="searchinput" type="search" name="search" placeholder="Find your next craving..." required>
                    <input type="hidden" name="submitted" value="true"/>
                </form>
                <nav class="header-nav">
                    <div class="shopping-cart">
                        <a href="ShoppingBasket.html"> <span class="basketicon material-symbols-outlined">shopping_cart</span> </a> <!--Placeholder URL-->
                    </div>
                    <a href="About-Us.html"> <img src="images/jukeboxicon.png" alt="About" class="jukebox"> </a> <!--Navigates the user to the about us page-->
                    <a href="login.php" class="signin-header"> Log In </a> <!--Placeholder URL and will add functionality to change the button to log out when signed in-->
                    <div class="profile-header">
                        <a href="Profile.html"> <span class="profileicon material-symbols-outlined">person</span> </a> <!--Placeholder URL-->
                    </div>
                </nav>
            </div>
        </header>
        <?php if (isset($recipes)): ?>
            <h2 id="search-header">Search Results:</h2>
            <?php if (!empty($recipes)): ?>
                <?php foreach ($recipes as $recipe): ?>
                    <?= htmlspecialchars($recipe['name']) ?><br>
                    <?= htmlspecialchars($recipe['category']) ?><br>
                    <?= htmlspecialchars($recipe['calories']) ?><br>
                    <?= htmlspecialchars($recipe['description']) ?><br>
                    <a href="meal.php?id=<?= $recipe['meal_id'] ?>">View</a>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>
    </body>
</html>