<?php
//connecting PHP to sql database 

//Database credentials 
$host = "localhost";
$user = "root";
$pass = "";
$db = "dorm_diner";

//creating connection 
$conn = new mysqli ($host, $user, $pass, $db);

//checking connection 
if( $conn-> connect_error){
    die("database connection failed :(" . $conn->connect_error);
}

?>