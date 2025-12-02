<?php 
    require_once("functions.php");

    if (isset($_POST['register'])) {
        $registration_status = registerUser();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Dorm Diner</title>
    <link rel="stylesheet" href="css/style.css">
    <script defer src="auth.js"></script>
</head>

<body>

<header id="main-header">
    <div class="header-content">
        <img src="logo.jpeg" style="height: 110px;">
        <h1>Register Here for Dorm Diner Meals</h1>
    </div>
    <nav>
        <a href="Home.html" class="btn btn-secondary">Home</a>
        <a href="contact.html" class="btn btn-secondary">Contact</a>
        <a href="login.php" class="btn btn-secondary">Login</a>
    </nav>
</header>

<main>
    <section id="signUp-form">
        <h2>Create An Account</h2>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" id="registerForm">

            <div class="mb-3">
                <label for="firstName" class="form-label"> First Name </label>
                <input type="text" id="firstName" name="firstName" placeholder="Enter your first name" required>
            </div>

            <div class="mb-3">
                <label for="lastName" class="form-label">Last Name</label>
                <input type="text" id="lastName" name="lastName" placeholder="Enter your last name" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Student Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your .ac.uk email" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>

            <div class="mb-3">
                <label for="confirmPassword" class="form-label">Confirm Password</label>
                <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm your password" required>
            </div>

            <div class="mb-3">
                <label for="birthday" class="form-label">Birthday</label>
                <input type="date" id="birthday" name="birthday">
            </div>

            <div class="mb-3">
                <label><input type="checkbox" name="notificationType" value="sms"> Receive SMS notifications</label>
            </div>

            <div class="mb-3">
                <label><input type="checkbox" name="notificationType" value="email"> Receive Email notifications</label>
            </div>

            <button type="submit" class="btn btn-primary" name="register">Register</button>
        </form>

        <?php 
            if (isset($registration_status)) {
                echo $registration_status;
            }
        ?>
    </section>
</main>

<footer>
    <p>&copy; 2025 Dorm Diner <a href="mailto:240146234@aston.ac.uk">Contact Us</a></p>
</footer>

</body>
</html>
