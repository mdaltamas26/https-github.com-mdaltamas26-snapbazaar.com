<?php
session_start();

// Include Database Connection
require 'db.php';

// Check if Admin is Logged In
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle User Ban/Unban
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['action'])) {
    $user_id = intval($_POST['user_id']);
    $status = ($_POST['action'] === 'ban') ? 'Banned' : 'Active';

    $stmt = $conn->prepare("UPDATE users SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $user_id);
    $stmt->execute();
    header("Location: users.php");
    exit();
}

// Search Functionality
$search = $_GET['search'] ?? '';
$searchQuery = $search ? "WHERE name LIKE ? OR email LIKE ?" : "";

// Pagination Settings
$limit = 10;
$page = $_GET['page'] ?? 1;
$offset = ($page - 1) * $limit;

// Get Total Users
$countStmt = $conn->prepare("SELECT COUNT(*) FROM users $searchQuery");
if ($search) {
    $likeSearch = "%$search%";
    $countStmt->bind_param("ss", $likeSearch, $likeSearch);
}
$countStmt->execute();
$totalUsers = $countStmt->get_result()->fetch_row()[0];
$totalPages = ceil($totalUsers / $limit);

// Fetch Users
$query = "SELECT id, name, email, status FROM users $searchQuery ORDER BY id DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);
if ($search) {
    $stmt->bind_param("ssii", $likeSearch, $likeSearch, $limit, $offset);
} else {
    $stmt->bind_param("ii", $limit, $offset);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - SnapBazaar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">👥 Manage Users</h2>

    <!-- Search Form -->
    <form method="GET" class="mb-3">
        <input type="text" name="search" class="form-control" placeholder="Search by name or email" value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-primary mt-2">Search</button>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td><?= htmlspecialchars($user['name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['status']) ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                            <?php if ($user['status'] === 'Banned') { ?>
                                <button name="action" value="unban" class="btn btn-success btn-sm">Unban</button>
                            <?php } else { ?>
                                <button name="action" value="ban" class="btn btn-danger btn-sm">Ban</button>
                            <?php } ?>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <nav>
        <ul class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&search=<?= htmlspecialchars($search) ?>"><?= $i ?></a>
                </li>
            <?php } ?>
        </ul>
    </nav>

    <div class="text-center mt-4">
        <a href="dashboard.php" class="btn btn-dark">Back to Dashboard</a>
    </div>
</div>
</body>
</html>