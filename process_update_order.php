<?php
session_start();

// Ensure db.php is included correctly
$path = __DIR__ . '/db.php';
if (file_exists($path)) {
    include $path;
} else {
    die("❌ Error: Database connection file not found!");
}

// Check if Admin is Logged In
if (!isset($_SESSION['admin_id'])) {
    die("❌ Error: Unauthorized access.");
}

// Validate POST Data
if (!isset($_POST['order_id']) || !isset($_POST['status'])) {
    die("❌ Error: Invalid request.");
}

$order_id = intval($_POST['order_id']);
$status = trim($_POST['status']);

// Ensure database connection is established
if (!isset($conn)) {
    die("❌ Error: Database connection not established.");
}

// Possible order statuses (to prevent invalid data)
$valid_statuses = ["Pending", "Shipped", "Delivered", "Cancelled"];
if (!in_array($status, $valid_statuses)) {
    die("❌ Error: Invalid order status.");
}

// Update Order Status in Database
$update_query = "UPDATE my_orders SET status = ? WHERE id = ?";
$stmt = mysqli_prepare($conn, $update_query);
mysqli_stmt_bind_param($stmt, "si", $status, $order_id);

if (mysqli_stmt_execute($stmt)) {
    echo "✅ Order updated successfully!";
    header("Location: orders.php");
    exit();
} else {
    echo "❌ Error: Unable to update order.";
}

mysqli_stmt_close($stmt);
?>