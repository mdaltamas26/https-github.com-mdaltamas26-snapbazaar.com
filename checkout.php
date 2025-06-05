<?php  
session_start();  
include 'db.php';  

if (!isset($_SESSION['user_id'])) {  
    header("Location: login.php");  
    exit();  
}  

$user_id = intval($_SESSION['user_id']);  

// 🏠 Fetch Address  
$address_query = "SELECT * FROM addresses WHERE user_id = '$user_id' LIMIT 1";  
$address_result = mysqli_query($conn, $address_query);  
$address = mysqli_fetch_assoc($address_result);  

// 🛒 Fetch Cart Items  
$cart_query = "SELECT c.*, p.name, p.price, p.image FROM cart c    
               JOIN products p ON c.product_id = p.id    
               WHERE c.user_id = '$user_id'";  
$cart_result = mysqli_query($conn, $cart_query);  

$total_price = 0;  
$cart_items = [];
while ($row = mysqli_fetch_assoc($cart_result)) {
    $row['total'] = $row['price'] * $row['quantity'];
    $total_price += $row['total'];
    $cart_items[] = $row;
}

// 💰 Fetch Wallet Balance
$wallet_query = "SELECT wallet_balance FROM users WHERE id = '$user_id'";
$wallet_result = mysqli_query($conn, $wallet_query);
$wallet_row = mysqli_fetch_assoc($wallet_result);
$wallet_balance = floatval($wallet_row['wallet_balance']);
?>  

