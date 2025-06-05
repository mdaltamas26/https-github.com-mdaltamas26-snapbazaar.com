<?php
session_start();
include 'db.php'; // Database connection

// Check if Admin is Logged In
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle Coupon Creation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code = strtoupper(trim($_POST['code']));
    $discount = floatval($_POST['discount']);
    $expiry_date = $_POST['expiry_date'];
    
    // Check if coupon already exists
    $check = $conn->prepare("SELECT id FROM coupons WHERE code = ?");
    $check->bind_param("s", $code);
    $check->execute();
    $result = $check->get_result();
    
    if ($result->num_rows > 0) {
        $message = "Coupon code already exists!";
    } else {
        // Insert new coupon
        $stmt = $conn->prepare("INSERT INTO coupons (code, discount, expiry_date) VALUES (?, ?, ?)");
        $stmt->bind_param("sds", $code, $discount, $expiry_date);
        if ($stmt->execute()) {
            $message = "Coupon created successfully!";
        } else {
            $message = "Failed to create coupon.";
        }
    }
}
?><!DOCTYPE html><html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Coupon - Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background: #f8f9fa; }
        .container { max-width: 600px; margin-top: 50px; }
        .card { padding: 20px; border-radius: 10px; }
    </style>
</head>
<body><div class="container">
    <h2 class="text-center">🎟 Create Coupon</h2>
    <div class="card shadow-sm">
        <?php if (isset($message)): ?>
            <div class="alert alert-info"> <?= $message ?> </div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Coupon Code</label>
                <input type="text" name="code" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Discount (%)</label>
                <input type="number" name="discount" class="form-control" step="0.01" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Expiry Date</label>
                <input type="date" name="expiry_date" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Create Coupon</button>
        </form>
    </div>
    <a href="dashboard.php" class="btn btn-secondary mt-3 w-100">⬅ Back to Dashboard</a>
</div></body>
</html>