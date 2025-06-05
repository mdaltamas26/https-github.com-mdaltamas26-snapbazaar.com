<?php
session_start();
include 'db.php';

if (!isset($_POST['coupon_code'])) {
    echo json_encode(['error' => 'Invalid request']);
    exit();
}

$coupon_code = strtoupper(trim($_POST['coupon_code']));
$user_id = $_SESSION['user_id'];

// Get cart total
$cart_total = 0;
$cart_query = mysqli_query($conn, "SELECT SUM(p.price * c.quantity) as total FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = '$user_id'");
if ($cart_row = mysqli_fetch_assoc($cart_query)) {
    $cart_total = $cart_row['total'];
}

// Check if the coupon is valid
$coupon_query = mysqli_query($conn, "SELECT * FROM coupons WHERE code = '$coupon_code' AND expiration_date >= CURDATE() AND usage_limit > 0");
$coupon = mysqli_fetch_assoc($coupon_query);

if (!$coupon) {
    echo json_encode(['error' => 'Invalid or expired coupon']);
    exit();
}

// Check min order amount
if ($cart_total < $coupon['min_order']) {
    echo json_encode(['error' => 'Minimum order ₹' . $coupon['min_order'] . ' required']);
    exit();
}

// Calculate discount
$discount_amount = ($coupon['type'] == 'percentage') ? ($cart_total * ($coupon['discount'] / 100)) : $coupon['discount'];
$discount_amount = min($discount_amount, $cart_total); // Discount cannot exceed cart total

$_SESSION['discount'] = $discount_amount;
$_SESSION['coupon_code'] = $coupon_code;

echo json_encode(['success' => 'Coupon Applied!', 'discount' => $discount_amount]);
?>