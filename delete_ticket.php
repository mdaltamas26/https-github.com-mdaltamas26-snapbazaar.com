<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if (isset($_GET['ticket_id']) && is_numeric($_GET['ticket_id'])) {
    $ticket_id = intval($_GET['ticket_id']);

    $stmt = $conn->prepare("DELETE FROM support_tickets WHERE id = ?");
    $stmt->bind_param("i", $ticket_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Ticket deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting ticket!";
    }
}
header("Location: manage_tickets.php");
exit();
?>