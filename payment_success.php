<?php
include 'db.php'; // DB connection

// Check if order_id is passed via URL
if (!isset($_GET['order_id'])) {
    echo "Order ID missing!";
    exit;
}

$order_id = intval($_GET['order_id']); // Safely convert to int

// Update payment_status in database
$stmt = $conn->prepare("UPDATE my_orders SET payment_status = 'paid' WHERE id = ?");
$stmt->bind_param("i", $order_id);
if ($stmt->execute()) {
    $message = "Payment status updated successfully.";
} else {
    $message = "Failed to update payment status.";
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="alert alert-info text-center shadow">
        <h4 class="mb-3"><?= $message ?></h4>
        <a href="my_orders.php" class="btn btn-success">Go to My Orders</a>
    </div>
</div>
</body>
</html>
