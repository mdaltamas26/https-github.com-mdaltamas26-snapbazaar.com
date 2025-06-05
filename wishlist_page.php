<?php
session_start();
include 'db.php';

// 🔐 Check if user is logged in
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist - SnapBazaar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
    <h2 class="text-center">❤ My Wishlist</h2>

    <div class="row">
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="<?= $row['image']; ?>" class="card-img-top" alt="<?= $row['name']; ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= $row['name']; ?></h5>
                        <p class="card-text">₹<?= $row['price']; ?></p>
                        
                        <!-- 🛒 Add to Cart -->
                        <form action="add_to_cart.php" method="POST" class="mb-2">
                            <input type="hidden" name="product_id" value="<?= $row['id']; ?>">
                            <button type="submit" class="btn btn-success w-100">🛒 Add to Cart</button>
                        </form>

                        <!-- ❌ Remove from Wishlist -->
                        <button class="btn btn-danger w-100 remove-wishlist" data-product-id="<?= $row['id']; ?>">❌ Remove</button>
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
                if (response.trim() == "removed") {
                    button.closest(".col-md-4").fadeOut();
                }
            }
        });
    });
});
</script>

</body>
</html>