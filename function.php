<?php
session_start();
require_once 'config/db.php'; // Include the unified database connection

// Validate email must end with .ac.uk
function verify_email($email)
{
    if (preg_match("/@.*\.ac\.uk$/", $email)) {
        return true;
    }
    return false;
}

// Register a new user
function registerUser()
{
    //$db = db_connect();
    global $conn; // Use the global mysqli connection from db.php

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
    $check = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    if (mysqli_num_rows($check) > 0) {
        return "<p style='color:red;'>Email already in use</p>";
    }

    // 6. Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // 7. Insert user
    $sql_query = "INSERT INTO users (name, email, password, student_verified)
                  VALUES ('$firstName', '$email', '$hashedPassword', 1)";

    if (mysqli_query($conn, $sql_query)) {
        return "<p style='color:green;'>Registered successfully!</p>";
    } else {
        return "<p style='color:red;'>Error: " . mysqli_error($conn) . "</p>";
    }
}

// Login user
function loginUser()
{
   // $db = db_connect();
global $conn; // Use the global mysqli connection from db.php
   
$email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "<p style='color:red;'>Invalid email format.</p>";
    }

    // Fetch user
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['email'] = $email;

            return "success"; // <-- return success, no redirect here
        }
    }

    return "<p style='color:red;'>Incorrect email or password.</p>";
}
