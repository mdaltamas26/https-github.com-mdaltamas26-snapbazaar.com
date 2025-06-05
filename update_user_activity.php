<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    exit();
}

$user_id = $_SESSION['user_id'];

// Update last_active timestamp
mysqli_query($conn, "UPDATE users SET last_active = NOW() WHERE id = '$user_id'");
?>
