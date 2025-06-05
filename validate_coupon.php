<?php
include 'db.php';
session_start();

$code = mysqli_real_escape_string($conn, $_GET['code'] ?? '');

$response = ['valid' => false, 'discount' => 0];

// 🛒 User ID and total from cart
if (!isset($_SESSION['user_id'])) {
    echo json_encode($response);
    exit;
}

$user_id = $_SESSION['user_id'];
$cart_query = "SELECT c.quantity, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = '$user_id'";
$cart_result = mysqli_query($conn, $cart_query);

$total_price = 0;
while ($item = mysqli_fetch_assoc($cart_result)) {
    $total_price += $item['price'] * $item['quantity'];
}

// 🎯 Search coupon from both possible tables
$coupon = null;

// Try table with discount, expiry_date (used in create_coupon.php)
$query1 = "SELECT * FROM coupons WHERE code = '$code' LIMIT 1";
$result1 = mysqli_query($conn, $query1);
if (mysqli_num_rows($result1)) {
    $coupon = mysqli_fetch_assoc($result1);
    $discount_type = 'fixed'; // assume it's fixed if no percentage fields
} else {
    // Try second structure (used in manage_coupons.php)
    $query2 = "SELECT * FROM coupons WHERE code = '$code' AND valid_from <= NOW() AND valid_to >= NOW() LIMIT 1";
    $result2 = mysqli_query($conn, $query2);
    if (mysqli_num_rows($result2)) {
        $coupon = mysqli_fetch_assoc($result2);
        $discount_type = 'percentage'; // assume this has % and max_discount
    }
}

// 🚦 Validate coupon
if ($coupon) {
    // Check min purchase if set
    $min_purchase = $coupon['min_purchase'] ?? 0;
    if ($total_price < $min_purchase) {
        $response['message'] = "Minimum purchase should be ₹$min_purchase.";
    } else {
        // Calculate discount
        if ($discount_type === 'percentage') {
            $discount = ($coupon['discount_percentage'] / 100) * $total_price;
            $discount = min($discount, $coupon['max_discount']); // cap to max
        } else {
            $discount = $coupon['discount'];
        }

        // Check expiry if fixed type
        if ($discount_type === 'fixed' && strtotime($coupon['expiry_date']) < time()) {
            $response['message'] = "Coupon has expired.";
        } else {
            $response['valid'] = true;
            $response['discount'] = round($discount, 2);
        }
    }
}

echo json_encode($response);
?>
