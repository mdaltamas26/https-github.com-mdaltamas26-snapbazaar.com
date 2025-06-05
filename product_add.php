<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $stock = $_POST['stock'];
    $is_new = isset($_POST['is_new']) ? 1 : 0;
    $is_sale = isset($_POST['is_sale']) ? 1 : 0;

    // Product Details for specific categories
    $extra_fields = json_encode($_POST['extra_fields']);

    // Main Image Upload
    $image = $_FILES['image']['name'];
    $target = "uploads/" . basename($image);

    // Multiple Images Upload
    $imageNames = [];
    if (!file_exists('uploads')) {
        mkdir('uploads', 0777, true);
    }
    foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
        $imgName = $_FILES['images']['name'][$key];
        $imgPath = "uploads/" . basename($imgName);
        if (move_uploaded_file($tmp_name, $imgPath)) {
            $imageNames[] = $imgName;
        }
    }
    $allImages = json_encode($imageNames);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, category, stock, image, images, is_new, is_sale, extra_fields) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdssssiis", $name, $description, $price, $category, $stock, $image, $allImages, $is_new, $is_sale, $extra_fields);

        if ($stmt->execute()) {
            $_SESSION['success'] = "✅ Product successfully added!";
        } else {
            $_SESSION['error'] = "❌ Error: " . $conn->error;
        }
    } else {
        $_SESSION['error'] = "❌ Error uploading image!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product - SnapBazaar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f4f4; }
        .container { max-width: 700px; margin-top: 50px; }
        .card { padding: 25px; background: #fff; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <a href="dashboard.php" class="navbar-brand">Dashboard</a>
        <a href="admin_products.php" class="navbar-brand">Back to Products</a>
    </div>
</nav>
<div class="container">
    <h2 class="text-center">➕ Add New Product</h2>
    <?php if (isset($_SESSION['success'])) { ?>
        <div class="alert alert-success"> <?= $_SESSION['success']; unset($_SESSION['success']); ?> </div>
    <?php } ?>
    <?php if (isset($_SESSION['error'])) { ?>
        <div class="alert alert-danger"> <?= $_SESSION['error']; unset($_SESSION['error']); ?> </div>
    <?php } ?>
    <div class="card">
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Product Name</label>
                <input type="text" class="form-control" name="name" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="description" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Price (₹)</label>
                <input type="number" step="0.01" class="form-control" name="price" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Category</label>
                <select class="form-control" name="category" id="category-select" required>
                    <option value="">-- Select Category --</option>
                    <option value="Mobile">Mobile</option>
                    <option value="Clothing">Clothing</option>
                    <option value="Home Appliances">Home Appliances</option>
                    <option value="Furniture">Furniture</option>
                    <option value="Books">Books</option>
                    <option value="Laptop">Laptop</option>
                    <option value="Faishon">Faishon</option>
                </select>
            </div>
            <div id="extra-fields"></div>
            <div class="mb-3">
                <label class="form-label">Stock</label>
                <input type="number" class="form-control" name="stock" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Main Image</label>
                <input type="file" class="form-control" name="image" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Additional Images</label>
                <input type="file" class="form-control" name="images[]" multiple>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" name="is_new" id="is_new">
                <label class="form-check-label" for="is_new">Mark as New</label>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" name="is_sale" id="is_sale">
                <label class="form-check-label" for="is_sale">Mark as On Sale</label>
            </div>
            <button type="submit" class="btn btn-primary">Add Product</button>
        </form>
    </div>
</div>
<script>
document.getElementById('category-select').addEventListener('change', function() {
    let category = this.value;
    let extraFieldsDiv = document.getElementById('extra-fields');
    extraFieldsDiv.innerHTML = '';

    if (category === 'Mobile') {
        extraFieldsDiv.innerHTML = `
            <div class="mb-3">
                <label class="form-label">RAM</label>
                <input type="text" class="form-control" name="extra_fields[ram]">
            </div>
            <div class="mb-3">
                <label class="form-label">ROM</label>
                <input type="text" class="form-control" name="extra_fields[rom]">
            </div>
            <div class="mb-3">
                <label class="form-label">Battery</label>
                <input type="text" class="form-control" name="extra_fields[battery]">
            </div>
            <div class="mb-3">
                <label class="form-label">Processor</label>
                <input type="text" class="form-control" name="extra_fields[processor]">
            </div>
        `;
    } else if (category === 'Clothing') {
        extraFieldsDiv.innerHTML = `
            <div class="mb-3">
                <label class="form-label">Size</label>
                <input type="text" class="form-control" name="extra_fields[size]">
            </div>
            <div class="mb-3">
                <label class="form-label">Material</label>
                <input type="text" class="form-control" name="extra_fields[material]">
            </div>
        `;
    } else if (category === 'Books') {
        extraFieldsDiv.innerHTML = `
            <div class="mb-3">
                <label class="form-label">Author</label>
                <input type="text" class="form-control" name="extra_fields[author]">
            </div>
            <div class="mb-3">
                <label class="form-label">Publisher</label>
                <input type="text" class="form-control" name="extra_fields[publisher]">
            </div>
        `;
    } else if (category === 'Clothing') {
        extraFieldsDiv.innerHTML = `
            <div class="mb-3">
                <label class="form-label">Size</label>
                <input type="text" class="form-control" name="extra_fields[size]">
            </div>
            <div class="mb-3">
                <label class="form-label">Material</label>
                <input type="text" class="form-control" name="extra_fields[material]">
            </div>
        `;
    } else if (category === 'Laptop') {
        extraFieldsDiv.innerHTML = `
            <div class="mb-3">
                <label class="form-label">Ram</label>
                <input type="text" class="form-control" name="extra_fields[ram]">
            </div>
            <div class="mb-3">
                <label class="form-label">Rom</label>
                <input type="text" class="form-control" name="extra_fields[rom]">
            </div>
            <div class="mb-3">
                <label class="form-label">Processor</label>
                <input type="text" class="form-control" name="extra_fields[processor]">
            </div>
            <div class="mb-3">
                <label class="form-label">Scareen Size</label>
                <input type="text" class="form-control" name="extra_fields[scareen size]">
            </div>
            <div class="mb-3">
                <label class="form-label">Weight</label>
                <input type="text" class="form-control" name="extra_fields[weight]">
            </div>
        `;
    }
});
</script>
</body>
</html>