<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch All Refund Requests
$refunds = mysqli_query($conn, "SELECT r.id, r.order_id, r.reason, r.status, u.name AS user_name 
                                FROM refunds r 
                                JOIN users u ON r.user_id = u.id 
                                WHERE r.status = 'Pending'");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $action = $_POST['action'];

    if ($action == "approve") {
        mysqli_query($conn, "UPDATE refunds SET status='Approved' WHERE id='$id'");
    } elseif ($action == "reject") {
        mysqli_query($conn, "UPDATE refunds SET status='Rejected' WHERE id='$id'");
    }

    header("Location: refunds.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Refunds</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>💰 Manage Refunds</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Request ID</th>
                    <th>Order ID</th>
                    <th>User</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($refund = mysqli_fetch_assoc($refunds)) { ?>
                    <tr>
                        <td><?= $refund['id'] ?></td>
                        <td><?= $refund['order_id'] ?></td>
                        <td><?= $refund['user_name'] ?></td>
                        <td><?= $refund['reason'] ?></td>
                        <td><?= $refund['status'] ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="id" value="<?= $refund['id'] ?>">
                                <button name="action" value="approve" class="btn btn-success btn-sm">Approve</button>
                                <button name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>