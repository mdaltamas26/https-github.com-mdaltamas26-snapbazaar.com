<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($user_query);

// Check if admin
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Account - SnapBazaar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .nav-tabs .nav-link.active {
            background-color: #0d6efd;
            color: white;
        }
        .tab-content {
            background: white;
            padding: 20px;
            border: 1px solid #dee2e6;
            border-top: none;
        }
        .profile-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <h2 class="text-center mb-4">Welcome, <?= htmlspecialchars($user['name']) ?></h2>
    
    <ul class="nav nav-tabs mb-3" id="accountTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button">Profile</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button">My Orders</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="wishlist-tab" data-bs-toggle="tab" data-bs-target="#wishlist" type="button">My Wishlist</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="support-tab" data-bs-toggle="tab" data-bs-target="#support" type="button">Support Tickets</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="chat-tab" data-bs-toggle="tab" data-bs-target="#chat" type="button">Live Chat</button>
        </li>
        <?php if ($is_admin): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="admin-tab" data-bs-toggle="tab" data-bs-target="#admin" type="button">Admin Panel</button>
        </li>
        <?php endif; ?>
    </ul>

    <div class="tab-content" id="accountTabsContent">
        <!-- Profile Section -->
        <div class="tab-pane fade show active" id="profile" role="tabpanel">
            <div class="row">
                <div class="col-md-4 text-center">
                    <img src="uploads/<?= $user['profile_pic'] ?? 'default.png' ?>" alt="Profile Picture" class="profile-img mb-3">
                </div>
                <div class="col-md-8">
                    <h4>Profile Info</h4>
                    <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                    <p><strong>Mobile:</strong> <?= htmlspecialchars($user['mobile']) ?></p>
                    <a href="edit_profile.php" class="btn btn-primary btn-sm">Edit Profile</a>
                </div>
            </div>
        </div>

        <!-- Orders -->
        <div class="tab-pane fade" id="orders" role="tabpanel">
            <?php include 'my_orders.php'; ?>
        </div>

        <!-- Wishlist -->
        <div class="tab-pane fade" id="wishlist" role="tabpanel">
            <?php include 'my_wishlist.php'; ?>
        </div>

        <!-- Support Tickets -->
        <div class="tab-pane fade" id="support" role="tabpanel">
            <?php include 'my_tickets.php'; ?>
        </div>

        <!-- Live Chat -->
        <div class="tab-pane fade" id="chat" role="tabpanel">
            <p>Start a conversation with our support team.</p>
            <a href="live_chat.php" class="btn btn-success">Open Chat</a>
        </div>

        <!-- Admin Panel -->
        <?php if ($is_admin): ?>
        <div class="tab-pane fade" id="admin" role="tabpanel">
            <h5>Admin Options</h5>
            <a href="add_product.php" class="btn btn-dark mb-2">➕ Add Product</a>
            <a href="create_coupon.php" class="btn btn-secondary mb-2">🏷️ Create Coupon</a>
        </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
