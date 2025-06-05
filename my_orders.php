<?php
session_start();
include 'db.php'; // Database connection file

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch orders for logged-in user
$sql = "SELECT o.id AS order_id, o.total_price, o.payment_status, o.order_status, o.tracking_id, o.created_at, p.name AS product_name, p.image AS product_image 
        FROM my_orders o
        JOIN products p ON o.product_id = p.id
        WHERE o.user_id = ?
        ORDER BY o.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?><!DOCTYPE html><html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - SnapBazaar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
</head>
<body>
    <div class="container mt-4">
        <h2 class="mb-4">My Orders</h2>
        <a href="home.php" class="btn btn-secondary btn-back">&lAarr; Back to Home</a>
        <?php if ($result->num_rows > 0): ?>
            <div class="list-group">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="list-group-item list-group-item-action mb-3 p-3 border rounded shadow-sm">
                        <div class="d-flex align-items-center">
                            <img src="uploads/<?= htmlspecialchars($row['product_image']) ?>" alt="Product Image" width="80" height="80" class="rounded me-3">
                            <div>
                                <h5 class="mb-1"> <?= htmlspecialchars($row['product_name']) ?> </h5>
                                <p class="mb-1">Order ID: #<?= $row['order_id'] ?></p>
                                <p class="mb-1 text-muted">Total: ₹<?= number_format($row['total_price'], 2) ?></p>
                                <p class="mb-1">
                                    Status: <span class="badge bg-<?= $row['order_status'] == 'processing' ? 'warning' : 'success' ?>">
                                        <?= ucfirst($row['order_status']) ?>
                                    </span>
                                    <?php if (!empty($row['tracking_id'])): ?>
                                        | Tracking ID: <strong><?= $row['tracking_id'] ?></strong>
                                    <?php endif; ?>
                                </p>
                                <small class="text-muted">Ordered on: <?= date('d M Y, h:i A', strtotime($row['created_at'])) ?></small>
                                <br>
                                <a href="order_details.php?order_id=<?= $row['order_id']; ?>" class="btn btn-primary btn-sm mt-2">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-muted">You have no orders yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>