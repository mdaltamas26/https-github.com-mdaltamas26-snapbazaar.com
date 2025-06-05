<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: admin_products.php");
    exit();
}

$product_id = intval($_GET['id']);
$product_query = "SELECT * FROM products WHERE id = $product_id";
$product_result = mysqli_query($conn, $product_query);
$product = mysqli_fetch_assoc($product_result);

$extra_details = json_decode($product['extra_details'] ?? '{}', true);

// Handle Product Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = floatval($_POST['price']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $stock = intval($_POST['stock']);
    $is_new = isset($_POST['is_new']) ? 1 : 0;
    $on_sale = isset($_POST['on_sale']) ? 1 : 0;

    // Extra fields as JSON
    $extra = [];

    if ($category == "Mobile") {
        $extra['Brand'] = $_POST['brand'];
        $extra['RAM'] = $_POST['ram'];
        $extra['Storage'] = $_POST['storage'];
    } elseif ($category == "Clothing") {
        $extra['Size'] = $_POST['size'];
        $extra['Color'] = $_POST['color'];
        $extra['Material'] = $_POST['material'];
    } elseif ($category == "Books") {
        $extra['Author'] = $_POST['author'];
        $extra['Publisher'] = $_POST['publisher'];
        $extra['Pages'] = $_POST['pages'];
    } elseif ($category == "Laptop") {
        $extra['Brand'] = $_POST['laptop_brand'];
        $extra['Processor'] = $_POST['processor'];
        $extra['RAM'] = $_POST['laptop_ram'];
        $extra['Storage'] = $_POST['laptop_storage'];
        $extra['Weight'] = $_POST['weight'];
    }

    // Auto description
    $extra['AutoDescription'] = $_POST['auto_description'];

    $extra_json = mysqli_real_escape_string($conn, json_encode($extra));

    // Main Image
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $target = "../images/" . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    } else {
        $image = $product['image'];
    }

    // Gallery Images
    $gallery_images = [];
    if (!empty($_FILES['gallery']['name'][0])) {
        foreach ($_FILES['gallery']['tmp_name'] as $key => $tmp_name) {
            $img_name = $_FILES['gallery']['name'][$key];
            $target_path = "../images/" . basename($img_name);
            move_uploaded_file($tmp_name, $target_path);
            $gallery_images[] = $img_name;
        }
    } else {
        $gallery_images = json_decode($product['gallery_images'] ?? '[]', true);
    }

    // Update Query
    $update_query = "UPDATE products SET 
        name='$name', 
        price='$price', 
        category='$category', 
        stock='$stock', 
        image='$image', 
        extra_details='$extra_json',
        gallery_images='" . mysqli_real_escape_string($conn, json_encode($gallery_images)) . "',
        is_new='$is_new',
        on_sale='$on_sale'
        WHERE id='$product_id'";

    mysqli_query($conn, $update_query);
    $_SESSION['success'] = "✅ Product updated successfully!";
    header("Location: admin_products.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product - SnapBazaar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
    function showExtraFields() {
        const category = document.getElementById('category').value;
        document.querySelectorAll('.extra-field').forEach(e => e.style.display = 'none');
        document.querySelectorAll('.extra-' + category).forEach(e => e.style.display = 'block');
    }
    </script>
</head>
<body onload="showExtraFields()">
<div class="container mt-5">
    <h2 class="text-center">✏ Edit Product</h2>
    <a href="admin_products.php" class="btn btn-primary mb-3">🔙 Back to Products</a>

    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" value="<?= htmlspecialchars($product['name']); ?>" required class="form-control mb-3">
        
        <input type="number" name="price" value="<?= $product['price']; ?>" step="0.01" required class="form-control mb-3">
        
        <select name="category" id="category" class="form-control mb-3" onchange="showExtraFields()" required>
            <option value="">-- Select Category --</option>
            <option value="Mobile" <?= $product['category'] == 'Mobile' ? 'selected' : ''; ?>>Mobile</option>
            <option value="Clothing" <?= $product['category'] == 'Clothing' ? 'selected' : ''; ?>>Clothing</option>
            <option value="Books" <?= $product['category'] == 'Books' ? 'selected' : ''; ?>>Books</option>
            <option value="Laptop" <?= $product['category'] == 'Laptop' ? 'selected' : ''; ?>>Laptop</option>
        </select>

        <!-- Mobile -->
        <input type="text" name="brand" class="form-control mb-3 extra-field extra-Mobile" placeholder="Brand" value="<?= $extra_details['Brand'] ?? ''; ?>">
        <input type="text" name="ram" class="form-control mb-3 extra-field extra-Mobile" placeholder="RAM" value="<?= $extra_details['RAM'] ?? ''; ?>">
        <input type="text" name="storage" class="form-control mb-3 extra-field extra-Mobile" placeholder="Storage" value="<?= $extra_details['Storage'] ?? ''; ?>">

        <!-- Clothing -->
        <input type="text" name="size" class="form-control mb-3 extra-field extra-Clothing" placeholder="Size" value="<?= $extra_details['Size'] ?? ''; ?>">
        <input type="text" name="color" class="form-control mb-3 extra-field extra-Clothing" placeholder="Color" value="<?= $extra_details['Color'] ?? ''; ?>">
        <input type="text" name="material" class="form-control mb-3 extra-field extra-Clothing" placeholder="Material" value="<?= $extra_details['Material'] ?? ''; ?>">

        <!-- Books -->
        <input type="text" name="author" class="form-control mb-3 extra-field extra-Books" placeholder="Author" value="<?= $extra_details['Author'] ?? ''; ?>">
        <input type="text" name="publisher" class="form-control mb-3 extra-field extra-Books" placeholder="Publisher" value="<?= $extra_details['Publisher'] ?? ''; ?>">
        <input type="number" name="pages" class="form-control mb-3 extra-field extra-Books" placeholder="Pages" value="<?= $extra_details['Pages'] ?? ''; ?>">

        <!-- Laptop -->
        <input type="text" name="laptop_brand" class="form-control mb-3 extra-field extra-Laptop" placeholder="Brand" value="<?= $extra_details['Brand'] ?? ''; ?>">
        <input type="text" name="processor" class="form-control mb-3 extra-field extra-Laptop" placeholder="Processor" value="<?= $extra_details['Processor'] ?? ''; ?>">
        <input type="text" name="laptop_ram" class="form-control mb-3 extra-field extra-Laptop" placeholder="RAM" value="<?= $extra_details['RAM'] ?? ''; ?>">
        <input type="text" name="laptop_storage" class="form-control mb-3 extra-field extra-Laptop" placeholder="Storage" value="<?= $extra_details['Storage'] ?? ''; ?>">
        <input type="text" name="weight" class="form-control mb-3 extra-field extra-Laptop" placeholder="Weight" value="<?= $extra_details['Weight'] ?? ''; ?>">

        <!-- Auto Description -->
        <?php
        if (empty($extra_details['AutoDescription'])) {
            $auto_parts = [];
            foreach ($extra_details as $key => $val) {
                if ($key != 'AutoDescription') {
                    $auto_parts[] = "$key: $val";
                }
            }
            $auto_description_value = implode(", ", $auto_parts);
        } else {
            $auto_description_value = $extra_details['AutoDescription'];
        }
        ?>
        <textarea name="auto_description" class="form-control mb-3" rows="4" placeholder="Write full product description or any extra details..."><?= htmlspecialchars($auto_description_value); ?></textarea>

        <input type="number" name="stock" value="<?= $product['stock']; ?>" required class="form-control mb-3">

        <label>Main Image</label>
        <input type="file" name="image" class="form-control mb-2">
        <img src="../images/<?= $product['image']; ?>" width="100" class="mb-3"><br>

        <label>Gallery Images</label>
        <input type="file" name="gallery[]" multiple class="form-control mb-2">
        <?php
        $gallery = json_decode($product['gallery_images'] ?? '[]', true);
        foreach ($gallery as $img) {
            echo "<img src='../images/$img' width='80' class='me-2 mb-2'>";
        }
        ?>

        <div class="form-check mb-3 mt-2">
            <input class="form-check-input" type="checkbox" name="is_new" <?= $product['is_new'] ? 'checked' : ''; ?>> 
            <label class="form-check-label">Is New?</label>
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="on_sale" <?= isset($product['on_sale']) && $product['on_sale'] ? 'checked' : ''; ?>> 
            <label class="form-check-label">On Sale?</label>
        </div>

        <button type="submit" class="btn btn-success">✅ Update Product</button>
    </form>
</div>
</body>
</html>