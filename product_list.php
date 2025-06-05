<?php
session_start();
include 'db.php';

// 🔹 Fetch All Products
$query = "SELECT * FROM products";
$result = mysqli_query($conn, $query);
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - SnapBazaar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* ❤ Wishlist Button - Top Right Corner */
        .wishlist-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 255, 255, 0.8);
            border: none;
            font-size: 24px;
            padding: 5px 8px;
            border-radius: 50%;
            cursor: pointer;
            transition: 0.3s;
        }

        .wishlist-btn.active {
            color: red;
        }

        .wishlist-btn:hover {
            background: rgba(255, 255, 255, 1);
        }

        .product-card {
            position: relative;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <h2 class="text-center">🛍 Shop Now</h2>
    <div class="row">
        <?php while ($row = mysqli_fetch_assoc($result)) { 
            // 🔹 Check if the product is already in wishlist
            $product_id = $row['id'];
            $wishlist_query = "SELECT * FROM wishlist WHERE user_id = '$user_id' AND product_id = '$product_id'";
            $wishlist_result = mysqli_query($conn, $wishlist_query);
            $is_in_wishlist = mysqli_num_rows($wishlist_result) > 0;
        ?>
            <div class="col-md-4 mb-4">
                <div class="card product-card">
                    <img src="<?= $row['image']; ?>" class="card-img-top" alt="<?= $row['name']; ?>">
                    <button class="wishlist-btn <?= $is_in_wishlist ? 'active' : '' ?>" 
                            data-product-id="<?= $row['id']; ?>">
                        ❤
                    </button>
                    <div class="card-body">
                        <h5 class="card-title"><?= $row['name']; ?></h5>
                        <p class="card-text">₹<?= $row['price']; ?></p>
                        <form action="add_to_cart.php" method="POST">
                            <input type="hidden" name="product_id" value="<?= $row['id']; ?>">
                            <button type="submit" class="btn btn-success w-100">🛒 Add to Cart</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<script>
$(document).ready(function() {
    $(".wishlist-btn").click(function() {
        var button = $(this);
        var product_id = button.data("product-id");

        $.ajax({
            url: "wishlist.php",
            type: "POST",
            data: { product_id: product_id },
            success: function(response) {
                if (response.trim() == "added") {
                    button.addClass("active");
                } else if (response.trim() == "removed") {
                    button.removeClass("active");
                }
            }
        });
    });
});
</script>

</body>
</html>