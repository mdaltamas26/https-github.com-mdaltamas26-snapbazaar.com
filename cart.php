<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);

$cart_query = "SELECT c.*, p.name, p.price, p.image FROM cart c 
               JOIN products p ON c.product_id = p.id 
               WHERE c.user_id = '$user_id'";
$cart_result = mysqli_query($conn, $cart_query);

$total_price = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shopping Cart | SnapBazaar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .qty-btn {
            padding: 4px 10px;
            font-size: 16px;
            font-weight: bold;
        }
        .qty-input {
            width: 50px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <div class="card p-4">
        <h2 class="text-center">🛒 Your Shopping Cart</h2>

        <?php if (mysqli_num_rows($cart_result) == 0): ?>
            <p class="text-center mt-3">Your cart is empty.</p>
            <div class="text-center mt-3">
                <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
            </div>
        <?php else: ?>
            <table class="table mt-4 text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Product</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($cart_result)) {
                        $total = $row['price'] * $row['quantity'];
                        $total_price += $total;
                    ?>
                        <tr>
                            <td><img src="uploads/<?= $row['image'] ?>" width="60"></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td>₹<?= number_format($row['price'], 2) ?></td>
                            <td>
                                <form method="post" action="cart_update.php" class="d-flex justify-content-center align-items-center">
                                    <button type="submit" name="decrease" value="<?= $row['id'] ?>" class="btn btn-sm btn-secondary qty-btn">−</button>
                                    <input type="text" class="form-control qty-input mx-1" value="<?= $row['quantity'] ?>" readonly>
                                    <button type="submit" name="increase" value="<?= $row['id'] ?>" class="btn btn-sm btn-secondary qty-btn">+</button>
                                </form>
                            </td>
                            <td>₹<?= number_format($total, 2) ?></td>
                            <td>
                        
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <div class="text-end">
                <h4><strong>Total: ₹<?= number_format($total_price, 2) ?></strong></h4>
            </div>

            <div class="text-center mt-3">
                <a href="checkout.php" class="btn btn-success btn-lg">Proceed to Checkout</a>
                <a href="shop.php" class="btn btn-secondary">Continue Shopping</a>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
