<?php
session_start();
include 'db.php'; // Database connection ensure karo

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Error: Please log in to request a refund.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $order_id = intval($_POST['order_id']);
    $reason = mysqli_real_escape_string($conn, trim($_POST['reason']));
    
    // Validate input
    if (empty($order_id) || empty($reason)) {
        die("Error: All fields are required.");
    }

    // Check if the order exists and belongs to the user
    $order_query = mysqli_query($conn, "SELECT * FROM my_orders WHERE id = '$order_id' AND user_id = '$user_id'");
    if (mysqli_num_rows($order_query) === 0) {
        die("Error: Invalid order ID.");
    }

    // Check if refund request already exists
    $check_refund = mysqli_query($conn, "SELECT * FROM refunds WHERE order_id = '$order_id'");
    if (mysqli_num_rows($check_refund) > 0) {
        die("Error: Refund request already submitted for this order.");
    }

    // Insert refund request
    $query = "INSERT INTO refunds (order_id, user_id, reason, status, created_at) VALUES ('$order_id', '$user_id', '$reason', 'Pending', NOW())";
    if (mysqli_query($conn, $query)) {
        echo "Success: Refund request submitted.";
    } else {
        echo "Error: Could not submit refund request.";
    }
} else {
    die("Error: Invalid request.");
}
?>