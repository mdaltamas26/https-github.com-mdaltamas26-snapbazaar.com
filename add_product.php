<?php
session_start();
include 'db.php'; // Database connection

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: admin_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 📝 Handle Product Addition
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $stock = $_POST['stock'];

    // Upload folder check and create if it doesn't exist
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Image upload processing
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        // Image filename to store in database
        $image = basename($_FILES["image"]["name"]);

        // SQL Query to Insert Product
        $sql = "INSERT INTO products (name, price, description, category, stock, image) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdssis", $name, $price, $description, $category, $stock, $image);

        if ($stmt->execute()) {
            $_SESSION['success'] = "✅ Product added successfully!";
            header("Location: profile.php"); // Redirect to profile page
            exit();
        } else {
            $_SESSION['error'] = "❌ Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $_SESSION['error'] = "❌ Error uploading file!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - SnapBazaar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<!-- 🔹 Navbar -->
<nav class="navbar">
    <a href="home.php">🏠 Home</a>
    <a href="shop.php">🛍 Shop</a>
    <a href="cart.php">🛒 Cart</a>
    <a href="profile.php">👤 Profile</a>
    <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
</nav>

<div class="container">
    <h2 class="text-center mt-5">🛒 Add New Product</h2>

    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['success'])) { ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php } ?>
    <?php if (isset($_SESSION['error'])) { ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php } ?>

    <form action="add_product.php" method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="name" class="form-label">Product Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Price</label>
            <input type="number" name="price" id="price" class="form-control" step="0.01" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <input type="text" name="category" id="category" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="stock" class="form-label">Stock Quantity</label>
            <input type="number" name="stock" id="stock" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Product Image</label>
            <input type="file" name="image" id="image" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Add Product</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
