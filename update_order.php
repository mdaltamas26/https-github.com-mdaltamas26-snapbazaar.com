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
    header("Location: login.php");
    exit();
}

// Validate Order ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("❌ Error: Invalid Order ID.");
}

$order_id = intval($_GET['id']);

// Ensure database connection is established
if (!isset($conn)) {
    die("❌ Error: Database connection not established.");
}

// Fetch Order
$order_query = mysqli_query($conn, "SELECT * FROM my_orders WHERE id = $order_id");
$order = mysqli_fetch_assoc($order_query);

if (!$order) {
    die("❌ Error: Order not found.");
}

// Possible order statuses
$order_statuses = ["Pending", "Shipped", "Delivered", "Cancelled"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Order</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>📝 Update Order #<?= htmlspecialchars($order['id']) ?></h2>
        <form action="process_update_order.php" method="POST">
            <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['id']) ?>">
            <div class="mb-3">
                <label class="form-label">Order Status</label>
                <select class="form-control" name="status">
                    <?php foreach ($order_statuses as $status) { ?>
                        <option value="<?= $status ?>" <?= $order['status'] == $status ? 'selected' : '' ?>>
                            <?= $status ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Update Order</button>
            <a href="orders.php" class="btn btn-secondary">Back</a>
        </form>
    </div>
</body>
</html>