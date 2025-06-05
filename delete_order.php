<?php
include 'db.php';

if (isset($_GET['id'])) {
    $order_id = intval($_GET['id']);

    $delete = $conn->query("DELETE FROM my_orders WHERE id = $order_id");

    if ($delete) {
        header("Location: manage_orders.php?msg=Order+deleted+successfully");
    } else {
        header("Location: manage_orders.php?error=Failed+to+delete+order");
    }
    exit();
} else {
    header("Location: manage_orders.php?error=Invalid+request");
    exit();
}
