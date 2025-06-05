<?php
session_start();
include 'db.php';

$response = ['status' => 'error', 'message' => 'Invalid request.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['coupon_code'] ?? '');

    if (!empty($code)) {
        $today = date('Y-m-d');

        $stmt = $conn->prepare("SELECT * FROM coupons WHERE code = ? AND valid_from <= ? AND valid_to >= ?");
        $stmt->bind_param("sss", $code, $today, $today);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($coupon = $result->fetch_assoc()) {
            // Fetch user cart total
            $user_id = $_SESSION['user_id'];
            $cart_query = "SELECT SUM(p.price * c.quantity) AS total_price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?";
            $cart_stmt = $conn->prepare($cart_query);
            $cart_stmt->bind_param("i", $user_id);
            $cart_stmt->execute();
            $cart_result = $cart_stmt->get_result();
            $cart_data = $cart_result->fetch_assoc();
            $total_price = $cart_data['total_price'] ?? 0;

            if ($total_price >= $coupon['min_purchase']) {
                $discount = min(($coupon['discount_percentage'] / 100) * $total_price, $coupon['max_discount']);
                $_SESSION['coupon_code'] = $coupon['code'];
                $_SESSION['discount_amount'] = $discount;

                $response = [
                    'status' => 'success',
                    'message' => 'Coupon applied successfully!',
                    'discount' => $discount,
                    'new_total' => max(0, $total_price - $discount)
                ];
            } else {
                $response['message'] = "Minimum purchase ₹" . number_format($coupon['min_purchase']) . " required.";
            }
        } else {
            $response['message'] = "Invalid or expired coupon.";
        }
    } else {
        $response['message'] = "Please enter a coupon code.";
    }
}

echo json_encode($response);
?>
