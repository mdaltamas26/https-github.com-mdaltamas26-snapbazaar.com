<?php
include 'db.php';

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if ($product) {
    echo "<h1>" . $product['name'] . "</h1>";
    echo "<img src='uploads/" . $product['image'] . "' width='200'><br>";
    echo "<p>" . $product['description'] . "</p>";
    echo "<p>Price: ₹" . $product['price'] . "</p>";
    echo "<p>Stock: " . $product['stock'] . "</p>";
    echo "<a href='add_to_cart.php?id=" . $product['id'] . "'>Add to Cart</a>";
} else {
    echo "Product not found!";
}
?>