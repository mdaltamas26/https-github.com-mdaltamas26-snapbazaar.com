<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    exit("Unauthorized");
}

if (!isset($_GET['id'])) {
    exit("Missing message ID");
}

$id = intval($_GET['id']);

// Fetch original message
$res = mysqli_query($conn, "SELECT * FROM messages WHERE id = '$id'");
if ($msg = mysqli_fetch_assoc($res)) {
    $original_sender = $msg['sender_id'];
    $original_receiver = $msg['receiver_id'];
    $original_msg = mysqli_real_escape_string($conn, $msg['message']);
    $original_time = $msg['timestamp'];

    // Move to deleted_messages
    mysqli_query($conn, "
        INSERT INTO deleted_messages 
        (original_sender_id, original_receiver_id, message, original_timestamp, deleted_at)
        VALUES ('$original_sender', '$original_receiver', '$original_msg', '$original_time', NOW())
    ");

    // Delete from messages
    mysqli_query($conn, "DELETE FROM messages WHERE id = '$id'");
    echo "Message deleted";
} else {
    echo "Message not found";
}
?>
