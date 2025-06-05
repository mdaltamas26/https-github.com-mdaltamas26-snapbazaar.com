<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch refund requests with total order amount
$query = "SELECT r.*, u.name, o.total_price 
          FROM refund_requests r
          JOIN users u ON r.user_id = u.id
          JOIN my_orders o ON r.order_id = o.id
          ORDER BY r.created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Refunds - SnapBazaar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Manage Refund Requests</h2>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <a href="dashboard.php" class="btn btn-secondary mb-3">&larr; Back to Dashboard</a>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
        <tr>
            <th>Refund ID</th>
            <th>User</th>
            <th>Order ID</th>
            <th>Amount</th>
            <th>Reason</th>
            <th>Status</th>
            <th>Requested At</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($refund = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $refund['id']; ?></td>
                <td><?= htmlspecialchars($refund['name']); ?></td>
                <td>#<?= $refund['order_id']; ?></td>
                <td>₹<?= number_format($refund['total_price'], 2); ?></td> <!-- FIXED AMOUNT DISPLAY -->
                <td><?= htmlspecialchars($refund['reason']); ?></td>
                <td>
                    <?php
                    $status = $refund['status'];
                    $badge = $status == 'pending' ? 'warning' : ($status == 'approved' ? 'success' : 'danger');
                    ?>
                    <span class="badge bg-<?= $badge ?> text-uppercase"><?= $status ?></span>
                </td>
                <td><?= date("d M Y, h:i A", strtotime($refund['created_at'])) ?></td>
                <td>
                    <?php if ($status == 'pending'): ?>
                        <form action="update_refund_status.php" method="POST" class="d-flex gap-2">
                            <input type="hidden" name="refund_id" value="<?= $refund['id'] ?>">
                            <button name="action" value="approve" class="btn btn-sm btn-success">Approve</button>
                            <button name="action" value="reject" class="btn btn-sm btn-danger">Reject</button>
                        </form>
                    <?php else: ?>
                        <span class="text-muted">No action</span>
                    <?php endif; ?>
                    <a href="admin_view_order.php?id=<?= $refund['order_id'] ?>" class="btn btn-sm btn-info mt-2">View</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
