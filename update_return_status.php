<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$return_id = intval($_GET['id']);
$status = $_GET['status'];

$update_query = "UPDATE returns SET status = '$status' WHERE id = '$return_id'";
if (mysqli_query($conn, $update_query)) {
    header("Location: admin_returns.php?success=Return status updated.");
} else {
    header("Location: admin_returns.php?error=Failed to update.");
}
?>