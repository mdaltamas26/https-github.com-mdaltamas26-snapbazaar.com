<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn->query("DELETE FROM coupons WHERE id = $id");
    header("Location: manage_coupons.php?msg=Coupon+deleted+successfully");
    exit();
}
