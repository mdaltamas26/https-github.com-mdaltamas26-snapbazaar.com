<?php
session_start();

// Remove applied coupon
unset($_SESSION['coupon_code']);
unset($_SESSION['discount_amount']);

echo json_encode([
    'status' => 'success',
    'message' => 'Coupon removed successfully.'
]);
?>
