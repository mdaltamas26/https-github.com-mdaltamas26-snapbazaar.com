<?php
session_start();
include 'db.php'; // Database connection

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch users
$query = "SELECT * FROM users ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - SnapBazaar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Manage Users</h2>

    <!-- Success/Error Message -->
    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_GET['msg']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <table class="table table-bordered table-hover">
        <thead class="table-dark">
        <a href="dashboard.php" class="btn btn-secondary btn-back">&larr; Back to Dashboard</a>
            <tr>
                <th>#ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($user = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['mobile']) ?></td>
                <td><?= date('d M Y, h:i A', strtotime($user['created_at'])) ?></td>
                <td>
                    <a href="delete_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>

                    <?php if ($user['status'] === 'active'): ?>
                        <a href="toggle_user_status.php?id=<?= $user['id'] ?>&action=ban" class="btn btn-sm btn-warning ms-1" onclick="return confirm('Are you sure you want to ban this user?');">Ban</a>
                    <?php else: ?>
                        <a href="toggle_user_status.php?id=<?= $user['id'] ?>&action=unban" class="btn btn-sm btn-success ms-1" onclick="return confirm('Unban this user?');">Unban</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
