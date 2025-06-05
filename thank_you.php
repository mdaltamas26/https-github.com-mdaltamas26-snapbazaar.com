<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thank You - SnapBazaar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container text-center mt-5">
    <h1 class="text-success">🎉 Thank You for Your Order!</h1>
    <p class="lead">Your order has been placed successfully. We will notify you once it’s out for delivery.</p>
    <a href="index.php" class="btn btn-primary mt-3">Continue Shopping</a>
    <a href="my_orders.php" class="btn btn-outline-secondary mt-3">View My Orders</a>
</div>
</body>
</html>
