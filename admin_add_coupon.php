<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code = strtoupper(trim($_POST['code']));
    $discount = $_POST['discount'];
    $type = $_POST['type'];
    $min_order = $_POST['min_order'];
    $usage_limit = $_POST['usage_limit'];
    $expiration_date = $_POST['expiration_date'];

    $stmt = $conn->prepare("INSERT INTO coupons (code, discount, type, min_order, usage_limit, expiration_date) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sdsdis", $code, $discount, $type, $min_order, $usage_limit, $expiration_date);

    if ($stmt->execute()) {
        echo "<script>alert('Coupon Added Successfully'); window.location='admin_add_coupon.php';</script>";
    } else {
        echo "<script>alert('Error adding coupon');</script>";
    }
}
?>

<form method="POST">
    <label>Coupon Code:</label>
    <input type="text" name="code" required><br>
    
    <label>Discount Value:</label>
    <input type="number" name="discount" step="0.01" required><br>
    
    <label>Type:</label>
    <select name="type">
        <option value="percentage">Percentage (%)</option>
        <option value="fixed">Fixed Amount (₹)</option>
    </select><br>
    
    <label>Min Order Amount:</label>
    <input type="number" name="min_order" step="0.01"><br>
    
    <label>Usage Limit:</label>
    <input type="number" name="usage_limit"><br>
    
    <label>Expiration Date:</label>
    <input type="date" name="expiration_date" required><br>

    <button type="submit">Add Coupon</button>
</form>