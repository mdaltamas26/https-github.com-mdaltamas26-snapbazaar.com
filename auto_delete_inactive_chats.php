<?php
include 'db.php';

// Get current timestamp
$currentTime = time();

// Find all users (except admin id=0) who have been inactive for more than 60 seconds
$result = mysqli_query($conn, "SELECT id FROM users WHERE id != 0 AND UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(last_active) > 60");

while ($user = mysqli_fetch_assoc($result)) {
    $userId = $user['id'];

    // Get all messages between this user and admin
    $messages = mysqli_query($conn, "
        SELECT * FROM messages 
        WHERE (sender_id = 'admin' AND receiver_id = '$userId') 
           OR (sender_id = '$userId' AND receiver_id = 'admin')
    ");

    while ($msg = mysqli_fetch_assoc($messages)) {
        $sender = $msg['sender_id'];
        $receiver = $msg['receiver_id'];
        $message = mysqli_real_escape_string($conn, $msg['message']);
        $timestamp = $msg['timestamp'];

        // Move to deleted_messages table
        mysqli_query($conn, "
            INSERT INTO deleted_messages (original_sender_id, original_receiver_id, message, original_timestamp, deleted_at) 
            VALUES ('$sender', '$receiver', '$message', '$timestamp', NOW())
        ");
    }

    // Delete from messages table
    mysqli_query($conn, "
        DELETE FROM messages 
        WHERE (sender_id = 'admin' AND receiver_id = '$userId') 
           OR (sender_id = '$userId' AND receiver_id = 'admin')
    ");
}

echo "✅ Old inactive chats deleted successfully.";
?>
