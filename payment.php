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

// Handle coupon code submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_coupon'])) {
    $entered_coupon = trim($_POST['coupon_code']);
    if ($entered_coupon !== '') {
        // Validate coupon from DB
        $coupon_check = mysqli_query($conn, "SELECT * FROM coupons WHERE code = '$entered_coupon'");
        if ($coupon_check && mysqli_num_rows($coupon_check) > 0) {
            $_SESSION['coupon_code'] = $entered_coupon;
        } else {
            $_SESSION['coupon_code'] = '';
            $_SESSION['coupon_error'] = "Invalid coupon code!";
        }
    } else {
        $_SESSION['coupon_code'] = '';
    }
    // Redirect to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Coupon code from session
$coupon_code = $_SESSION['coupon_code'] ?? '';

// 🛒 Fetch Cart Items + Product Price
$cart_result = mysqli_query($conn, "
    SELECT c.*, p.name, p.price 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = '$user_id'
");

if (!$cart_result || mysqli_num_rows($cart_result) === 0) {
    die("❌ Error: Cart is empty!");
}

$total_price = 0;
$cart_items = [];
while ($row = mysqli_fetch_assoc($cart_result)) {
    $item_total = floatval($row['price']) * intval($row['quantity']);
    $total_price += $item_total;
    $cart_items[] = $row;
}

// 🎟️ Coupon Validation & Discount Calculation (in case session discount is not set)
$discount_amount = 0;
if (!empty($coupon_code)) {
    $coupon_check = mysqli_query($conn, "SELECT * FROM coupons WHERE code = '$coupon_code'");
    if ($coupon_check && mysqli_num_rows($coupon_check) > 0) {
        $coupon = mysqli_fetch_assoc($coupon_check);
        // Discount in percentage or fixed amount? Assuming 'discount' is fixed amount for simplicity
        $discount_amount = floatval($coupon['discount']);
        if ($discount_amount > $total_price) {
            $discount_amount = $total_price;
        }
    } else {
        $coupon_code = '';
        $discount_amount = 0;
    }
}

// If session discount is set, override coupon discount amount (optional)
// $discount_amount = $_SESSION['discount_amount'] ?? $discount_amount;

// 💰 Fetch Wallet Balance
$wallet_q = mysqli_query($conn, "SELECT wallet_balance FROM users WHERE id = '$user_id'");
$wallet_data = mysqli_fetch_assoc($wallet_q);
$wallet_balance = floatval($wallet_data['wallet_balance'] ?? 0);

// 💸 Calculate wallet deduction
$subtotal_after_coupon = $total_price - $discount_amount;
$wallet_deduction = min($wallet_balance, $subtotal_after_coupon);

// 💵 Final amount to pay via UPI
$final_amount = max(0, $subtotal_after_coupon - $wallet_deduction);

// UPI ID (you can put your real UPI ID here)
$upi_id = 'newuser9546@ybl';

// UPI QR Data with final amount
$qr_data = "upi://pay?pa=$upi_id&pn=SnapBazaar&cu=INR&am=" . number_format($final_amount, 2, '.', '');

// If form submitted (transaction_id from user input)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['transaction_id'])) {
    $transaction_id = trim($_POST['transaction_id'] ?? '');
    if (empty($transaction_id)) {
        die("❌ Please enter the UPI Transaction ID.");
    }

    $payment_method = 'UPI';
    $payment_status = ($final_amount <= 0) ? 'Paid' : 'Pending';
    if ($final_amount <= 0) {
        $payment_method = 'Wallet';
    }

    // 📦 Fetch user address (assuming 1 address per user)
    $address_result = mysqli_query($conn, "SELECT * FROM addresses WHERE user_id = '$user_id' LIMIT 1");
    if (!$address_result || mysqli_num_rows($address_result) == 0) {
        die("❌ Address not found!");
    }
    $address = mysqli_fetch_assoc($address_result);
    $full_address = $address['address'] . ", " . $address['city'] . ", " . $address['state'] . " - " . $address['pincode'];
    $phone = $address['phone'];

    // 📧 User Email
    $user_result = mysqli_query($conn, "SELECT email FROM users WHERE id = '$user_id' LIMIT 1");
    $user = mysqli_fetch_assoc($user_result);
    $user_email = $user['email'] ?? 'N/A';

    // Transaction ID for order
    $order_transaction_id = "TXN" . rand(10000000, 99999999);

    // Insert orders for each cart item with proportional discount and wallet deduction
    $order_details_html = "<h3>New Order Placed</h3><p><strong>Transaction ID:</strong> $order_transaction_id</p><ul>";

    foreach ($cart_items as $item) {
        $product_id = intval($item['product_id']);
        $quantity = intval($item['quantity']);
        $product_price = floatval($item['price']);
        $item_total = $product_price * $quantity;

        $item_discount = ($discount_amount > 0) ? round(($item_total / $total_price) * $discount_amount, 2) : 0;
        $item_wallet = ($wallet_deduction > 0) ? round(($item_total / $total_price) * $wallet_deduction, 2) : 0;
        $item_final = $item_total - $item_discount - $item_wallet;

        // Insert into my_orders table
        $insert = "
            INSERT INTO my_orders 
            (user_id, product_id, quantity, total_price, payment_method, transaction_id, payment_status, address, phone, coupon_code, discount_amount, wallet_deduction, order_status, created_at, updated_at)
            VALUES 
            ('$user_id', '$product_id', '$quantity', '$item_final', '$payment_method', '$order_transaction_id', '$payment_status', 
            '$full_address', '$phone', '$coupon_code', '$item_discount', '$item_wallet', 'Pending', NOW(), NOW())
        ";

        if (!mysqli_query($conn, $insert)) {
            die("❌ Order Insertion Failed: " . mysqli_error($conn));
        }

        $order_details_html .= "<li><strong>{$item['name']}</strong> x {$quantity} - ₹" . number_format($item_final, 2) . "</li>";
    }

    $order_details_html .= "</ul>";
    $order_details_html .= "<p><strong>Total:</strong> ₹" . number_format($total_price, 2) . "</p>";
    $order_details_html .= "<p><strong>Coupon Discount:</strong> ₹" . number_format($discount_amount, 2) . "</p>";
    $order_details_html .= "<p><strong>Wallet Used:</strong> ₹" . number_format($wallet_deduction, 2) . "</p>";
    $order_details_html .= "<p><strong>Final Payable:</strong> ₹" . number_format($final_amount, 2) . "</p>";
    $order_details_html .= "<p><strong>Payment Method:</strong> $payment_method</p>";
    $order_details_html .= "<p><strong>User Email:</strong> $user_email</p><p><strong>Phone:</strong> $phone</p><p><strong>Address:</strong> $full_address</p>";

    // *** Wallet balance update - deduct the used wallet amount ***
    if ($wallet_deduction > 0) {
        $update_wallet_sql = "UPDATE users SET wallet_balance = wallet_balance - $wallet_deduction WHERE id = $user_id";
        mysqli_query($conn, $update_wallet_sql);
    }

    // Clear Cart
    mysqli_query($conn, "DELETE FROM cart WHERE user_id = '$user_id'");

    // Send Admin Email
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

    // Send User Email
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
            <p>Your order has been placed with Transaction ID: <strong>$order_transaction_id</strong>.</p>
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

    // Redirect to My Orders page with success message
    header("Location: my_orders.php?success=Order Placed Successfully!");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>UPI Payment - SnapBazaar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f9f9f9;
            font-family: 'Segoe UI', sans-serif;
        }
        .container {
            max-width: 750px;
            margin-top: 40px;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.08);
        }
        .upi-box {
            background: #e9f5ff;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
        }
        .upi-box img {
            max-width: 200px;
            margin-bottom: 15px;
        }
        .copy-btn {
            cursor: pointer;
            color: #0d6efd;
            margin-left: 8px;
            font-size: 0.9rem;
        }
        .summary-table td {
            padding: 6px 0;
        }
        .coupon-error {
            color: red;
            font-weight: 600;
            margin-top: 5px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card p-4">

        <!-- Coupon Code Form Added on Top -->
        <form method="POST" class="mb-4" novalidate>
            <div class="input-group">
                <input type="text" name="coupon_code" class="form-control" placeholder="Enter Coupon Code" value="<?= htmlspecialchars($coupon_code) ?>">
                <button class="btn btn-outline-primary" type="submit" name="apply_coupon">Apply Coupon</button>
            </div>
            <?php if (!empty($_SESSION['coupon_error'])): ?>
                <div class="coupon-error"><?= htmlspecialchars($_SESSION['coupon_error']) ?></div>
                <?php unset($_SESSION['coupon_error']); ?>
            <?php endif; ?>
        </form>

        <h4 class="mb-4 text-center">Complete UPI Payment</h4>

        <div class="upi-box mb-4">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode($qr_data) ?>" alt="UPI QR Code">
            <p class="mb-0">
                <strong>Pay to:</strong>
                <span class="text-primary" id="upiId"><?= htmlspecialchars($upi_id) ?></span>
                <span class="copy-btn" onclick="copyUPI()">[Copy]</span>
            </p>
            <p><strong>Amount:</strong> ₹<?= number_format($final_amount, 2) ?></p>
        </div>

        <form method="POST" id="upiForm" novalidate>
            <div class="mb-3">
                <label for="transaction_id" class="form-label">Enter UPI Transaction ID</label>
                <input type="text" name="transaction_id" id="transaction_id" class="form-control" required maxlength="20" placeholder="e.g. 123456789012">
            </div>

            <h5 class="mb-3">Order Summary</h5>
            <ul class="list-group mb-3">
                <?php foreach ($cart_items as $item): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?= htmlspecialchars($item['name']) ?> × <?= intval($item['quantity']) ?>
                        <span>₹<?= number_format(floatval($item['price']) * intval($item['quantity']), 2) ?></span>
                    </li>
                <?php endforeach; ?>
                <?php if ($discount_amount > 0): ?>
                    <li class="list-group-item d-flex justify-content-between text-success">
                        Coupon (<?= htmlspecialchars($coupon_code) ?>)
                        <span>-₹<?= number_format($discount_amount, 2) ?></span>
                    </li>
                <?php endif; ?>
                <?php if ($wallet_deduction > 0): ?>
                    <li class="list-group-item d-flex justify-content-between text-info">
                        Wallet Deduction
                        <span>-₹<?= number_format($wallet_deduction, 2) ?></span>
                    </li>
                <?php endif; ?>
                <li class="list-group-item d-flex justify-content-between fw-bold">
                    Total Payable
                    <span>₹<?= number_format($final_amount, 2) ?></span>
                </li>
            </ul>

            <input type="hidden" name="payment_method" value="UPI">
            <button type="submit" class="btn btn-primary w-100">Confirm Payment</button>
        </form>
    </div>
</div>

<script>
function copyUPI() {
    navigator.clipboard.writeText("<?= htmlspecialchars($upi_id) ?>").then(() => {
        alert('UPI ID copied to clipboard');
    }).catch(() => alert('Failed to copy UPI ID'));
}
</script>

</body>
</html>
