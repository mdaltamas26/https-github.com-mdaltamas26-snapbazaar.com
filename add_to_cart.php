<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "⚠ Please login first!";
    exit();
}

$user_id = intval($_SESSION['user_id']); // Ensure user_id is an integer

// ✅ Accept both POST and GET Requests
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : (isset($_GET['id']) ? intval($_GET['id']) : 0);
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1; // Default Quantity = 1

if ($product_id <= 0 || $quantity <= 0) {
    echo "⚠ Invalid Product or Quantity!";
    exit();
}

// 🔍 Check if Product exists
$product_check = mysqli_prepare($conn, "SELECT id FROM products WHERE id = ?");
mysqli_stmt_bind_param($product_check, "i", $product_id);
mysqli_stmt_execute($product_check);
mysqli_stmt_store_result($product_check);

if (mysqli_stmt_num_rows($product_check) == 0) {
    echo "⚠ Product does not exist!";
    exit();
}
mysqli_stmt_close($product_check);

// 🔄 Check if Product is already in the cart
$check_cart = mysqli_prepare($conn, "SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
mysqli_stmt_bind_param($check_cart, "ii", $user_id, $product_id);
mysqli_stmt_execute($check_cart);
mysqli_stmt_store_result($check_cart);

if (mysqli_stmt_num_rows($check_cart) > 0) {
    // 🛒 Update Quantity in Cart
    $update_cart = mysqli_prepare($conn, "UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?");
    mysqli_stmt_bind_param($update_cart, "iii", $quantity, $user_id, $product_id);
    mysqli_stmt_execute($update_cart);
    mysqli_stmt_close($update_cart);
} else {
    // 🛍 Add New Product to Cart
    $insert_cart = mysqli_prepare($conn, "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($insert_cart, "iii", $user_id, $product_id, $quantity);
    mysqli_stmt_execute($insert_cart);
    mysqli_stmt_close($insert_cart);
}

// ✅ Redirect to Cart Page
header("Location: cart.php");
exit();
?>