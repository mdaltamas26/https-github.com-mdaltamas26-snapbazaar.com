<?php
session_start();
include 'db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = $_POST['order_id'] ?? null;
    $payment_status = strtolower($_POST['payment_status'] ?? '');
    $payment_method = strtolower($_POST['payment_method'] ?? '');

    if ($order_id && $payment_status && $payment_method) {
        // Restrict COD to only pending or paid
        if ($payment_method == 'cod' && !in_array($payment_status, ['pending', 'paid'])) {
            $_SESSION['error'] = "COD orders can only be marked as Pending or Paid.";
            header("Location: manage_orders.php");
            exit();
        }

        // Update payment status
        $stmt = $conn->prepare("UPDATE my_orders SET payment_status = ? WHERE id = ?");
        $stmt->bind_param("si", $payment_status, $order_id);
        if ($stmt->execute()) {
            // If UPI and status is failed ➜ cancel the order
            if ($payment_method == 'upi' && $payment_status == 'failed') {
                $update = $conn->prepare("UPDATE my_orders SET order_status = 'cancelled' WHERE id = ?");
                $update->bind_param("i", $order_id);
                $update->execute();

                // Fetch user data
                $query = $conn->prepare("SELECT u.name, u.email FROM users u JOIN my_orders o ON u.id = o.user_id WHERE o.id = ?");
                $query->bind_param("i", $order_id);
                $query->execute();
                $result = $query->get_result();
                if ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();
                    $user_name = $user['name'];
                    $user_email = $user['email'];

                    // Send warning email
                    $mail = new PHPMailer(true);
                    try {
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'angryffgaming8@gmail.com';
                        $mail->Password = 'mnystaswqbsxkbrw'; // App Password
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;

                        $mail->setFrom('angryffgaming8@gmail.com', 'SnapBazaar');
                        $mail->addAddress($user_email, $user_name);
                        $mail->isHTML(true);
                        $mail->Subject = "Order Cancelled due to Failed UPI Payment";

                        $mail->Body = "
                            <div style='font-family: Arial, sans-serif; color: #333;'>
                                <h3 style='color: red;'>Illegal Transaction Detected</h3>
                                <p>Dear <b>$user_name</b>,</p>
                                <p>Your order (ID: <b>$order_id</b>) has been <b style='color:red;'>cancelled</b> due to a failed UPI payment attempt that appears suspicious.</p>
                                <p>This activity has been logged and could be reported for further investigation.</p>
                                <p><b>Note:</b> Any repeated fraudulent behavior may lead to account suspension or legal consequences.</p>
                                <br>
                                <p>Regards,<br><b>SnapBazaar Security Team</b></p>
                            </div>
                        ";

                        $mail->send();
                    } catch (Exception $e) {
                        // Email failed silently
                    }
                }
            }

            $_SESSION['message'] = "Payment status updated" . ($payment_status == 'failed' ? " and order auto-cancelled!" : "!");
        } else {
            $_SESSION['error'] = "Failed to update payment status.";
        }
    } else {
        $_SESSION['error'] = "Missing input.";
    }
}

header("Location: manage_orders.php");
exit();
?>
