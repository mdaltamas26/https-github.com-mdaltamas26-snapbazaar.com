<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = trim($_POST['code']);
    $discount = intval($_POST['discount']);

    if (!empty($code) && $discount > 0) {
        $stmt = $conn->prepare("INSERT INTO coupons (code, discount, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("si", $code, $discount);

        if ($stmt->execute()) {
            header("Location: manage_coupons.php?msg=Coupon+added+successfully");
        } else {
            header("Location: manage_coupons.php?error=Failed+to+add+coupon");
        }
    } else {
        header("Location: manage_coupons.php?error=Invalid+input");
    }
}
