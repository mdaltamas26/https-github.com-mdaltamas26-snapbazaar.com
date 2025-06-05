<?php
session_start();
include("db_connect.php"); // Database connection

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Debugging: Convert 'id' to 'ticket_id' if necessary
if (isset($_GET['id']) && !isset($_GET['ticket_id'])) {
    $_GET['ticket_id'] = $_GET['id']; 
}

// Check if ticket_id is provided
if (!isset($_GET['ticket_id']) || empty($_GET['ticket_id']) || !is_numeric($_GET['ticket_id'])) {
    die("❌ Error: Invalid or missing ticket_id in URL! Debug: " . var_export($_GET, true));
}

$ticket_id = intval($_GET['ticket_id']);

// Fetch ticket details
$query = $conn->prepare("SELECT * FROM support_tickets WHERE id = ?");
$query->bind_param("i", $ticket_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows == 0) {
    die("❌ Error: Ticket not found!");
}
$ticket = $result->fetch_assoc();

// Handle admin reply submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reply_message'])) {
    $reply_message = trim($_POST['reply_message']);
    $admin_id = $_SESSION['admin_id'];

    if (!empty($reply_message)) {
        $insert_query = $conn->prepare("INSERT INTO support_replies (ticket_id, admin_id, reply_text, created_at) VALUES (?, ?, ?, NOW())");
        $insert_query->bind_param("iis", $ticket_id, $admin_id, $reply_message);
        
        if ($insert_query->execute()) {
            echo "<script>alert('Reply sent successfully!'); window.location.href='admin_view_ticket.php?ticket_id=$ticket_id';</script>";
            exit();
        } else {
            echo "❌ Error: Failed to send reply.";
        }
    } else {
        echo "❌ Error: Reply message cannot be empty!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Support Ticket</title>
</head>
<body>
    <h2>Ticket Details</h2>
    <p><strong>Subject:</strong> <?php echo htmlspecialchars($ticket['subject']); ?></p>
    <p><strong>Message:</strong> <?php echo htmlspecialchars($ticket['message']); ?></p>
    <p><strong>Status:</strong> <?php echo htmlspecialchars($ticket['status']); ?></p>
    <p><strong>Created At:</strong> <?php echo $ticket['created_at']; ?></p>

    <h3>Replies</h3>
    <?php
    // Make sure table name and column names match your database structure
    $reply_query = $conn->prepare("SELECT r.reply_text, r.created_at, a.username AS admin_name 
                                    FROM support_replies r 
                                    JOIN admins a ON r.admin_id = a.id 
                                    WHERE r.ticket_id = ? ORDER BY r.created_at ASC");
    $reply_query->bind_param("i", $ticket_id);
    $reply_query->execute();
    $replies = $reply_query->get_result();

    if ($replies->num_rows > 0) {
        while ($reply = $replies->fetch_assoc()) {
            echo "<p><b>" . htmlspecialchars($reply['admin_name']) . " (Admin):</b> " . htmlspecialchars($reply['reply_text']) . " <i>[" . $reply['created_at'] . "]</i></p>";
        }
    } else {
        echo "<p>No replies yet.</p>";
    }
    ?>

    <h3>Send a Reply</h3>
    <form method="POST" action="">
        <textarea name="reply_message" required placeholder="Type your reply here"></textarea><br>
        <button type="submit">Send Reply</button>
    </form>
</body>
</html>