<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$ticket_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$query = "SELECT * FROM support_tickets WHERE id = '$ticket_id' AND user_id = '$user_id'";
$result = mysqli_query($conn, $query);
$ticket = mysqli_fetch_assoc($result);

if (!$ticket) {
    echo "Ticket not found!";
    exit();
}

// Fetch replies (just showing reply message & timestamp)
$reply_query = "SELECT * FROM support_replies WHERE ticket_id = '$ticket_id' ORDER BY created_at ASC";
$reply_result = mysqli_query($conn, $reply_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Ticket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<h2>📩 Ticket Details</h2>
<div class="card p-3 mb-4">
    <p><strong>Subject:</strong> <?= htmlspecialchars($ticket['subject']); ?></p>
    <p><strong>Message:</strong> <?= nl2br(htmlspecialchars($ticket['message'])); ?></p>
    <p><strong>Status:</strong> <?= htmlspecialchars($ticket['status']); ?></p>
    <p><strong>Created At:</strong> <?= $ticket['created_at']; ?></p>
    <?php if (!empty($ticket['image'])): ?>
        <p><strong>Uploaded Image:</strong></p>
        <img src="uploads/<?= htmlspecialchars($ticket['image']); ?>" alt="Image" style="max-width:300px;">
    <?php endif; ?>
</div>

<h4>📢 Admin Replies</h4>
<div class="card p-3">
    <?php if (mysqli_num_rows($reply_result) > 0): ?>
        <?php while ($reply = mysqli_fetch_assoc($reply_result)): ?>
            <p><strong>Admin:</strong> <?= nl2br(htmlspecialchars($reply['reply_message'])); ?><br>
            <small class="text-muted"><?= $reply['created_at']; ?></small></p>
            <hr>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No replies yet.</p>
    <?php endif; ?>
</div>

<a href="profile.php" class="btn btn-primary mt-4">⬅ Back to Profile</a>

</body>
</html>
