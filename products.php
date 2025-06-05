<?php
session_start();
include 'db.php'; // Database connection

// Products fetch karna
$sql = "SELECT * FROM products";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
</head>
<body>

<h2>All Products</h2>

<?php while ($row = $result->fetch_assoc()): ?>
    <div>
        <h3><?= $row['name'] ?></h3>
        <p>Price: ₹<?= $row['price'] ?></p>

        <!-- Add to Cart Button -->
        <form action="add_to_cart.php" method="POST">
            <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
            <button type="submit">Add to Cart</button>
        </form>
    </div>
    <hr>
<?php endwhile; ?>

<!-- Cart Page Link -->
<a href="cart.php">Go to Cart</a>

</body>
</html>