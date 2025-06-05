<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Fetch all return requests
$returns_query = "SELECT r.*, u.name, o.product_id, p.name AS product_name FROM returns r 
                  JOIN users u ON r.user_id = u.id
                  JOIN my_orders o ON r.order_id = o.id
                  JOIN products p ON o.product_id = p.id
                  ORDER BY r.id DESC";
$returns_result = mysqli_query($conn, $returns_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Return Requests - Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Return Requests</h2>
    <table>
        <tr>
            <th>User</th>
            <th>Product</th>
            <th>Reason</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($returns_result)) { ?>
        <tr>
            <td><?= $row['name'] ?></td>
            <td><?= $row['product_name'] ?></td>
            <td><?= $row['reason'] ?></td>
            <td><?= $row['status'] ?></td>
            <td>
                <?php if ($row['status'] == 'Pending') { ?>
                    <a href="update_return_status.php?id=<?= $row['id'] ?>&status=Approved">Approve</a> | 
                    <a href="update_return_status.php?id=<?= $row['id'] ?>&status=Rejected">Reject</a>
                <?php } else { ?>
                    <?= $row['status'] ?>
                <?php } ?>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>