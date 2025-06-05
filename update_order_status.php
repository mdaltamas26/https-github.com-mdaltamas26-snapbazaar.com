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
    $order_status = $_POST['order_status'] ?? null;

    if ($order_id && $order_status) {
        $payment_status = ($order_status == 'delivered') ? 'Paid' : 'Pending';

        // First update the order
        $stmt = $conn->prepare("UPDATE my_orders SET order_status = ?, payment_status = ? WHERE id = ?");
        $stmt->bind_param("ssi", $order_status, $payment_status, $order_id);

        if ($stmt->execute()) {
            // Get user email for the order
            $user_query = $conn->prepare("SELECT users.email, users.name FROM my_orders JOIN users ON my_orders.user_id = users.id WHERE my_orders.id = ?");
            $user_query->bind_param("i", $order_id);
            $user_query->execute();
            $user_result = $user_query->get_result();

            if ($user_result->num_rows > 0) {
                $user = $user_result->fetch_assoc();
                $user_email = $user['email'];
                $user_name = $user['name'];

                // Send email
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'angryffgaming8@gmail.com';
                    $mail->Password = 'mnys tasw qbsx kbrw';  // App password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->SMTPOptions = array(
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true,
                        ),
                    );

                    $mail->setFrom('angryffgaming8@gmail.com', 'SnapBazaar');
                    $mail->addAddress($user_email, $user_name);
                    $mail->isHTML(true);
                    $mail->Subject = "Order Status Updated - SnapBazaar";
                    $mail->Body = "
    <div style='font-family: Arial, sans-serif; color: #333;'>
        <h2 style='color: #007bff;'>Hi <b>$user_name</b>,</h2>
        <p>We wanted to let you know that your order <strong>#$order_id</strong> <span style='color: #28a745; font-weight: bold;'>".ucfirst($order_status)."</span></p>
        <p><strong>Payment Status:</strong> $payment_status</p>
        <hr>
        <p>Thanks for choosing <strong>SnapBazaar</strong> — we appreciate your business!</p>
        <p>If you need any assistance, we're just an email away.</p>
        <br>
        <p>Cheers,<br><strong>SnapBazaar Team</strong></p>
    </div>
";

                    

                    $mail->send();
                    $_SESSION['message'] = "Order status updated & email sent!";
                } catch (Exception $e) {
                    $_SESSION['message'] = "Order updated but email not sent. Error: {$mail->ErrorInfo}";
                }
            } else {
                $_SESSION['message'] = "Order updated but user not found!";
            }
        } else {
            $_SESSION['error'] = "Failed to update order status.";
        }

        $stmt->close();
    } else {
        $_SESSION['error'] = "Invalid input.";
    }
}

header("Location: manage_orders.php");
exit();
?>
