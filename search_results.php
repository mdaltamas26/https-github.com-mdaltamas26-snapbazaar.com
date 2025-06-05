<?php
session_start();
include 'db_connect.php'; // Database Connection

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($searchQuery) {
    $query = "SELECT * FROM products WHERE name LIKE ?";
    $stmt = $conn->prepare($query);
    $searchQuery = '%' . $searchQuery . '%';
    $stmt->bind_param("s", $searchQuery);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results | SnapBazaar</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #333;
            overflow: hidden;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 14px 20px;
        }
        .container {
            text-align: center;
            padding: 50px;
        }
        .products {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        .product-card {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 200px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="navbar">
    <div>
        <a href="home.php">Home</a>
        <a href="shop.php">Shop</a>
        <a href="cart.php">Cart</a>
        <a href="Profile.php">Profile</a>
    </div>
    <div>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<div class="container">
    <h1>Search Results for "<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>"</h1>

    <?php
    if ($result && $result->num_rows > 0) {
        echo '<div class="products">';
        while ($row = $result->fetch_assoc()) {
            echo '<div class="product-card">
                    <img src="uploads/' . htmlspecialchars($row['image']) . '" width="150" height="150" alt="' . htmlspecialchars($row['name']) . '">
                    <h3>' . htmlspecialchars($row['name']) . '</h3>
                    <p>₹' . htmlspecialchars($row['price']) . '</p>
                    <a href="add_to_cart.php?id=' . $row['id'] . '" class="btn">Add to Cart</a>
                  </div>';
        }
        echo '</div>';
    } else {
        echo "<p>No products found for your search.</p>";
    }
    ?>
</div>

</body>
</html>