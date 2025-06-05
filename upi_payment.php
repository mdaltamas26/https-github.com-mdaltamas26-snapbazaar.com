<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$total = isset($_GET['amount']) ? floatval($_GET['amount']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pay via UPI - SnapBazaar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container text-center mt-5">
    <h2>🧾 Pay Using UPI</h2>
    <p class="lead">Scan the QR code below to complete the payment of <strong>₹<?= number_format($total, 2) ?></strong></p>
    <img src="images/upi_qr.png" alt="UPI QR Code" width="300" class="mb-4">
    
    <form action="place_order.php" method="POST">
        <input type="hidden" name="payment_method" value="upi">
        <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">
        <input type="hidden" name="total_amount" value="<?= $total ?>">
        <button type="submit" class="btn btn-success">✅ I have paid, Confirm Order</button>
    </form>
</div>
</body>
</html>
