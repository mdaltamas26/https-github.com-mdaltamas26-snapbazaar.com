<?php
session_start();
include 'db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'phpmailer/src/Exception.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['order_id'], $_POST['cancel_reason'])) {
        header("Location: my_orders.php?error=Missing order info");
        exit();
    }

    $order_id = intval($_POST['order_id']);
    $reason = trim($_POST['cancel_reason']);
    $user_id = $_SESSION['user_id'] ?? null;

    if (!$user_id) {
        header("Location: login.php");
        exit();
    }

    // Get order
    $stmt = $conn->prepare("SELECT * FROM my_orders WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        header("Location: my_orders.php?error=Invalid order");
        exit();
    }

    $order = $result->fetch_assoc();
    $wallet_deduction = floatval($order['wallet_deduction']);
    $transaction_id = $order['transaction_id'];

    // Start transaction (optional, recommended)
    $conn->begin_transaction();

    try {
        // Update order status to cancelled
        $update = $conn->prepare("UPDATE my_orders SET order_status = 'cancelled' WHERE id = ?");
        $update->bind_param("i", $order_id);
        $update->execute();

        // Refund wallet if needed
        if ($wallet_deduction > 0) {
            $wallet_update = $conn->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?");
            $wallet_update->bind_param("di", $wallet_deduction, $user_id);
            $wallet_update->execute();
        }

        // Insert refund request record for cancellation
        $insert = $conn->prepare("INSERT INTO refund_requests (user_id, order_id, amount, reason, status, type, created_at) VALUES (?, ?, ?, ?, 'cancelled', 'cancel', NOW())");
        $insert->bind_param("iids", $user_id, $order_id, $wallet_deduction, $reason);
        $insert->execute();

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: my_orders.php?error=Failed to cancel order");
        exit();
    }

    // Get user email
    $user_q = $conn->prepare("SELECT email FROM users WHERE id = ? LIMIT 1");
    $user_q->bind_param("i", $user_id);
    $user_q->execute();
    $user_result = $user_q->get_result();
    $user_row = $user_result->fetch_assoc();
    $user_email = $user_row['email'] ?? '';

    // Send cancellation email
    if (!empty($user_email)) {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'angryffgaming8@gmail.com'; // sender email
            $mail->Password   = 'mnys tasw qbsx kbrw';       // sender app password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('angryffgaming8@gmail.com', 'SnapBazaar');
            $mail->addAddress($user_email);
            $mail->isHTML(true);
            $mail->Subject = 'Your Order has been Cancelled - SnapBazaar';
            $mail->Body    = "
                <h2>Order Cancelled Successfully</h2>
                <p>Dear Customer,</p>
                <p>Your order with Transaction ID <strong>$transaction_id</strong> has been cancelled successfully.</p>
                <p><strong>Reason:</strong> $reason</p>
                <p><strong>Wallet Refunded:</strong> ₹$wallet_deduction</p>
                <p>Thank you for using SnapBazaar. We hope to serve you again soon.</p>
                <br><p>- Team SnapBazaar</p>
            ";
            $mail->send();
        } catch (Exception $e) {
            error_log("Cancel Email Error: " . $mail->ErrorInfo);
        }
    }

    header("Location: order_details.php?order_id=" . $order_id . "&msg=Order cancelled and email sent successfully");
    exit();
} else {
    header("Location: my_orders.php");
    exit();
}
