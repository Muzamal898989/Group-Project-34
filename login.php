<?php
    require_once("functions.php");

    if(isset($_POST['login'])){
        $login_status = loginUser();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dorm Diner Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<header>
    <img src="logo.jpeg" style="height: 110px;">
</header>

<main>
    <section class="auth-card" aria-labelledby="authTitle">
        <h2 id="authTitle">Welcome To Dorm Diner</h2>
    </section>

    <section id="login">
        <h2>Login Here</h2>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" placeholder="Email" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Password" required>
            </div>

            <div class="form-group">
                <input type="submit" name="login" value="Login" class="btn btn-primary">
            </div>

            <div class="form-group">
                <p>Don't have an account? <a href="signUp.php">Sign Up Here</a></p>
            </div>
        </form>

        <?php
            if(isset($login_status)){
                echo $login_status;
            }
        ?>
    </section>
</main>

<footer>
    <p>&copy;2025 Dorm Diner <a href="mailto:240146234@aston.ac.uk">Contact Us</a></p>
</footer>

</body>
</html>
