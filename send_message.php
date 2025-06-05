<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit();
}

$user_id = $_SESSION['user_id'];
mysqli_query($conn, "UPDATE users SET last_active = NOW() WHERE id = '$user_id'");

if (!empty($_POST['message'])) {
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    mysqli_query($conn, "INSERT INTO messages (sender_id, receiver_id, message, timestamp, is_read) 
                         VALUES ('$user_id', 'admin', '$message', NOW(), 0)");
}

if (!empty($_FILES['file']['name'])) {
    $fileName = basename($_FILES['file']['name']);
    $uniqueName = time() . "_" . $fileName;
    $uploadPath = 'livechat/' . $uniqueName;

    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadPath)) {
        $fileUrl = "livechat/" . $uniqueName;
        $fileMsg = "<a href='$fileUrl' target='_blank'>📎 " . htmlspecialchars($fileName) . "</a>";
        $fileMsgEscaped = mysqli_real_escape_string($conn, $fileMsg);

        mysqli_query($conn, "INSERT INTO messages (sender_id, receiver_id, message, timestamp, is_read)
                             VALUES ('$user_id', 'admin', '$fileMsgEscaped', NOW(), 0)");
    }
}

echo "success";
?>
