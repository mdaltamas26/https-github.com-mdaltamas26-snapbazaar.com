<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    die("Unauthorized Access!");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ticket_id']) && isset($_POST['reply_text'])) {
    $ticket_id = intval($_POST['ticket_id']);
    $admin_id = $_SESSION['admin_id'];
    $reply_text = mysqli_real_escape_string($conn, $_POST['reply_text']);

    $insert_query = "INSERT INTO support_replies (ticket_id, admin_id, reply_text, created_at) 
                     VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("iis", $ticket_id, $admin_id, $reply_text);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>