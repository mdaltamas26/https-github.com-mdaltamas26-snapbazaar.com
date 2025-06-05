<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $refund_id = intval($_POST['refund_id']);
    $action = $_POST['action'];

    if (in_array($action, ['approve', 'reject'])) {
        $status = $action == 'approve' ? 'approved' : 'rejected';
        $query = "UPDATE refund_requests SET status = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $status, $refund_id);

        if ($stmt->execute()) {
            header("Location: manage_refunds.php?msg=Refund+{$status}+successfully");
        } else {
            header("Location: manage_refunds.php?error=Failed+to+update+status");
        }
    }
}
