<?php
    // This function will connect to the database
    function db_connect() {
        $connection = mysqli_connect("localhost", "root", "", "healthy_food_app");
        if($connection){
            return $connection;
        } else {
            die("Could not connect to database because ". mysqli_error());
        }
    }
     
    // This function will register a user 
    function registerUser(){
        $db_connection = db_connect();

        $sql_query = "INSERT INTO users(name,email,password,student_verified) VALUES('{$_POST['firstName']}', '{$_POST['email']}', '{$_POST['password']}','0' )";
        $query_result = mysqli_query($db_connection,$sql_query) or die("Error registering user" .mysqli_error($db_connection));
        if($query_result){
            return "true";
        } else {
            return "false";
        }
    }
?>