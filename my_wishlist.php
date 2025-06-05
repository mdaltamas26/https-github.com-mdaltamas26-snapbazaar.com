<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login to view wishlist!'); window.location.href='login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT products.* FROM wishlist JOIN products ON wishlist.product_id = products.id WHERE wishlist.user_id = '$user_id'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Wishlist - SnapBazaar</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- jQuery CDN (for AJAX) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body {
            background-color: #f8f9fa;
        }
        .wishlist-card {
            transition: all 0.3s ease;
        }
        .wishlist-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .wishlist-img {
            height: 250px;
            object-fit: contain;
        }
        .btn-remove {
            background-color: #dc3545;
            color: #fff;
        }
        .btn-remove:hover {
            background-color: #bb2d3b;
        }
        .btn-custom-cart {
            background: linear-gradient(to right, #28a745, #218838);
            color: #fff;
            font-weight: bold;
            border: none;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }
        .btn-custom-cart:hover {
            background: linear-gradient(to right, #218838, #1e7e34);
        }
        .btn-custom-remove {
            font-weight: bold;
            border: 1px solid rgba(220, 53, 70, 0.5);
        }
        .btn-custom-remove:hover {
            background-color: rgba(220, 53, 70, 0.45);
            color: #fff;
        }
        .top-buttons {
            margin-top: 20px;
            text-align: center;
        }
        .top-buttons a {
            margin: 0 10px;
        }
    </style>
</head>
<body>

<div class="container mt-3 top-buttons">
    <a href="home.php" class="btn btn-outline-primary">Home</a>
    <a href="shop.php" class="btn btn-outline-primary">Shop</a>
    <a href="profile.php" class="btn btn-outline-primary">Profile</a>
</div>

<div class="container mt-4">
    <h2 class="text-center mb-4">❤ My Wishlist</h2>

    <div class="row">
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <div class="col-md-4 mb-4">
                <div class="card wishlist-card">
                    <img src="uploads/<?= $row['image']; ?>" class="card-img-top wishlist-img" alt="<?= htmlspecialchars($row['name']); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($row['name']); ?></h5>
                        <p class="card-text text-muted">₹<?= $row['price']; ?></p>

                        <!-- Add to Cart -->
                        <form action="add_to_cart.php" method="POST" class="mb-2">
                            <input type="hidden" name="product_id" value="<?= $row['id']; ?>">
                            <button type="submit" class="btn btn-custom-cart w-100">🛒 Add to Cart</button>
                        </form>

                        <!-- Remove from Wishlist -->
                        <button class="btn btn-outline-danger btn-custom-remove w-100 remove-wishlist" data-product-id="<?= $row['id']; ?>">
                            ❌ Remove
                        </button>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<script>
$(document).ready(function() {
    $(".remove-wishlist").click(function() {
        var button = $(this);
        var product_id = button.data("product-id");

        $.ajax({
            url: "wishlist.php",
            type: "POST",
            data: { product_id: product_id },
            success: function(response) {
                if (response.trim() === "removed") {
                    button.closest(".col-md-4").fadeOut();
                }
            }
        });
    });
});
</script>

</body>
</html>