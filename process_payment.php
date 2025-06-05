<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit();
}

$user_id = intval($_SESSION['user_id']);
$transaction_id = $_POST['transaction_id'] ?? '';

error_log("Transaction ID received: $transaction_id");

if (empty($transaction_id) || !preg_match('/^\d{12}$/', $transaction_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid transaction ID.']);
    exit();
}

// Fetch cart items
$stmt_cart = $conn->prepare("SELECT c.product_id, c.quantity, p.name, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
if (!$stmt_cart) {
    error_log("Prepare cart failed: " . $conn->error);
    echo json_encode(['status' => 'error', 'message' => 'Server error (prepare cart).']);
    exit();
}
$stmt_cart->bind_param("i", $user_id);
if (!$stmt_cart->execute()) {
    error_log("Execute cart failed: " . $stmt_cart->error);
    echo json_encode(['status' => 'error', 'message' => 'Server error (execute cart).']);
    exit();
}

$cart_result = $stmt_cart->get_result();
if ($cart_result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Your cart is empty.']);
    exit();
}

$original_total = 0;
$cart_items = [];
while ($row = $cart_result->fetch_assoc()) {
    $original_total += $row['price'] * $row['quantity'];
    $cart_items[] = $row;
}

$discount = isset($_SESSION['discount_amount']) ? floatval($_SESSION['discount_amount']) : 0;
$total_price = max(0, $original_total - $discount);

if (!$conn->begin_transaction()) {
    error_log("Transaction start failed: " . $conn->error);
    echo json_encode(['status' => 'error', 'message' => 'Server error (start transaction).']);
    exit();
}

try {
    $stmt_order = $conn->prepare("INSERT INTO orders (user_id, transaction_id, total_amount, discount_amount, payment_method, order_date, status) VALUES (?, ?, ?, ?, 'UPI', NOW(), 'Pending')");
    if (!$stmt_order) {
        throw new Exception("Prepare order failed: " . $conn->error);
    }
    $stmt_order->bind_param("isdd", $user_id, $transaction_id, $total_price, $discount);
    if (!$stmt_order->execute()) {
        throw new Exception("Execute order failed: " . $stmt_order->error);
    }

    $order_id = $conn->insert_id;

    $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt_item) {
        throw new Exception("Prepare order_items failed: " . $conn->error);
    }

    foreach ($cart_items as $item) {
        $subtotal = $item['price'] * $item['quantity'];
        $stmt_item->bind_param("iiidd", $order_id, $item['product_id'], $item['quantity'], $item['price'], $subtotal);
        if (!$stmt_item->execute()) {
            throw new Exception("Execute order_items failed: " . $stmt_item->error);
        }
    }

    $stmt_clear_cart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    if (!$stmt_clear_cart) {
        throw new Exception("Prepare clear cart failed: " . $conn->error);
    }
    $stmt_clear_cart->bind_param("i", $user_id);
    if (!$stmt_clear_cart->execute()) {
        throw new Exception("Execute clear cart failed: " . $stmt_clear_cart->error);
    }

    unset($_SESSION['discount_amount'], $_SESSION['coupon_code']);

    $conn->commit();

    $stmt_user = $conn->prepare("SELECT email, name FROM users WHERE id = ?");
    if (!$stmt_user) {
        throw new Exception("Prepare user select failed: " . $conn->error);
    }
    $stmt_user->bind_param("i", $user_id);
    if (!$stmt_user->execute()) {
        throw new Exception("Execute user select failed: " . $stmt_user->error);
    }
    $result_user = $stmt_user->get_result();
    $user_data = $result_user->fetch_assoc();

    $user_email = $user_data['email'];
    $user_name = htmlspecialchars($user_data['name'], ENT_QUOTES);

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@gmail.com'; 
        $mail->Password = 'your-app-password';  
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('no-reply@snapbazaar.com', 'SnapBazaar');

        $mail->addAddress($user_email, $user_name);
        $mail->isHTML(false);
        $mail->Subject = "SnapBazaar Order Confirmation - Order #$order_id";
        $mail->Body = "Hello $user_name,\n\nThank you for your purchase! Your order #$order_id has been received.\n\nTotal Amount Paid: ₹" . number_format($total_price, 2) . "\nTransaction ID: $transaction_id\n\nWe will notify you once your order is shipped.\n\nRegards,\nSnapBazaar Team";
        $mail->send();

        $mail->clearAddresses();
        $admin_email = "admin@snapbazaar.com"; 
        $mail->addAddress($admin_email);
        $mail->Subject = "New Order Received - Order #$order_id";
        $mail->Body = "New order received from user ID: $user_id\n\nOrder ID: $order_id\nTotal Amount: ₹" . number_format($total_price, 2) . "\nTransaction ID: $transaction_id\n\nPlease process this order promptly.";
        $mail->send();

    } catch (Exception $e) {
        error_log("Email sending failed: " . $mail->ErrorInfo);
    }

    echo json_encode(['status' => 'success', 'message' => 'Order placed successfully!']);

} catch (Exception $e) {
    $conn->rollback();
    error_log("Order placement failed: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Order placement failed. Please try again.', 'debug' => $e->getMessage()]);
}
?>
