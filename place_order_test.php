<?php 
session_start();
include 'includes/db_connect.php';

$user_id  = 1;

$cart = [
    ['meal_id' => 1, 'quantity' => 2],
     ['meal_id' => 3, 'quantity' => 1],
];


$total_calories = 0;



//loop cart items and sum calories
foreach($cart as $item){
    $meal_id = $item['meal_id'];
    $qty = $item['quantity'];

    //get calories from DB
    $stmt = $conn->prepare("SELECT calories FROM meals WHERE meal_id =?");
    $stmt->bind_param("i", $meal_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $meal = $result->fetch_assoc();

    if($meal){
        
        $item_calories = $meal['calories'] * $qty;
    }
}

echo "<p>Total calories: ", $total_calories . "kcal</p>";
?>