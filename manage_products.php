<?php
session_start();
include 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch all products
$query = "SELECT * FROM products ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Manage Products</h2>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']); ?></div>
    <?php endif; ?>

    <div class="mb-3">
        <a href="product_add.php" class="btn btn-success">+ Add Product</a>
        <a href="dashboard.php" class="btn btn-secondary float-end">← Back to Dashboard</a>
    </div>

    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price (₹)</th>
                <th>Category</th>
                <th>Stock</th>
                <th>Image</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id']; ?></td>
                <td><?= htmlspecialchars($row['name']); ?></td>
                <td><?= number_format($row['price'], 2); ?></td>
                <td><?= htmlspecialchars($row['category']); ?></td>
                <td><?= $row['stock']; ?></td>
                <td>
                    <?php if (!empty($row['image'])): ?>
                        <img src="uploads/<?= $row['image']; ?>" width="60" height="60" style="object-fit:cover;">
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </td>
                <td><?= date("d M Y", strtotime($row['created_at'])); ?></td>
                <td>
                    <a href="admin_edit_product.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                    <a href="delete_product.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure to delete?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
