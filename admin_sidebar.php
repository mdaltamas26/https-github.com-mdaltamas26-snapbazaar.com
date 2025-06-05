<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
?>

<style>
    .sidebar {
        width: 250px;
        height: 100vh;
        background: #343a40;
        position: fixed;
        top: 0;
        left: 0;
        padding: 20px;
        color: white;
    }
    .sidebar h3 {
        text-align: center;
        margin-bottom: 20px;
    }
    .sidebar a {
        color: white;
        text-decoration: none;
        display: block;
        padding: 10px;
        margin: 5px 0;
        border-radius: 5px;
    }
    .sidebar a:hover {
        background: #495057;
    }
    .logout-btn {
        position: absolute;
        bottom: 20px;
        left: 20px;
        width: 200px;
        background: red;
        text-align: center;
    }
</style>

<div class="sidebar">
    <h3>⚙ Admin Panel</h3>
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="manage_products.php">🛒 Manage Products</a>
    <a href="manage_orders.php">📜 Manage Orders</a>
    <a href="manage_users.php">👥 Manage Users</a>
    <a href="manage_coupons.php">🎟 Manage Coupons</a>
    <a href="settings.php">⚙ Settings</a>
    <a href="logout.php" class="btn btn-danger logout-btn">🚪 Logout</a>
</div>