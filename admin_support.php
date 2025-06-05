<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    die("❌ Access Denied");
}

$result = mysqli_query($conn, "SELECT * FROM support_tickets ORDER BY created_at DESC");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_ticket'])) {
    $ticket_id = $_POST['ticket_id'];
    $status = $_POST['status'];
    
    mysqli_query($conn, "UPDATE support_tickets SET status='$status', updated_at=NOW() WHERE id='$ticket_id'");
    header("Refresh:0");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Support Panel</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h2>📊 Manage Support Tickets</h2>
<table border="1">
    <tr>
        <th>Ticket ID</th>
        <th>User ID</th>
        <th>Subject</th>
        <th>Message</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?= $row['id']; ?></td>
            <td><?= $row['user_id']; ?></td>
            <td><?= $row['subject']; ?></td>
            <td><?= $row['message']; ?></td>
            <td><?= $row['status']; ?></td>
            <td>
                <form method="POST">
                    <input type="hidden" name="ticket_id" value="<?= $row['id']; ?>">
                    <select name="status">
                        <option value="Pending" <?= $row['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="In Progress" <?= $row['status'] == 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                        <option value="Resolved" <?= $row['status'] == 'Resolved' ? 'selected' : ''; ?>>Resolved</option>
                    </select>
                    <button type="submit" name="update_ticket">Update</button>
                </form>
            </td>
        </tr>
    <?php } ?>
</table>

</body>
</html>