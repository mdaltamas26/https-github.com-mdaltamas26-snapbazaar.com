<?php  
session_start();  
require 'db.php';  
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['user_id'])) {  
    header("Location: login.php");  
    exit();  
}

$user_id = intval($_SESSION['user_id']);  
$payment_method = mysqli_real_escape_string($conn, $_POST['payment_method'] ?? '');  
$coupon_code = mysqli_real_escape_string($conn, $_POST['applied_coupon'] ?? '');

// 🛒 Fetch Cart Items
$cart_result = mysqli_query($conn, "
    SELECT c.*, p.name, p.price 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = '$user_id'
");

if (!$cart_result || mysqli_num_rows($cart_result) === 0) {
    die("❌ Error: Cart is empty!");
}

// 🧮 Total Calculation
$total_price = 0;
$cart_items = [];
while ($row = mysqli_fetch_assoc($cart_result)) {
    $item_total = floatval($row['price']) * intval($row['quantity']);
    $total_price += $item_total;
    $cart_items[] = $row;
}

// 🎟️ Coupon Handling
$discount_amount = 0;
if (!empty($coupon_code)) {
    $coupon_check = mysqli_query($conn, "SELECT * FROM coupons WHERE code = '$coupon_code'");
    if ($coupon_check && mysqli_num_rows($coupon_check) > 0) {
        $coupon = mysqli_fetch_assoc($coupon_check);
        $discount_amount = floatval($coupon['discount']);
        if ($discount_amount > $total_price) {
            $discount_amount = $total_price;
        }
    } else {
        $coupon_code = null;
        $discount_amount = 0;
    }
}

$subtotal_after_coupon = $total_price - $discount_amount;

// 💰 Fetch User Wallet
$wallet_q = mysqli_query($conn, "SELECT wallet_balance FROM users WHERE id = '$user_id'");
$wallet_data = mysqli_fetch_assoc($wallet_q);
$wallet_balance = floatval($wallet_data['wallet_balance'] ?? 0);

// 💸 Wallet Deduction
$wallet_deduction = min($wallet_balance, $subtotal_after_coupon);
$final_price = $subtotal_after_coupon - $wallet_deduction;

// 🔄 Update wallet
if ($wallet_deduction > 0) {
    mysqli_query($conn, "UPDATE users SET wallet_balance = wallet_balance - $wallet_deduction WHERE id = '$user_id'");
}

$transaction_id = "TXN" . rand(10000000, 99999999);  
$payment_status = ($final_price <= 0) ? 'Paid' : (($payment_method == 'UPI') ? 'Paid' : 'Pending');
if ($final_price <= 0) {
    $payment_method = 'Wallet';
}

// 📦 Address Fetch
$address_result = mysqli_query($conn, "SELECT * FROM addresses WHERE user_id = '$user_id' LIMIT 1");
if (!$address_result || mysqli_num_rows($address_result) == 0) {
    die("❌ Error: Address not found!");
}
$address = mysqli_fetch_assoc($address_result);
$full_address = $address['address'] . ", " . $address['city'] . ", " . $address['state'] . " - " . $address['pincode'];
$phone = $address['phone'];

// 📧 User Email
$user_result = mysqli_query($conn, "SELECT email FROM users WHERE id = '$user_id' LIMIT 1");
$user = mysqli_fetch_assoc($user_result);
$user_email = $user['email'] ?? 'N/A';

// ✅ Save Order(s)
$order_details_html = "<h3>New Order Placed</h3><p><strong>Transaction ID:</strong> $transaction_id</p><ul>";

foreach ($cart_items as $item) {
    $product_id = intval($item['product_id']);
    $quantity = intval($item['quantity']);
    $product_price = floatval($item['price']);
    $item_total = $product_price * $quantity;

    // Proportional discount & wallet deduction
    $item_discount = ($discount_amount > 0) ? round(($item_total / $total_price) * $discount_amount, 2) : 0;
    $item_wallet = ($wallet_deduction > 0) ? round(($item_total / $total_price) * $wallet_deduction, 2) : 0;
    $item_final = $item_total - $item_discount - $item_wallet;

    // Insert order row
    $insert = "
       INSERT INTO my_orders 
(user_id, product_id, quantity, total_price, payment_method, transaction_id, payment_status, address, phone, coupon_code, discount_amount, wallet_deduction, order_status, created_at, updated_at)
VALUES 
('$user_id', '$product_id', '$quantity', '$item_final', '$payment_method', '$transaction_id', '$payment_status', 
'$full_address', '$phone', '$coupon_code', '$item_discount', '$wallet_deduction', 'Pending', NOW(), NOW())

    ";

    if (!mysqli_query($conn, $insert)) {
        die("❌ Order Insertion Failed: " . mysqli_error($conn));
    }

    $order_details_html .= "<li><strong>{$item['name']}</strong> x {$item['quantity']} - ₹{$item_final}</li>";
}

$order_details_html .= "</ul>";
$order_details_html .= "<p><strong>Total:</strong> ₹" . number_format($total_price, 2) . "</p>";
$order_details_html .= "<p><strong>Coupon Discount:</strong> ₹" . number_format($discount_amount, 2) . "</p>";
$order_details_html .= "<p><strong>Wallet Used:</strong> ₹" . number_format($wallet_deduction, 2) . "</p>";
$order_details_html .= "<p><strong>Final Payable:</strong> ₹" . number_format($final_price, 2) . "</p>";
$order_details_html .= "<p><strong>Payment Method:</strong> $payment_method</p>";
$order_details_html .= "<p><strong>User Email:</strong> $user_email</p><p><strong>Phone:</strong> $phone</p><p><strong>Address:</strong> $full_address</p>";

// 🧹 Clear Cart
mysqli_query($conn, "DELETE FROM cart WHERE user_id = '$user_id'");

// 📩 Admin Email
try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'angryffgaming8@gmail.com';     
    $mail->Password   = 'mnys tasw qbsx kbrw';   
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('angryffgaming888@gmail.com', 'SnapBazaar');
    $mail->addAddress('angryffgaming888@gmail.com');
    $mail->isHTML(true);
    $mail->Subject = 'New Order Placed - SnapBazaar';
    $mail->Body    = $order_details_html;
    $mail->send();
} catch (Exception $e) {
    error_log("Admin Email Error: {$mail->ErrorInfo}");
}

