<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized";
    exit();
}

$user_id = $_SESSION['user_id'];
$last_id = isset($_POST['last_message_id']) ? intval($_POST['last_message_id']) : 0;

$query = "SELECT * FROM messages 
          WHERE id > $last_id AND (
              (sender_id = 'admin' AND receiver_id = '$user_id') 
              OR (sender_id = '$user_id' AND receiver_id = 'admin')
          ) 
          ORDER BY timestamp ASC";

$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    $isUser = $row['sender_id'] == $user_id;
    $msg = $row['message'];
    $msgId = $row['id'];
    $time = date("d M Y h:i A", strtotime($row['timestamp']));

    echo '<div class="' . ($isUser ? 'msg-right' : 'msg-left') . '" data-id="' . $msgId . '">';
    echo '<div class="msg-bubble ' . ($isUser ? 'user-msg' : 'admin-msg') . '">';

    preg_match('/<a\s+href=["\'](.*?)["\'].*?>(.*?)<\/a>/', $msg, $matches);
    $fileUrl = '';
    $fileName = '';
    $isFileLink = false;

    if ($matches) {
        $fileUrl = $matches[1];
        $fileName = $matches[2];
        $isFileLink = true;
    }

    if (!$isFileLink && filter_var($msg, FILTER_VALIDATE_URL)) {
        $fileUrl = $msg;
        $fileName = basename($fileUrl);
        $isFileLink = true;
    }

    $ext = strtolower(pathinfo($fileUrl ?: '', PATHINFO_EXTENSION));

    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
        echo "<img src='" . htmlspecialchars($fileUrl) . "' style='max-width: 200px; border-radius:10px;' alt='Image'>";
    } elseif ($ext === 'mp4') {
        echo "<video controls style='max-width: 200px; border-radius:10px;'>
                <source src='" . htmlspecialchars($fileUrl) . "' type='video/mp4'>
                Your browser does not support the video tag.
              </video>";
    } elseif ($ext === 'mp3') {
        echo "<audio controls style='width: 200px;'>
                <source src='" . htmlspecialchars($fileUrl) . "' type='audio/mpeg'>
                Your browser does not support the audio element.
              </audio>";
    } elseif ($isFileLink) {
        echo "<a href='" . htmlspecialchars($fileUrl) . "' target='_blank'>📎 " . htmlspecialchars($fileName) . "</a>";
    } else {
        echo nl2br(htmlspecialchars($msg));
    }

    echo '<div class="timestamp">' . $time . '</div>';
    echo '</div></div>';
}
?>
