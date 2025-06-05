<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id']) || !isset($_POST['receiver_id'])) {
    exit();
}

$receiver_id = intval($_POST['receiver_id']);
$msgSent = false;

if (!empty($_POST['message'])) {
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    mysqli_query($conn, "INSERT INTO messages (sender_id, receiver_id, message, timestamp, is_read) 
                         VALUES ('admin', '$receiver_id', '$message', NOW(), 0)");
    $msgSent = true;
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
                             VALUES ('admin', '$receiver_id', '$fileMsgEscaped', NOW(), 0)");
        $msgSent = true;
    }
}

echo $msgSent ? 'success' : 'fail';
