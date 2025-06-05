<?php
session_start();
include 'db.php'; // Database connection

if (!isset($_SESSION['user_id'])) {
    die("Please login first!");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];

    $sql = "DELETE FROM wishlist WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    
    if ($stmt->execute()) {
        echo "Removed from Wishlist!";
    } else {
        echo "Error!";
    }
}
?>