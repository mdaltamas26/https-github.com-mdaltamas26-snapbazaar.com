<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    echo "Admin login required.";
    exit();
}

// 10 minutes ago
$timeLimit = date("Y-m-d H:i:s", strtotime("-10 minutes"));

// Get active users who messaged admin
$query = "
    SELECT u.id, u.email 
    FROM users u 
    INNER JOIN (
        SELECT DISTINCT sender_id FROM messages WHERE receiver_id = 'admin'
    ) m ON u.id = m.sender_id
    WHERE u.last_active >= '$timeLimit'
";

$users = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Live Chat - SnapBazaar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .user-list { border-right: 1px solid #ccc; height: 100vh; overflow-y: auto; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- User List -->
        <div class="col-md-3 user-list bg-white p-3">
            <h5>Active Users (Last 10 Min)</h5>
            <ul class="list-group">
                <?php while($u = mysqli_fetch_assoc($users)): ?>
                    <li class="list-group-item">
                        <a href="admin_chat_window.php?user_id=<?= $u['id'] ?>">
                            <?= htmlspecialchars($u['id']) ?> - <?= htmlspecialchars($u['email']) ?>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
            <hr>
        </div>

        <div class="col-md-9 d-flex align-items-center justify-content-center text-muted">
            Select a user to start chat.
        </div>
    </div>
</div>
</body>
</html>
