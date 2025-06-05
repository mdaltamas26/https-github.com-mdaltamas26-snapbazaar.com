<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = intval($_POST['order_id']);
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);
    
    // Check if order belongs to the user
    $check_order = "SELECT * FROM my_orders WHERE id = '$order_id' AND user_id = '$user_id'";
    $result = mysqli_query($conn, $check_order);

    if (mysqli_num_rows($result) == 0) {
        die("Invalid order.");
    }

    // Insert return request
    $insert_query = "INSERT INTO returns (user_id, order_id, reason, status) 
                     VALUES ('$user_id', '$order_id', '$reason', 'Pending')";
    if (mysqli_query($conn, $insert_query)) {
        header("Location: my_orders.php?success=Return request submitted.");
    } else {
        die("Error processing request.");
    }
}
?>