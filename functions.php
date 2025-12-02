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

    $firstName = $_POST['firstName'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "<p style='color:red;'>Invalid email format.</p>";
    }

    // Validate aston.ac.uk email
    if (!verify_email($email)) {
        return "<p style='color:red;'>Only student emails ending with aston.ac.uk are allowed.</p>";
    }

    // Check if email already exists
    $check = mysqli_query($db, "SELECT * FROM users WHERE email = '$email'");
    if (mysqli_num_rows($check) > 0) {
        return "<p style='color:red;'>Email already in use</p>";
    }

    // Hash password securely
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert query
    $sql_query = "INSERT INTO users (name, email, password, student_verified)
                  VALUES ('$firstName', '$email', '$hashedPassword', 1)";

    if (mysqli_query($db, $sql_query)) {
        return "<p style='color:green;'>Registered successfully!</p>";
    } else {
        return "<p style='color:red;'>Error registering user: " . mysqli_error($db) . "</p>";
    }
}

// Login user
function loginUser() {
    $db = db_connect();

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "<p style='color:red;'>Invalid email format.</p>";
    }

    // Fetch user
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($db, $query);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        // Verify hashed password
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
