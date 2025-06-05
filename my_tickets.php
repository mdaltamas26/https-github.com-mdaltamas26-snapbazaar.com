<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo "Please login to view your support tickets.";
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM support_tickets WHERE user_id = '$user_id' ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Support Tickets</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        img { max-width: 100px; height: auto; }
    </style>
</head>
<body>

<h2>📩 My Support Tickets</h2>
<table>
    <tr>
        <th>Ticket ID</th>
        <th>Subject</th>
        <th>Message</th>
        <th>Image</th>
        <th>Status</th>
        <th>Admin Reply</th>
        <th>Created At</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td>#<?= $row['id']; ?></td>
            <td><?= htmlspecialchars($row['subject']); ?></td>
            <td><?= htmlspecialchars($row['message']); ?></td>
            <td>
                <?php if (!empty($row['image'])) { ?>
                    <a href="uploads/support_tickets/<?= $row['image']; ?>" target="_blank">
                        <img src="uploads/support_tickets/<?= $row['image']; ?>" alt="Ticket Image">
                    </a>
                <?php } else { echo "No Image"; } ?>
            </td>
            <td><?= $row['status']; ?></td>
            <td><?= !empty($row['admin_reply']) ? htmlspecialchars($row['admin_reply']) : "No Reply Yet"; ?></td>
            <td><?= $row['created_at']; ?></td>
        </tr>
    <?php } ?>
</table>

</body>
</html>