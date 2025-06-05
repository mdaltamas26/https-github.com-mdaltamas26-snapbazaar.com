<?php
session_start();
include 'db.php';

$category_filter = $_GET['category'] ?? '';
$sort_filter = $_GET['sort'] ?? '';
$search = $_GET['search'] ?? '';

$query = "SELECT * FROM products WHERE 1";
if ($category_filter != '' && $category_filter != 'all') {
    $query .= " AND category = '$category_filter'";
}
if ($search != '') {
    $query .= " AND name LIKE '%$search%'";
}

switch ($sort_filter) {
    case 'price_asc':
        $query .= " ORDER BY price ASC";
        break;
    case 'price_desc':
        $query .= " ORDER BY price DESC";
        break;
    case 'latest':
    default:
        $query .= " ORDER BY created_at DESC";
        break;
}

$limit = 9;
$page = $_GET['page'] ?? 1;
$offset = ($page - 1) * $limit;
$total_result = mysqli_query($conn, $query);
$total_products = mysqli_num_rows($total_result);
$total_pages = ceil($total_products / $limit);

$query .= " LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

$wishlist = [];
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $wishlist_query = "SELECT product_id FROM wishlist WHERE user_id = '$user_id'";
    $wishlist_result = mysqli_query($conn, $wishlist_query);
    while ($wish = mysqli_fetch_assoc($wishlist_result)) {
        $wishlist[] = $wish['product_id'];
    }
}

// Manual categories
$manual_categories = ['Electronics', 'Fashion', 'Home & Kitchen', 'Beauty', 'Sports', 'Books', 'Grocery', 'Toys', 'Automotive', 'Health'];
?>
<!DOCTYPE html><html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Shop - SnapBazaar</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    body { background-color: #f1f3f6; font-family: 'Segoe UI', sans-serif; }
    .card { border: none; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1); transition: 0.3s; }
    .card:hover { transform: translateY(-5px); }
    .card img { height: 250px; object-fit: cover; width: 100%; }
    .wishlist-btn { position: absolute; top: 10px; right: 10px; background: #fff; border: none; border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; }
    .wishlist-btn.active span { color: red; }
    .badge-new { background-color: green; color: white; }
    .badge-sale { background-color: red; color: white; }
    .compare-btn { background-color: #007bff; color: white; border-radius: 30px; padding: 8px 20px; border: none; margin-top: 10px; }
    .filter-bar select, .filter-bar input { max-width: 200px; }
    .pagination .active { font-weight: bold; background-color: #343a40; color: white; }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="home.php">SnapBazaar</a>
    <form class="d-flex" method="GET">
      <input class="form-control me-2" name="search" type="search" placeholder="Search products..." value="<?= htmlspecialchars($search) ?>">
      <button class="btn btn-outline-light" type="submit">Search</button>
    </form>
    <ul class="navbar-nav ms-auto">
      <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
      <li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>
      <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
    </ul>
  </div>
</nav><div class="container mt-4">
  <form method="GET" class="row g-2 filter-bar align-items-center mb-4">
    <div class="col-md-3">
      <select name="category" class="form-select" onchange="this.form.submit()">
        <option value="all">All Categories</option>
        <?php foreach ($manual_categories as $cat) { ?>
          <option value="<?= $cat ?>" <?= $category_filter == $cat ? 'selected' : '' ?>>
            <?= $cat ?>
          </option>
        <?php } ?>
      </select>
    </div>
    <div class="col-md-3">
      <select name="sort" class="form-select" onchange="this.form.submit()">
        <option value="latest" <?= $sort_filter == 'latest' ? 'selected' : '' ?>>Newest</option>
        <option value="price_asc" <?= $sort_filter == 'price_asc' ? 'selected' : '' ?>>Price Low to High</option>
        <option value="price_desc" <?= $sort_filter == 'price_desc' ? 'selected' : '' ?>>Price High to Low</option>
      </select>
    </div>
  </form>  <div class="row">
    <?php while ($row = mysqli_fetch_assoc($result)) {
      $product_id = $row['id'];
      $is_in_wishlist = in_array($product_id, $wishlist) ? 'active' : ''; ?>
      <div class="col-md-4 mb-4">
        <div class="card position-relative">
          <img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
          <button class="wishlist-btn <?= $is_in_wishlist ?>" data-product-id="<?= $product_id ?>">
            <span class="heart">❤</span>
          </button>
          <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
            <p class="card-text fw-bold">₹<?= number_format($row['price'], 2) ?></p>
            <?php if ($row['is_new']) echo '<span class="badge badge-new">New</span>'; ?>
            <?php if ($row['is_sale']) echo '<span class="badge badge-sale ms-2">Sale</span>'; ?>
            <form action="add_to_cart.php" method="POST" class="mt-3">
              <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
              <button type="submit" class="btn btn-success w-100">Add to Cart</button>
            </form>
            <div class="form-check mt-2">
              <input class="form-check-input compare-checkbox" type="checkbox" value="<?= $row['id'] ?>">
              <label class="form-check-label">Compare</label>
            </div>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>  <div class="text-center my-3">
    <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
      <a href="?page=<?= $i ?>&category=<?= $category_filter ?>&search=<?= $search ?>&sort=<?= $sort_filter ?>" class="btn btn-sm btn-outline-dark mx-1 <?= $page == $i ? 'active' : '' ?>">Page <?= $i ?></a>
    <?php } ?>
  </div>  <div class="text-end">
    <button id="compareNowBtn" class="compare-btn">Compare Now</button>
  </div>
</div><script src="https://code.jquery.com/jquery-3.6.0.min.js"></script><script>
$(document).ready(function () {
  $(".wishlist-btn").click(function () {
    var btn = $(this);
    var product_id = btn.data("product-id");
    $.post("wishlist.php", { product_id: product_id }, function (response) {
      if (response === "added") btn.addClass("active");
      else if (response === "removed") btn.removeClass("active");
    });
  });

  $('#compareNowBtn').click(function () {
    const selected = $('.compare-checkbox:checked').map(function () {
      return $(this).val();
    }).get();
    if (selected.length < 2 || selected.length > 3) {
      alert("Select 2 or 3 products to compare.");
      return;
    }
    window.location.href = 'compare.php?products=' + selected.join(',');
  });
});
</script><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script></body>
</html>