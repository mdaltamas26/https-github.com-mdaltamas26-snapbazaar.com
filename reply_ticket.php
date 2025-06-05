<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ticket_id = intval($_POST['ticket_id']);
    $reply = trim($_POST['reply']);

    if (!empty($reply)) {
        $stmt = $conn->prepare("UPDATE support_tickets SET reply = ?, status = 'closed' WHERE id = ?");
        $stmt->bind_param("si", $reply, $ticket_id);

        if ($stmt->execute()) {
            header("Location: manage_tickets.php?msg=Reply sent successfully.");
            exit();
        } else {
            header("Location: manage_tickets.php?msg=Error sending reply.");
            exit();
        }
    } else {
        header("Location: manage_tickets.php?msg=Reply cannot be empty.");
        exit();
    }
} else {
    header("Location: manage_tickets.php");
    exit();
}
?>
