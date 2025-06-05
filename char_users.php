<?php
session_start();
include '../db_connect.php'; // adjust path if needed

// Fetch unique users who have chatted
$query = "SELECT u.id, u.name, m.message, m.timestamp, 
         (SELECT COUNT(*) FROM messages WHERE sender_id = u.id AND receiver_id = 'admin' AND is_read = 0) AS unread_count
          FROM users u
          LEFT JOIN (
              SELECT * FROM messages WHERE id IN (
                  SELECT MAX(id) FROM messages GROUP BY sender_id, receiver_id
              )
          ) m ON m.sender_id = u.id OR m.receiver_id = u.id
          GROUP BY u.id
          ORDER BY m.timestamp DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Chat Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .chat-card:hover {
            background-color: #f8f9fa;
            cursor: pointer;
        }
    </style>
</head>
<body class="bg-light">
<div class="container py-4">
    <h3 class="mb-4">Live Chat - Users</h3>
    <div class="list-group">
        <?php while($row = mysqli_fetch_assoc($result)): ?>
            <a href="admin_chat.php?user_id=<?= $row['id'] ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start chat-card">
                <div class="ms-2 me-auto">
                    <div class="fw-bold">#<?= $row['id'] ?> - <?= htmlspecialchars($row['name']) ?></div>
                    <small class="text-muted"><?= htmlspecialchars(substr($row['message'], 0, 50)) ?>...</small>
                </div>
                <div class="text-end">
                    <?php if ($row['unread_count'] > 0): ?>
                        <span class="badge bg-danger rounded-pill"><?= $row['unread_count'] ?></span><br>
                    <?php endif; ?>
                    <small class="text-muted"><?= date('h:i A', strtotime($row['timestamp'])) ?></small>
                </div>
            </a>
        <?php endwhile; ?>
    </div>
</div>
</body>
</html>