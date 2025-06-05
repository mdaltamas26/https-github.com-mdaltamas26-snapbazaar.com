<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("⚠ Error: User not logged in.");
}

$user_id = intval($_SESSION['user_id']);
$transaction_id = isset($_POST['transaction_id']) ? trim($_POST['transaction_id']) : '';

if (strlen($transaction_id) !== 12 || !ctype_digit($transaction_id)) {
    die("⚠ Invalid Transaction ID.");
}

// Fetch Cart Items
$cart_query = "SELECT c.*, p.name, p.price FROM cart c 
               JOIN products p ON c.product_id = p.id 
               WHERE c.user_id = '$user_id'";
$cart_result = mysqli_query($conn, $cart_query);

if (!$cart_result) {
    die("⚠ Database Error: " . mysqli_error($conn));
}

if (mysqli_num_rows($cart_result) == 0) {
    die("⚠ Your cart is empty.");
}

$total_price = 0;
$order_items = [];

while ($row = mysqli_fetch_assoc($cart_result)) {
    $order_items[] = [
        'product_id' => $row['product_id'],
        'name' => $row['name'],
        'price' => $row['price'],
        'quantity' => $row['quantity']
    ];
    $total_price += $row['price'] * $row['quantity'];
}

// Convert order items to JSON safely
$order_data = mysqli_real_escape_string($conn, json_encode($order_items));
$order_date = date("Y-m-d H:i:s");

// Insert Order
$insert_order = "INSERT INTO my_orders (user_id, items, total_price, transaction_id, order_date, status) 
                 VALUES ('$user_id', '$order_data', '$total_price', '$transaction_id', '$order_date', 'Processing')";

if (mysqli_query($conn, $insert_order)) {
    // Clear Cart After Order
    mysqli_query($conn, "DELETE FROM cart WHERE user_id = '$user_id'");
    echo "✅ Order Placed Successfully!";
} else {
    echo "⚠ Error Placing Order: " . mysqli_error($conn);
}
?>