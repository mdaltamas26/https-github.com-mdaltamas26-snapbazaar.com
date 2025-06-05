<?php
session_start();
include 'db.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$result = mysqli_query($conn, "SELECT * FROM support_tickets WHERE user_id='$user_id' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support - SnapBazaar</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h2>📩 Submit a Support Ticket</h2>
<form action="submit_ticket.php" method="POST">
    <input type="text" name="subject" placeholder="Subject" required>
    <textarea name="message" placeholder="Describe your issue..." required></textarea>
    <button type="submit">Submit Ticket</button>
</form>

<h2>📜 Your Support Tickets</h2>
<table border="1">
    <tr>
        <th>Ticket ID</th>
        <th>Subject</th>
        <th>Message</th>
        <th>Status</th>
        <th>Created At</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?= $row['id']; ?></td>
            <td><?= $row['subject']; ?></td>
            <td><?= $row['message']; ?></td>
            <td><?= $row['status']; ?></td>
            <td><?= $row['created_at']; ?></td>
        </tr>
    <?php } ?>
</table>

</body>
</html>