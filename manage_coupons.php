<?php
session_start();
include 'db.php';

// Admin check
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch coupons
$coupons = $conn->query("SELECT * FROM coupons ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Coupons - SnapBazaar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Manage Coupons</h2>

    <!-- Flash messages -->
    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <!-- Add Coupon Form -->
    <form action="add_coupon.php" method="POST" class="mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <input type="text" name="code" class="form-control" placeholder="Coupon Code" required>
            </div>
            <div class="col-md-4">
                <input type="number" name="discount" class="form-control" placeholder="Discount (%)" required min="1" max="100">
            </div>
            <div class="col-md-4 d-grid">
                <button type="submit" class="btn btn-success">Add Coupon</button>
            </div>
        </div>
    </form>

    <!-- Coupon Table -->
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
        <a href="dashboard.php" class="btn btn-secondary btn-back">&lAarr; Back to Dashboard</a>
        <tr>
            <th>ID</th>
            <th>Coupon Code</th>
            <th>Discount (%)</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $coupons->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['code']) ?></td>
                <td><?= $row['discount'] ?>%</td>
                <td><?= date('d M Y, h:i A', strtotime($row['created_at'])) ?></td>
                <td>
                    <a href="delete_coupon.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this coupon?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
