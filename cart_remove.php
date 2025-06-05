<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['cart_id'])) {
    $cart_id = intval($_GET['cart_id']);

    $delete_query = "DELETE FROM cart WHERE id = '$cart_id' AND user_id = '" . $_SESSION['user_id'] . "'";
    if (mysqli_query($conn, $delete_query)) {
        $_SESSION['message'] = "Item removed successfully!";
    } else {
        $_SESSION['error'] = "Failed to remove item!";
    }
}

header("Location: checkout.php");
exit();