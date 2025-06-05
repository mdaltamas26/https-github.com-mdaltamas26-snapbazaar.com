<?php
// Database connection
include 'db.php';

// Order ID fetch करो
$order_id = $_GET['id'];

// Order details fetch करो
$order_query = "SELECT my_orders.*, users.name, users.email FROM my_orders 
                JOIN users ON my_orders.user_id = users.id
                WHERE my_orders.id = $order_id";
$order_result = $conn->query($order_query);
$order = $order_result->fetch_assoc();

// Address fetch करो
$address_query = "SELECT * FROM addresses WHERE user_id = {$order['user_id']} ORDER BY is_default DESC, id ASC LIMIT 1";
$address_result = $conn->query($address_query);
$address = $address_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Order - SnapBazaar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); border: none; }
        .card-header { font-weight: bold; background-color: #007bff; color: white; }
        .btn-back, .btn-download { margin-bottom: 20px; }
    </style>
</head><body>
<div class="container mt-5">
<a href="manage_payments.php" class="btn btn-secondary btn-back">&larr; Back</a>

    <!-- Buttons (outside print area) -->
    <div class="d-flex justify-content-between mb-3">
        <a href="dashboard.php" class="btn btn-secondary btn-back">&larr; Back to Dashboard</a>
        <button class="btn btn-danger btn-download" onclick="downloadPDF()">⬇️ Download as PDF</button>
    </div>

    <!-- Printable Section -->
    <div id="printSection">
        <!-- Order Details -->
        <div class="card mt-4">
            <div class="card-header">Order Details</div>
            <div class="card-body">
                <p><strong>Order ID:</strong> <?= $order['id']; ?></p>
                <p><strong>User Name:</strong> <?= htmlspecialchars($order['name']); ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($order['email']); ?></p>
                <p><strong>Total Price:</strong> ₹<?= number_format($order['total_price'], 2); ?></p>
                <p><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method']); ?></p>
                <p><strong>Order Status:</strong> 
                    <span class="badge bg-<?= ($order['order_status'] == 'delivered') ? 'success' : (($order['order_status'] == 'pending') ? 'warning' : 'danger'); ?>">
                        <?= ucfirst($order['order_status']); ?>
                    </span>
                </p>
            </div>
        </div>

        <!-- Address Details -->
        <div class="card mt-3">
            <div class="card-header">Shipping Address</div>
            <div class="card-body">
                <?php if ($address): ?>
                    <p><strong>Full Name:</strong> <?= htmlspecialchars($address['full_name']); ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($address['phone']); ?></p>
                    <p><strong>Address:</strong> <?= htmlspecialchars($address['address']); ?></p>
                    <p><strong>City:</strong> <?= htmlspecialchars($address['city']); ?></p>
                    <p><strong>State:</strong> <?= htmlspecialchars($address['state']); ?></p>
                    <p><strong>Country:</strong> <?= htmlspecialchars($address['country']); ?></p>
                    <p><strong>Pincode:</strong> <?= htmlspecialchars($address['pincode']); ?></p>
                <?php else: ?>
                    <p class="text-danger">No address found for this order.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function downloadPDF() {
    const element = document.getElementById('printSection');
    html2pdf().from(element).set({
        margin: 10,
        filename: 'Order_<?= $order['id']; ?>.pdf',
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    }).save();
}
</script>
</body>
</html>
