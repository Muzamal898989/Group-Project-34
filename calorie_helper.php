<?php
function calculate_cart_calories($conn, $cart){
    $total_calories = 0;
    foreach($cart as $item){
        $meal_id = $item['meal_id'];
        $qty = $item['quantity'];

        //get calories for this meal
        $stmt = $conn->prepare("SELECT calories FROM meals WHERE meal_id =?");
    $stmt->bind_param("i", $meal_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $meal = $result->fetch_assoc();

    if($meal){
        
        $item_calories = $meal['calories'] * $qty;
    }
}
return $total_calories;
}
//for basket php
/* include 'includes/db_connect.php';
include 'includes/calorie_helper.php';

$cart = $_SESSION['cart']; // their basket

$total_calories = calculate_cart_calories($conn, $cart);

echo "<p>Total Calories: {$total_calories} kcal</p>";
*/
?>