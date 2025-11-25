<?php
    // Connect to the database
    function db_connect() {
        $connection = mysqli_connect("localhost", "root", "", "healthy_food_app");
        if ($connection) {
            return $connection;
        } else {
            die("Database connection failed: " . mysqli_connect_error());
        }
    }

    // Validate email must end with .ac.uk
    function verify_email($email) {
        if (preg_match("/@.*\.ac\.uk$/", $email)) {
            return true;
        }
