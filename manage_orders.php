<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

$query = "SELECT my_orders.id, my_orders.user_id, my_orders.total_price, my_orders.payment_method, my_orders.payment_status, my_orders.order_status, my_orders.transaction_id, users.name 
          FROM my_orders 
          JOIN users ON my_orders.user_id = users.id";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders - SnapBazaar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Manage Orders</h2>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php elseif (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <a href="dashboard.php" class="btn btn-secondary mb-3">&lAarr; Back to Dashboard</a>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Order ID</th>
                <th>User</th>
                <th>Total Price</th>
                <th>Payment Method</th>
                <th>Transaction ID</th>
                <th>Payment Status</th>
                <th>Order Status</th>
                <th>Update Status</th>
                <th>Change Payment</th>
                <th>View</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($order = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $order['id']; ?></td>
                    <td><?= htmlspecialchars($order['name']); ?></td>
                    <td>₹<?= number_format($order['total_price'], 2); ?></td>
                    <td><?= htmlspecialchars($order['payment_method']); ?></td>
                    <td><?= htmlspecialchars($order['transaction_id'] ?? '—'); ?></td>
                    <td>
                        <?php
                        $badgeClass = strtolower($order['payment_status']) == 'paid' ? 'bg-success' : (strtolower($order['payment_status']) == 'failed' ? 'bg-danger' : 'bg-warning');
                        ?>
                        <span class="badge <?= $badgeClass; ?>">
                            <?= ucfirst($order['payment_status']); ?>
                        </span>
                    </td>
                    <td><?= ucfirst($order['order_status']); ?></td>
                    <td>
                        <form action="update_order_status.php" method="POST" class="d-flex">
                            <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
                            <select name="order_status" class="form-select form-select-sm me-2">
                                <option value="pending" <?= $order['order_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="shipped" <?= $order['order_status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                <option value="delivered" <?= $order['order_status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                <option value="cancelled" <?= $order['order_status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary">Update</button>
                        </form>
                    </td>
                    <td>
                        <form action="update_payment_status.php" method="POST" class="d-flex">
                            <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
                            <input type="hidden" name="payment_method" value="<?= $order['payment_method']; ?>">
                            <select name="payment_status" class="form-select form-select-sm me-2">
                                <?php if (strtolower($order['payment_method']) == 'cod'): ?>
                                    <option value="pending" <?= $order['payment_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="paid" <?= $order['payment_status'] == 'paid' ? 'selected' : ''; ?>>Paid</option>
                                <?php else: ?>
                                    <option value="paid" <?= $order['payment_status'] == 'paid' ? 'selected' : ''; ?>>Paid</option>
                                    <option value="failed" <?= $order['payment_status'] == 'failed' ? 'selected' : ''; ?>>Failed</option>
                                <?php endif; ?>
                            </select>
                            <button type="submit" class="btn btn-sm btn-warning">Change</button>
                        </form>
                    </td>
                    <td>
                        <a href="view_order.php?id=<?= $order['id']; ?>" class="btn btn-sm btn-info">View</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
