<?php
include 'includes/db_connect.php';
$username = 'admin';
$plain_password = 'AdminPass123';
$hash = password_hash($plain_password, PASSWORD_DEFAULT);

$stmt= $conn->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $hash);
$stmt->execute();

if($stmt->affected_rows >0){
    echo "admin created successfully WHOOP!";
    }
    else{
        echo "failed to create admin DUN DUN DUNNNN";

    }
    $stmt->close();
    $conn->close();

?>