<?php
session_start();
include 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle Product Deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // Check if product is linked to any orders or refund requests
    $check_orders = mysqli_query($conn, "SELECT id FROM order_items WHERE product_id = $id LIMIT 1");
    $check_refunds = mysqli_query($conn, "SELECT id FROM refund_requests WHERE product_id = $id LIMIT 1");

    if (mysqli_num_rows($check_orders) > 0 || mysqli_num_rows($check_refunds) > 0) {
        $_SESSION['error'] = "❌ Cannot delete: Product is linked to existing orders or refund requests!";
    } else {
        $delete_query = "DELETE FROM products WHERE id = $id";
        if (mysqli_query($conn, $delete_query)) {
            $_SESSION['success'] = "✅ Product deleted successfully!";
        } else {
            $_SESSION['error'] = "❌ Deletion failed. Please try again.";
        }
    }

    header("Location: admin_products.php");
    exit();
}

// Fetch Products
$products_query = "SELECT * FROM products ORDER BY created_at DESC";
$products_result = mysqli_query($conn, $products_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products - SnapBazaar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">📦 Manage Products</h2>
    <a href="dashboard.php" class="btn btn-primary mb-3">🏠 Back to Dashboard</a>
    <a href="product_add.php" class="btn btn-success mb-3">➕ Add New Product</a>

    <?php if (isset($_SESSION['success'])) { ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php } ?>

    <?php if (isset($_SESSION['error'])) { ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php } ?>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Price</th>
                <th>Category</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($product = mysqli_fetch_assoc($products_result)) { ?>
                <tr>
                    <td><img src="../images/<?= $product['image']; ?>" width="50"></td>
                    <td><?= htmlspecialchars($product['name']); ?></td>
                    <td>₹<?= number_format($product['price'], 2); ?></td>
                    <td><?= htmlspecialchars($product['category']); ?></td>
                    <td><?= $product['stock']; ?></td>
                    <td>
                        <a href="admin_edit_product.php?id=<?= $product['id']; ?>" class="btn btn-warning btn-sm">✏ Edit</a>
                        <a href="?delete=<?= $product['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">🗑 Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>