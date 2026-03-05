<?php
//connecting PHP to sql database 

//Database credentials 
$host = "localhost";
$user = "root";
$pass = "";
$db = "cs2team34_db";

//creating connection 
$conn = new mysqli ($host, $user, $pass, $db);

//checking connection 
if( $conn-> connect_error){
    die("database connection failed :(" . $conn->connect_error);
}

?>