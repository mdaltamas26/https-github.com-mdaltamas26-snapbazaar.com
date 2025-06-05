<?php
session_start();
include 'db_connect.php';

// Only allow admin access
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Reply submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_id'], $_POST['reply_message'])) {
    $ticket_id = $_POST['ticket_id'];
    $reply_message = mysqli_real_escape_string($conn, $_POST['reply_message']);
    $admin_id = $_SESSION['admin_id'];

    $insert = "INSERT INTO support_replies (ticket_id, reply_message, admin_id) 
               VALUES ('$ticket_id', '$reply_message', '$admin_id')";
    mysqli_query($conn, $insert);
}

// Fetch all tickets
$tickets = mysqli_query($conn, "SELECT st.*, u.name, u.email FROM support_tickets st JOIN users u ON st.user_id = u.id ORDER BY st.created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Support Tickets</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <a href="dashboard.php" class="btn btn-secondary mb-3">&larr; Back to Dashboard</a>
</head>
<body class="container mt-5">
    <h2>🎫 Manage Support Tickets</h2>

    <?php while ($ticket = mysqli_fetch_assoc($tickets)) { 
        $ticket_id = $ticket['id'];
        $replies = mysqli_query($conn, "SELECT * FROM support_replies WHERE ticket_id = '$ticket_id' ORDER BY created_at ASC");
    ?>
        <div class="card mt-4">
            <div class="card-header">
                <strong><?= htmlspecialchars($ticket['subject']); ?></strong> - by <?= htmlspecialchars($ticket['name']); ?> (<?= htmlspecialchars($ticket['email']); ?>)
            </div>
            <div class="card-body">
                <p><strong>Message:</strong> <?= nl2br(htmlspecialchars($ticket['message'])); ?></p>

                <?php if (!empty($ticket['file_path'])) { ?>
                    <p><strong>Attachment:</strong><br>
                    <a href="<?= htmlspecialchars($ticket['file_path']); ?>" target="_blank" class="btn btn-sm btn-info">View File</a></p>
                <?php } ?>

                <p><strong>Status:</strong> <?= $ticket['status']; ?> | 
                   <strong>Created At:</strong> <?= date('d M Y, H:i', strtotime($ticket['created_at'])); ?>
                </p>

                <h5 class="mt-3">🗨 Admin Replies:</h5>
                <?php if (mysqli_num_rows($replies) > 0) {
                    while ($reply = mysqli_fetch_assoc($replies)) { ?>
                        <div class="border p-2 mb-2">
                            <?= nl2br(htmlspecialchars($reply['reply_message'])); ?><br>
                            <small class="text-muted"><?= date('d M Y, H:i', strtotime($reply['created_at'])); ?></small>
                        </div>
                    <?php }
                } else {
                    echo "<p>No replies yet.</p>";
                } ?>

                <form method="post" class="mt-3">
                    <input type="hidden" name="ticket_id" value="<?= $ticket['id']; ?>">
                    <div class="mb-2">
                        <textarea name="reply_message" class="form-control" rows="3" placeholder="Write your reply..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Reply</button>
                </form>
            </div>
        </div>
    <?php } ?>
</body>
</html>