<?php
session_start();
include 'db.php'; // Database Connection

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch Orders
$orders_query = "SELECT orders.*, users.name, users.email, return_requests.status AS return_status, return_requests.reason
                 FROM orders 
                 JOIN users ON orders.user_id = users.id
                 LEFT JOIN return_requests ON orders.id = return_requests.order_id
                 ORDER BY orders.created_at DESC";
$orders_result = mysqli_query($conn, $orders_query);

// Update Order Status
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    
    mysqli_query($conn, "UPDATE orders SET status='$new_status' WHERE id='$order_id'");
    $_SESSION['success'] = "Order status updated successfully!";
    header("Location: admin_orders.php");
    exit();
}

// Process Return Requests
if (isset($_POST['update_return_status'])) {
    $order_id = $_POST['order_id'];
    $return_status = $_POST['return_status'];
    
    mysqli_query($conn, "UPDATE return_requests SET status='$return_status' WHERE order_id='$order_id'");
    
    if ($return_status == "Approved") {
        mysqli_query($conn, "UPDATE orders SET status='Return Approved' WHERE id='$order_id'");
    } elseif ($return_status == "Rejected") {
        mysqli_query($conn, "UPDATE orders SET status='Return Rejected' WHERE id='$order_id'");
    }

    $_SESSION['success'] = "Return request updated successfully!";
    header("Location: admin_orders.php");
    exit();
}

// Process Refund
if (isset($_POST['process_refund'])) {
    $order_id = $_POST['order_id'];
    
    mysqli_query($conn, "UPDATE orders SET refund_status='Refund Completed' WHERE id='$order_id'");
    
    $_SESSION['success'] = "Refund processed successfully!";
    header("Location: admin_orders.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Orders - SnapBazaar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">📦 Orders Management</h2>

    <?php if (isset($_SESSION['success'])) { ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php } ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>User</th>
                <th>Email</th>
                <th>Total</th>
                <th>Payment</th>
                <th>Transaction ID</th>
                <th>Status</th>
                <th>Return Status</th>
                <th>Refund Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($order = mysqli_fetch_assoc($orders_result)) { ?>
                <tr>
                    <td><?= $order['id']; ?></td>
                    <td><?= $order['name']; ?></td>
                    <td><?= $order['email']; ?></td>
                    <td>₹<?= number_format($order['total_amount'], 2); ?></td>
                    <td><?= $order['payment_method']; ?></td>
                    <td><?= $order['transaction_id'] ?: 'N/A'; ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
                            <select name="status" class="form-control">
                                <option value="Pending" <?= ($order['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="Processing" <?= ($order['status'] == 'Processing') ? 'selected' : ''; ?>>Processing</option>
                                <option value="Completed" <?= ($order['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                <option value="Cancelled" <?= ($order['status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-primary btn-sm mt-2">Update</button>
                        </form>
                    </td>

                    <!-- Return Status -->
                    <td>
                        <?php if ($order['return_status']) { ?>
                            <form method="POST">
                                <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
                                <select name="return_status" class="form-control">
                                    <option value="Pending" <?= ($order['return_status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Approved" <?= ($order['return_status'] == 'Approved') ? 'selected' : ''; ?>>Approved</option>
                                    <option value="Rejected" <?= ($order['return_status'] == 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                                </select>
                                <button type="submit" name="update_return_status" class="btn btn-warning btn-sm mt-2">Update</button>
                            </form>
                        <?php } else { ?>
                            <span class="badge bg-secondary">No Request</span>
                        <?php } ?>
                    </td>

                    <!-- Refund Status -->
                    <td>
                        <?php if ($order['return_status'] == 'Approved' && $order['refund_status'] != 'Refund Completed') { ?>
                            <form method="POST">
                                <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
                                <button type="submit" name="process_refund" class="btn btn-success btn-sm">Process Refund</button>
                            </form>
                        <?php } else { ?>
                            <span class="badge bg-info"><?= $order['refund_status'] ?: 'N/A'; ?></span>
                        <?php } ?>
                    </td>

                    <td>
                        <a href="order_details.php?order_id=<?= $order['id']; ?>" class="btn btn-info btn-sm">View</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>