// 📩 User Email
try {
    $user_mail = new PHPMailer(true);
    $user_mail->isSMTP();
    $user_mail->Host       = 'smtp.gmail.com';
    $user_mail->SMTPAuth   = true;
    $user_mail->Username   = 'angryffgaming8@gmail.com';
    $user_mail->Password   = 'mnys tasw qbsx kbrw';
    $user_mail->SMTPSecure = 'tls';
    $user_mail->Port       = 587;

    $user_mail->setFrom('angryffgaming8@gmail.com', 'SnapBazaar');
$user_mail->addAddress($user_email);
$user_mail->isHTML(true);
$user_mail->Subject = '📦 Your Order is Confirmed - SnapBazaar';

$user_mail->Body = "
    <h2>Thank you for your order!</h2>
    <p>Your order has been placed with Transaction ID: <strong>$transaction_id</strong>.</p>
    <p><strong>Payment Method:</strong> $payment_method</p>
    <p>Below are your order details:</p>
    $order_details_html
    <p>We’ll notify you once your order is shipped.</p>
    <br><p>– Team SnapBazaar</p>
";

    $user_mail->send();
} catch (Exception $e) {
    error_log("User Email Error: {$user_mail->ErrorInfo}");
}

// ✅ Redirect to My Orders
header("Location: my_orders.php?success=Order Placed Successfully!");
exit();
?>
