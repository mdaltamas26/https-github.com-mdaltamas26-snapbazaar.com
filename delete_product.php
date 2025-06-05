<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);

    // Step 1: Check if this product exists in any order
    $check_order = $conn->query("SELECT * FROM order_items WHERE product_id = $product_id LIMIT 1");

    if ($check_order->num_rows > 0) {
        // Product used in order, can't delete
        header("Location: manage_products.php?error=Cannot delete. Product is part of an order.");
        exit();
    }

    // Step 2: Safe to delete
    $conn->query("DELETE FROM products WHERE id = $product_id");

    header("Location: manage_products.php?msg=Product deleted successfully.");
    exit();
} else {
    header("Location: manage_products.php?error=Invalid Product ID.");
    exit();
}
?>
