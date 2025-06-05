<?php
include 'db.php';
session_start();

if (!isset($_POST['order_id']) || !isset($_SESSION['user_id'])) {
    die("Unauthorized Access");
}

$order_id = $_POST['order_id'];
$user_id = $_SESSION['user_id'];
$reason = trim($_POST['refund_reason']);

// Check if refund already requested
$check_query = "SELECT status FROM refund_requests WHERE user_id = ? AND order_id = ? AND type = 'refund'";
$stmt = $conn->prepare($check_query);
$stmt->bind_param("ii", $user_id, $order_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($existing_status);
    $stmt->fetch();

    if ($existing_status === 'pending' || $existing_status === 'approved') {
        header("Location: order_details.php?order_id=$order_id&error=You have already requested a refund. Status: $existing_status");
        exit();
    }

    // If rejected, delete the previous rejected request and allow new one
    $delete = $conn->prepare("DELETE FROM refund_requests WHERE user_id = ? AND order_id = ? AND type = 'refund' AND status = 'rejected'");
    $delete->bind_param("ii", $user_id, $order_id);
    $delete->execute();
}

// Insert new refund request
$insert = $conn->prepare("INSERT INTO refund_requests (user_id, order_id, reason, status, type, created_at) VALUES (?, ?, ?, 'pending', 'refund', NOW())");
$insert->bind_param("iis", $user_id, $order_id, $reason);
if ($insert->execute()) {
    header("Location: order_details.php?order_id=$order_id&success=Refund request submitted successfully.");
} else {
    header("Location: order_details.php?order_id=$order_id&error=Failed to submit refund request.");
}
exit;
?>
