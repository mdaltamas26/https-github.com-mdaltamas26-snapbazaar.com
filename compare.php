<?php
include 'db.php';

$product_ids = isset($_GET['products']) ? explode(',', $_GET['products']) : [];

if (count($product_ids) < 2) {
    echo "Please select at least 2 products to compare.";
    exit;
}

$placeholders = implode(',', array_fill(0, count($product_ids), '?'));
$stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
$stmt->bind_param(str_repeat('i', count($product_ids)), ...$product_ids);
$stmt->execute();
$result = $stmt->get_result();
$products = [];
$all_features = ['Price', 'Category', 'Description'];

while ($row = $result->fetch_assoc()) {
    $extra = [];
    if (!empty($row['extra_details'])) {
        $extra = json_decode($row['extra_details'], true);
        if (is_array($extra)) {
            $all_features = array_merge($all_features, array_keys($extra));
        }
    }

    // Fetch gallery images for this product
    $product_id = $row['id'];
    $images_res = mysqli_query($conn, "SELECT image FROM product_images WHERE product_id = $product_id");
    $gallery_images = [];
    while ($img = mysqli_fetch_assoc($images_res)) {
        $gallery_images[] = $img['image'];
    }

    $row['extra_decoded'] = $extra;
    $row['gallery_images'] = $gallery_images;
    $products[] = $row;
}
$all_features = array_unique($all_features);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Compare Products - SnapBazaar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; font-family: Arial, sans-serif; }
    .compare-card {
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 15px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.05);
      text-align: center;
      padding: 15px;
      margin-bottom: 20px;
    }
    .compare-card img {
      height: 180px;
      object-fit: contain;
      margin-bottom: 10px;
    }
    .gallery-img {
      height: 60px;
      width: auto;
      margin: 3px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    .compare-table th {
      background-color: #343a40;
      color: white;
    }
    .badge {
      font-size: 0.75rem;
    }
  </style>
</head>
<body>
<div class="container my-4">
  <h2 class="text-center mb-4">Compare Products</h2>
  <div class="row mb-4">
    <?php foreach ($products as $product): ?>
    <div class="col-md-<?php echo 12 / count($products); ?>">
      <div class="compare-card">
        <img src="uploads/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="img-fluid mb-2">
        <h5><?= htmlspecialchars($product['name']) ?></h5>
        <p><strong>₹<?= number_format($product['price'], 2) ?></strong></p>
        <?php if ($product['is_new']) echo '<span class="badge bg-success">New</span> '; ?>
        <?php if ($product['is_sale']) echo '<span class="badge bg-danger">Sale</span>'; ?>

        <div class="mt-3">
          <?php foreach ($product['gallery_images'] as $gallery_img): ?>
            <img src="uploads/<?= htmlspecialchars($gallery_img) ?>" class="gallery-img" alt="Gallery Image">
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <table class="table table-bordered compare-table text-center">
    <thead>
      <tr>
        <th>Feature</th>
        <?php foreach ($products as $product): ?>
        <th><?= htmlspecialchars($product['name']) ?></th>
        <?php endforeach; ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($all_features as $feature): ?>
      <tr>
        <td><?= htmlspecialchars($feature) ?></td>
        <?php foreach ($products as $product): ?>
        <td>
          <?php
          if ($feature == 'Price') {
              echo '₹' . number_format($product['price'], 2);
          } elseif ($feature == 'Category') {
              echo htmlspecialchars($product['category']);
          } elseif ($feature == 'Description') {
              echo nl2br(htmlspecialchars($product['description']));
          } else {
              echo isset($product['extra_decoded'][$feature]) ? htmlspecialchars($product['extra_decoded'][$feature]) : '-';
          }
          ?>
        </td>
        <?php endforeach; ?>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="text-center">
    <a href="shop.php" class="btn btn-primary mt-3">Back to Shop</a>
  </div>
</div>
</body>
</html>