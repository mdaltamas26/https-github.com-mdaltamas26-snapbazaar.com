<?php
session_start();

// Correctly include db.php
$path = __DIR__ . '/db.php'; // Adjusted path
if (file_exists($path)) {
    include $path;
} else {
    die("Error: Database connection file not found!");
}

// Check if Admin is Logged In
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Ensure database connection is established
if (!isset($conn)) {
    die("Error: Database connection not established.");
}

// Fetch Orders
$orders = mysqli_query($conn, "SELECT * FROM my_orders ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>📦 Manage Orders</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>User ID</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = mysqli_fetch_assoc($orders)) { ?>
                    <tr>
                        <td><?= htmlspecialchars($order['id']) ?></td>
                        <td><?= htmlspecialchars($order['user_id']) ?></td>
                        <td>₹<?= number_format((float)$order['total_price'], 2) ?></td>
                        <td><?= htmlspecialchars($order['status']) ?></td>
                        <td>
                            <a href="update_order.php?id=<?= urlencode($order['id']) ?>" class="btn btn-primary btn-sm">Update</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <a href="dashboard.php" class="btn btn-dark">Back</a>
    </div>
</body>
</html>