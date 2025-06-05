<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'], $_GET['action'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action === 'ban') {
        $status = 'banned';
        $msg = "User has been banned.";
    } elseif ($action === 'unban') {
        $status = 'active';
        $msg = "User has been unbanned.";
    } else {
        header('Location: manage_users.php?error=Invalid action.');
        exit();
    }

    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);

    if ($stmt->execute()) {
        header("Location: manage_users.php?msg=" . urlencode($msg));
    } else {
        header("Location: manage_users.php?error=Something went wrong.");
    }
    $stmt->close();
} else {
    header('Location: manage_users.php?error=Invalid request.');
}
?>
