<?php
session_start();
include 'db.php';

$code = $_POST['code'] ?? '';
$user_id = $_SESSION['user_id'] ?? 0;

if (!$code) {
    echo "Please enter a coupon code";
    exit;
}

$today = date("Y-m-d");
$query = "SELECT * FROM coupons 
          WHERE code = '$code' 
          AND valid_from <= '$today' 
          AND valid_to >= '$today' 
          LIMIT 1";

$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    echo "Invalid or expired coupon";
    exit;
}

$coupon = mysqli_fetch_assoc($result);

// 🛒 Get total price
$cart_total = 0;
$cart_result = mysqli_query($conn, "SELECT c.*, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = '$user_id'");
while ($row = mysqli_fetch_assoc($cart_result)) {
    $cart_total += $row['price'] * $row['quantity'];
}

// ✅ Minimum purchase check
if ($cart_total < $coupon['min_purchase']) {
    echo "Minimum purchase should be ₹" . $coupon['min_purchase'];
    exit;
}

// 🧮 Calculate discount
$discount = 0;
if ($coupon['discount_percentage'] > 0) {
    $discount = ($coupon['discount_percentage'] / 100) * $cart_total;
}
if ($coupon['max_discount'] > 0 && $discount > $coupon['max_discount']) {
    $discount = $coupon['max_discount'];
}
if ($coupon['discount'] > 0) {
    $discount = $coupon['discount']; // Flat discount
}

// ✅ Save in session
$_SESSION['discount_amount'] = round($discount);
$_SESSION['coupon_code'] = $coupon['code'];

echo "success";
