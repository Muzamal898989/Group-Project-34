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

this might work idk
<?php
$host = "localhost";
$user = "root";
$pass = "";          // leave empty if you never set one
$db   = "dorm_diner";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("DB failed: " . $conn->connect_error);
}
?>