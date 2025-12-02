<?php

// Connect to the database
function db_connect() {
    $connection = mysqli_connect("localhost", "root", "", "healthy_food_app");

    if (!$connection) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    return $connection;
}

// Validate email must end with .ac.uk
function verify_email($email) {
    if (preg_match("/@.*\.ac\.uk$/", $email)) {
        return true;
    }
    return false;
}

// Register a new user
function registerUser() {
    $db = db_connect();

    // Get form values safely
    $firstName = trim($_POST['firstName']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);

    // 1. Check required fields
    if (empty($firstName) || empty($email) || empty($password) || empty($confirmPassword)) {
        return "<p style='color:red;'>Please fill in all required fields.</p>";
    }

    // 2. Check password match
    if ($password !== $confirmPassword) {
        return "<p style='color:red;'>Passwords do not match.</p>";
    }

    // 3. Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "<p style='color:red;'>Invalid email format.</p>";
    }

    // 4. Validate .ac.uk domain
    if (!verify_email($email)) {
        return "<p style='color:red;'>Only student emails ending with .ac.uk are allowed.</p>";
    }

    // 5. Check if email already exists
    $check = mysqli_query($db, "SELECT * FROM users WHERE email = '$email'");
    if (mysqli_num_rows($check) > 0) {
        return "<p style='color:red;'>Email already in use</p>";
    }

    // 6. Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // 7. Insert user
    $sql_query = "INSERT INTO users (name, email, password, student_verified)
                  VALUES ('$firstName', '$email', '$hashedPassword', 1)";

    if (mysqli_query($db, $sql_query)) {
        return "<p style='color:green;'>Registered successfully!</p>";
    } else {
        return "<p style='color:red;'>Error: " . mysqli_error($db) . "</p>";
    }
}

// Login user
function loginUser() {
    $db = db_connect();

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "<p style='color:red;'>Invalid email format.</p>";
    }

    // Fetch user
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($db, $query);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['email'] = $email;

            header("Location: index.html");
            exit();
        }
    }

    return "<p style='color:red;'>Incorrect email or password.</p>";
}

?>
