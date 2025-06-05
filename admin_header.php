<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel | SnapBazaar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background: #f8f9fa; }
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
        .content {
            margin-left: 260px;
            padding: 20px;
        }
        .logout-btn {
            position: absolute;
            bottom: 20px;
            left: 20px;
            width: 200px;
            background: red;
        }
    </style>
</head>
<body>

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

<div class="content">