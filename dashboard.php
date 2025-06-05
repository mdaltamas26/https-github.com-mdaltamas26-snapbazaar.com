<?php
session_start();
include 'db.php'; // Database Connection

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

// Fetch dashboard stats
$total_orders = $conn->query("SELECT COUNT(*) FROM my_orders")->fetch_row()[0];
$total_users = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
$total_sales = $conn->query("SELECT SUM(total_price) FROM my_orders WHERE order_status='delivered'")->fetch_row()[0] ?? 0;
$total_refunds = $conn->query("SELECT COUNT(*) FROM refund_requests WHERE status='approved'")->fetch_row()[0]; // ✅ Fixed this line
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard - SnapBazaar</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"/>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar text-white p-3 min-vh-100">
      <h4 class="text-center">Admin Panel</h4>
      <ul class="nav flex-column">
        <li class="nav-item"><a href="#" class="nav-link text-white">Dashboard</a></li>
        <li class="nav-item"><a href="manage_orders.php" class="nav-link text-white">Manage Orders</a></li>
        <li class="nav-item"><a href="manage_users.php" class="nav-link text-white">Manage Users</a></li>
        <li class="nav-item"><a href="admin_products.php" class="nav-link text-white">Manage Products</a></li>
        <li class="nav-item"><a href="manage_coupons.php" class="nav-link text-white">Manage Coupons</a></li>
        <li class="nav-item"><a href="manage_refunds.php" class="nav-link text-white">Manage Refunds</a></li>
        <li class="nav-item"><a href="manage_tickets.php" class="nav-link text-white">Support Tickets</a></li>
        <li class="nav-item"><a href="manage_payments.php" class="nav-link text-white">Manage Payments</a></li>
        <li class="nav-item"><a href="reports.php" class="nav-link text-white">Reports & Analytics</a></li>
        <li class="nav-item"><a href="manage_referrals.php" class="nav-link text-white">Manage Referral</a></li>
        <li class="nav-item"><a href="admin_chat.php" class="nav-link text-white">Live Chat</a></li>
        <!-- ✅ Updated logout link with confirmation -->
        <li class="nav-item">
          <a href="admin_logout.php" class="nav-link text-danger" onclick="return confirm('Kya aap sach me logout karna chahte hain?')">Logout</a>
        </li>
      </ul>
    </nav>

    <!-- Main Content -->
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h2>Admin Dashboard</h2>
      </div>

      <!-- Dashboard Stats -->
      <div class="row">
        <div class="col-md-3">
          <div class="card text-white bg-primary p-3">
            <h5>Total Orders</h5>
            <p><?= $total_orders; ?></p>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card text-white bg-success p-3">
            <h5>Total Users</h5>
            <p><?= $total_users; ?></p>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card text-white bg-warning p-3">
            <h5>Total Sales</h5>
            <p>₹<?= number_format($total_sales, 2); ?></p>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card text-white bg-danger p-3">
            <h5>Approved Refunds</h5>
            <p><?= $total_refunds; ?></p>
          </div>
        </div>
      </div>

      <h4 class="mt-4">Quick Actions</h4>
      <div class="row">
        <div class="col-md-4">
          <a href="product_add.php" class="btn btn-outline-primary w-100">Add New Product</a>
        </div>
        <div class="col-md-4">
          <a href="create_coupon.php" class="btn btn-outline-warning w-100">Create Coupon</a>
        </div>
        <div class="col-md-4"></div>
      </div>
    </main>
  </div>
</div>
</body>
</html>
