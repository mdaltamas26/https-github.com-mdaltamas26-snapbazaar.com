<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "error";
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'];

// Check if product is already in wishlist
$check_query = "SELECT * FROM wishlist WHERE user_id = '$user_id' AND product_id = '$product_id'";
$check_result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($check_result) > 0) {
    // Remove from wishlist
    $delete_query = "DELETE FROM wishlist WHERE user_id = '$user_id' AND product_id = '$product_id'";
    mysqli_query($conn, $delete_query);
    echo "removed";
} else {
    // Add to wishlist
    $insert_query = "INSERT INTO wishlist (user_id, product_id) VALUES ('$user_id', '$product_id')";
    mysqli_query($conn, $insert_query);
    echo "added";
}
?>