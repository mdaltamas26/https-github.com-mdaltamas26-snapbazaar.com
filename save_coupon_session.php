<?php
session_start();
if (isset($_GET['code']) && isset($_GET['discount'])) {
    $_SESSION['coupon_code'] = $_GET['code'];
    $_SESSION['discount_amount'] = $_GET['discount'];
    echo "saved";
}
?>
