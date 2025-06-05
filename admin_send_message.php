<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    exit("Unauthorized");
}
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $message = htmlspecialchars(trim($_POST['message']));

    if ($message !== "" && $user_id > 0) {
        $stmt = $conn->prepare("INSERT INTO live_chat (sender, user_id, message) VALUES (?, ?, ?)");
        $sender = 'admin';
        $stmt->bind_param("sis", $sender, $user_id, $message);
        $stmt->execute();
        echo "success";
    }
}
?>