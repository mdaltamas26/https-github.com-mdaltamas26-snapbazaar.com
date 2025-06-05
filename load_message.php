<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'];

$chats = mysqli_query($conn, "SELECT * FROM messages WHERE 
    (sender_id = 'admin' AND receiver_id = '$user_id') OR 
    (sender_id = '$user_id' AND receiver_id = 'admin') 
    ORDER BY timestamp ASC");

while ($row = mysqli_fetch_assoc($chats)) {
    $isUser = $row['sender_id'] == $user_id;
    $alignClass = $isUser ? 'msg-right' : 'msg-left';
    $msgClass = $isUser ? 'user-msg' : 'admin-msg';
    echo "<div class='$alignClass'>
            <div>
                <div class='msg-bubble $msgClass'>{$row['message']}</div>
                <div class='timestamp'>{$row['timestamp']}</div>
            </div>
          </div>";
}
?>
