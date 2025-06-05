<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    exit("Unauthorized");
}
include 'db_connect.php';

$user_id = intval($_GET['user_id'] ?? 0);
if ($user_id > 0) {
    $result = $conn->query("SELECT * FROM live_chat WHERE user_id = $user_id ORDER BY timestamp ASC");
    while ($row = $result->fetch_assoc()) {
        $msg = htmlspecialchars($row['message']);
        $time = date("h:i A", strtotime($row['timestamp']));
        if ($row['sender'] === 'user') {
            echo "<div class='message user-msg'>$msg<div class='time'>$time</div></div>";
        } else {
            echo "<div class='message admin-msg'>$msg<div class='time'>$time</div></div>";
        }
    }
}
?>