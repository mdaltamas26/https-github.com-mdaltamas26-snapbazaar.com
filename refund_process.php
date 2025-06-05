<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $return_id = intval($_POST['return_id']);
    $refund_amount = floatval($_POST['refund_amount']);
    
    // Update return status
    $update_query = "UPDATE returns SET status = 'Refunded' WHERE id = '$return_id'";
    if (mysqli_query($conn, $update_query)) {
        // Add amount to user's wallet
        $get_user_query = "SELECT user_id FROM returns WHERE id = '$return_id'";
        $user_result = mysqli_query($conn, $get_user_query);
        $user = mysqli_fetch_assoc($user_result);
        $user_id = $user['user_id'];
        
        $wallet_update = "UPDATE users SET wallet = wallet + $refund_amount WHERE id = '$user_id'";
        mysqli_query($conn, $wallet_update);
        
        header("Location: admin_returns.php?success=Refund processed.");
    } else {
        header("Location: admin_returns.php?error=Refund failed.");
    }
}
?>