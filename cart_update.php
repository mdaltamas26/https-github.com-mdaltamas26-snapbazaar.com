<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);

// Increase quantity
if (isset($_POST['increase'])) {
    $cart_id = intval($_POST['increase']);

    $update_query = "UPDATE cart SET quantity = quantity + 1 WHERE id = $cart_id AND user_id = $user_id";
    mysqli_query($conn, $update_query);
}

// Decrease quantity
if (isset($_POST['decrease'])) {
    $cart_id = intval($_POST['decrease']);

    // Get current quantity
    $qty_query = "SELECT quantity FROM cart WHERE id = $cart_id AND user_id = $user_id";
    $qty_result = mysqli_query($conn, $qty_query);

    if ($qty_result && mysqli_num_rows($qty_result) > 0) {
        $row = mysqli_fetch_assoc($qty_result);
        $current_qty = intval($row['quantity']);

        if ($current_qty <= 1) {
            // If quantity is 1 or less, remove the item from cart
            $delete_query = "DELETE FROM cart WHERE id = $cart_id AND user_id = $user_id";
            mysqli_query($conn, $delete_query);
        } else {
            // Otherwise, just reduce the quantity
            $update_query = "UPDATE cart SET quantity = quantity - 1 WHERE id = $cart_id AND user_id = $user_id";
            mysqli_query($conn, $update_query);
        }
    }
}

header("Location: cart.php");
exit();
