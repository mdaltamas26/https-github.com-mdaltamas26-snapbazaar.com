<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = intval($_POST['order_id']);
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);
    $user_id = $_SESSION['user_id'];

    // Check if return request already exists
    $check_query = "SELECT * FROM returns WHERE order_id = '$order_id'";
    $check_result = mysqli_query($conn, $check_query);
    if (mysqli_num_rows($check_result) > 0) {
        header("Location: my_orders.php?error=Return request already exists.");
        exit();
    }

    // Insert return request
    $insert_query = "INSERT INTO returns (user_id, order_id, reason, status) VALUES ('$user_id', '$order_id', '$reason', 'Pending')";
    if (mysqli_query($conn, $insert_query)) {
        header("Location: my_orders.php?success=Return request submitted.");
    } else {
        header("Location: my_orders.php?error=Failed to submit request.");
    }
}
?>