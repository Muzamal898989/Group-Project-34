<?php
//connecting PHP to sql database 

//Database credentials 
 
$host = "cs2410-web01pvm.aston.ac.uk";
$user = "cs2team34";
$pass = "IJx3HLbYg1PvUpQbYerU0Y4eo";
$db = "cs2team34_db";

/*
$host = "localhost";
$user = "root";
$pass = "";  // empty password for XAMPP
$dbname = "dorm_diner";  // your real local DB name
*/

//creating connection 
$conn = new mysqli ($host, $user, $pass, $db);

//checking connection 
if( $conn-> connect_error){
    die("database connection failed :(" . $conn->connect_error);
}

?>