<!DOCTYPE html>  
<html lang="en">  
<head>  
  <meta charset="UTF-8">  
  <meta name="viewport" content="width=device-width, initial-scale=1.0">  
  <title>Checkout - SnapBazaar</title>  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">  
  <style>  
    body { background-color: #f8f9fa; }  
    .section-card { border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); margin-bottom: 30px; }  
    .section-header { font-size: 1.25rem; font-weight: 600; background: #343a40; color: white; padding: 15px; border-top-left-radius: 10px; border-top-right-radius: 10px; }  
    .product-img { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; }  
    .btn-lg { padding: 12px 20px; font-size: 1rem; border-radius: 10px; }  
    .btn-success:hover, .btn-warning:hover { opacity: 0.9; transition: 0.3s; }  
    #coupon_message { min-height: 1.5rem; font-weight: 600; }
  </style>  
</head>  
<body>  

<div class="container py-5">  
  <h2 class="mb-4 text-center text-primary">🗞 Checkout</h2>  

  <!-- Address Section -->  
  <div class="section-card bg-white">  
    <div class="section-header">📍 Delivery Address</div>  
    <div class="p-4">  
      <form action="save_address.php" method="POST">  
        <input type="hidden" name="user_id" value="<?= $user_id ?>">  
        <div class="row g-3">  
          <div class="col-md-6">  
            <label class="form-label">Full Name</label>  
            <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($address['full_name'] ?? '') ?>" required>  
          </div>  
          <div class="col-md-6">  
            <label class="form-label">Phone Number</label>  
            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($address['phone'] ?? '') ?>" required>  
          </div>  
          <div class="col-12">  
            <label class="form-label">Full Address</label>  
            <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($address['address'] ?? '') ?>" required>  
          </div>  
          <div class="col-md-4">  
            <input type="text" name="city" class="form-control" placeholder="City" value="<?= htmlspecialchars($address['city'] ?? '') ?>" required>  
          </div>  
          <div class="col-md-4">  
            <input type="text" name="state" class="form-control" placeholder="State" value="<?= htmlspecialchars($address['state'] ?? '') ?>" required>  
          </div>  
          <div class="col-md-4">  
            <input type="text" name="pincode" class="form-control" placeholder="Pincode" value="<?= htmlspecialchars($address['pincode'] ?? '') ?>" required>  
          </div>  
        </div>  
        <button class="btn btn-primary mt-3 w-100">📂 Save Address</button>  
      </form>  
    </div>  
  </div>  

  <!-- Cart Summary -->  
  <div class="section-card bg-white">  
    <div class="section-header">🗼 Your Order</div>  
    <div class="p-4">  
      <div class="table-responsive">  
        <table class="table table-hover text-center align-middle">  
          <thead class="table-dark">  
            <tr>  
              <th>Image</th>  
              <th>Product</th>  
              <th>Price</th>  
              <th>Qty</th>  
              <th>Total</th>  
              <th></th>  
            </tr>  
          </thead>  
          <tbody>  
            <?php foreach ($cart_items as $item): ?>  
              <tr>  
                <td><img src="uploads/<?= htmlspecialchars($item['image']) ?>" class="product-img"></td>  
                <td><?= htmlspecialchars($item['name']) ?></td>  
                <td>₹<?= number_format($item['price'], 2) ?></td>  
                <td><?= $item['quantity'] ?></td>  
                <td>₹<?= number_format($item['total'], 2) ?></td>  
                <td><a href="cart_remove.php?cart_id=<?= $item['id'] ?>" class="btn btn-outline-danger btn-sm">Remove</a></td>  
              </tr>  
            <?php endforeach; ?>  
            <tr class="table-light">  
              <td colspan="4" class="text-end fw-bold">Subtotal:</td>  
              <td colspan="2" class="fw-bold text-success">₹<span id="subtotal_display"><?= number_format($total_price, 2) ?></span></td>  
            </tr>  
            <tr class="table-warning">  
              <td colspan="4" class="text-end fw-bold">Discount:</td>  
              <td colspan="2">₹<span id="discount_display">0.00</span></td>  
            </tr>  
            <tr class="table-info">
              <td colspan="4" class="text-end fw-bold">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="use_wallet" onclick="applyWallet()">
                  <label class="form-check-label fw-bold" for="use_wallet">
                    Use Wallet Balance (₹<?= number_format($wallet_balance, 2) ?>)
                  </label>
                </div>
              </td>
              <td colspan="2">₹<span id="wallet_used">0.00</span></td>
            </tr>
            <tr class="table-success">  
              <td colspan="4" class="text-end fw-bold">Total Payable:</td>  
              <td colspan="2">₹<span id="payable_display"><?= number_format($total_price, 2) ?></span></td>  
            </tr>  
          </tbody>  
        </table>  

        <!-- Coupon Form -->  
        <div class="mt-4">
          <label for="coupon_code" class="form-label fw-bold">🏱 Have a Coupon Code?</label>
          <div class="input-group shadow-sm">
            <span class="input-group-text bg-primary text-white">
              <i class="bi bi-tag-fill"></i>
            </span>
            <input type="text" class="form-control border-primary" id="coupon_code" placeholder="Enter Coupon Code" onkeypress="handleKeyPress(event)">
            <button class="btn btn-primary" type="button" onclick="applyCoupon()" style="background: linear-gradient(45deg, #4caf50, #81c784); border: none;">
              Apply
            </button>
          </div>
          <div id="coupon_message" class="mt-3"></div>
        </div>

        <!-- Payment Section -->  
        <div class="section-card bg-white mt-4">  
          <div class="section-header">💳 Payment Options</div>  
          <div class="p-4 text-center">  
            <!-- UPI Button -->  
            <form action="payment.php" method="GET" class="mb-3">  
              <input type="hidden" name="amount" id="upi_amount" value="<?= $total_price ?>">  
              <input type="hidden" name="applied_coupon" id="upi_coupon" value="">  
              <input type="hidden" name="wallet_used" id="wallet_used_input" value="0">  
              <button class="btn btn-success btn-lg w-100">💸 Pay via UPI</button>  
            </form>  

            <!-- COD Button with Confirm -->  
            <form action="place_order.php" method="POST" onsubmit="return confirmCOD();">  
              <input type="hidden" name="payment_method" value="COD">  
              <input type="hidden" name="total_price" id="cod_amount" value="<?= $total_price ?>">  
              <input type="hidden" name="applied_coupon" id="cod_coupon" value="">  
              <input type="hidden" name="wallet_used" id="wallet_used_input" value="0">  
              <button class="btn btn-warning btn-lg w-100">🚚 Confirm Order (Cash on Delivery)</button>  
            </form>  
          </div>  
        </div>  
      </div>  
    </div>  
  </div>  
</div>  

<script>
function confirmCOD() {
  return confirm("Are you sure you want to place this order using Cash on Delivery?");
}

function applyCoupon() {
  let code = document.getElementById("coupon_code").value.trim();
  if (code === '') {
    showMessage("Please enter a coupon code.", false);
    return;
  }

  fetch(`validate_coupon.php?code=${encodeURIComponent(code)}`)
    .then(response => response.json())
    .then(data => {
      if (data.valid) {
        showMessage(`Coupon applied! You got ₹${data.discount} off.`, true);
        let subtotal = parseFloat(<?= $total_price ?>);
        let discount = parseFloat(data.discount);
        let payable = subtotal - discount;
        if (payable < 0) payable = 0;
        document.getElementById("discount_display").textContent = discount.toFixed(2);
        document.getElementById("payable_display").textContent = payable.toFixed(2);
        document.getElementById("upi_amount").value = payable.toFixed(2);
        document.getElementById("cod_amount").value = payable.toFixed(2);
        document.getElementById("upi_coupon").value = code;
        document.getElementById("cod_coupon").value = code;
        applyWallet();
      } else {
        showMessage("Invalid or expired coupon code.", false);
      }
    }).catch(() => {
      showMessage("Error validating coupon. Please try again.", false);
    });
}

function applyWallet() {
  let walletBalance = <?= $wallet_balance ?>;
  let useWallet = document.getElementById("use_wallet").checked;
  let subtotal = parseFloat(<?= $total_price ?>);
  let discount = parseFloat(document.getElementById("discount_display").textContent) || 0;
  let payable = subtotal - discount;

  let walletUsed = 0;
  if (useWallet) {
    walletUsed = Math.min(walletBalance, payable);
  }
  let newPayable = payable - walletUsed;
  if (newPayable < 0) newPayable = 0;

  document.getElementById("wallet_used").textContent = walletUsed.toFixed(2);
  document.getElementById("payable_display").textContent = newPayable.toFixed(2);
  document.getElementById("upi_amount").value = newPayable.toFixed(2);
  document.getElementById("cod_amount").value = newPayable.toFixed(2);
  document.getElementById("wallet_used_input").value = walletUsed.toFixed(2);
}

function showMessage(msg, success) {
  const el = document.getElementById("coupon_message");
  el.textContent = msg;
  el.className = success ? "text-success fw-bold" : "text-danger fw-bold";
}

document.getElementById("coupon_code").addEventListener("keydown", function(event) {
  if (event.key === "Enter") {
    event.preventDefault();
    applyCoupon();
  }
});
</script>

</body>  
</html>