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
    
    //This function checks and login a user 

    function loginUser(){
        $username = $_POST['email'];
        $password = $_POST['password'];

        $hashed_password = md5($password);
        $check_user_query = "SELECT email, password FROM users WHERE email = '$username' AND password = '$hashed_password'";

         $db_connection = db_connect();

        $check_result = mysqli_query($db_connection, $check_user_query) or die("Failed to execute query".mysqli_error(db_connection));
        $num_rows = mysqli_num_rows($check_result);

        if($num_rows>0) {
            session_start();
            $_SESSION['email'] = $_POST["email"];
            header("location: index.html");
        } else{
            header("location: login.php");
        }
    }

    //This function checks the email extenstion
    function verify_email($email){
        $last_five = substr($email, -5);
        if($last_five == "ac.uk"){
            echo $last_five;
            //return true;
        } else {
            echo "wrong email";
            //return false;
        }
        return $last_five;
    }

    // This function will register a user 
    function registerUser(){
        $db_connection = db_connect();
        
        $verify_email = verify_email($_POST['email']);

        if($verify_email == "ac.uk" ){
            $hash_password = md5($_POST['password']);
            $sql_query = "INSERT INTO users(name,email,password,student_verified) VALUES('{$_POST['firstName']}', '{$_POST['email']}', '$hash_password','1' )";
            $query_result = mysqli_query($db_connection,$sql_query) or die("Error registering user" .mysqli_error($db_connection));
            
            if($query_result){
                return "<p style= 'color:green;'>Registered successfully</p>";
            }
        } else{
            return "<p style= 'color:red;'> invalid Email</p>";
        }
            
    }
?>