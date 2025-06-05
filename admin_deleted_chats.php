<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    echo "Admin login required.";
    exit();
}

$now = time();
$validChats = [];

$chatsQuery = mysqli_query($conn, "SELECT * FROM deleted_messages ORDER BY deleted_at DESC");

while ($row = mysqli_fetch_assoc($chatsQuery)) {
    $senderId = $row['original_sender_id'];
    $receiverId = $row['original_receiver_id'];

    // Get sender name
    $senderName = ($senderId === 'admin') ? 'Admin' : '';
    if ($senderId !== 'admin') {
        $senderRes = mysqli_query($conn, "SELECT name, last_active FROM users WHERE id = '$senderId'");
        if ($senderData = mysqli_fetch_assoc($senderRes)) {
            $senderName = $senderData['name'];
            $inactiveDuration = $now - strtotime($senderData['last_active']);
        } else {
            continue; // Skip if sender not found
        }
    }

    // Get receiver name
    $receiverName = ($receiverId === 'admin') ? 'Admin' : '';
    if ($receiverId !== 'admin') {
        $receiverRes = mysqli_query($conn, "SELECT name, last_active FROM users WHERE id = '$receiverId'");
        if ($receiverData = mysqli_fetch_assoc($receiverRes)) {
            $receiverName = $receiverData['name'];
            $inactiveDuration = $now - strtotime($receiverData['last_active']);
        } else {
            continue; // Skip if receiver not found
        }
    }

    // Check inactivity
    if ($inactiveDuration > 60) {
        $row['sender_name'] = $senderName;
        $row['receiver_name'] = $receiverName;
        $validChats[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Deleted Conversations - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h3>🗑️ Deleted Chat History (Inactive Users Only)</h3>
    <?php if (count($validChats) > 0): ?>
        <table class="table table-bordered mt-3">
            <thead class="table-light">
                <tr>
                    <th>Sender</th>
                    <th>Receiver</th>
                    <th>Message</th>
                    <th>Original Time</th>
                    <th>Deleted At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($validChats as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['sender_name']) ?> (ID: <?= $row['original_sender_id'] ?>)</td>
                        <td><?= htmlspecialchars($row['receiver_name']) ?> (ID: <?= $row['original_receiver_id'] ?>)</td>
                        <td><?= htmlspecialchars($row['message']) ?></td>
                        <td><?= $row['original_timestamp'] ?></td>
                        <td><?= $row['deleted_at'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info mt-4">No deleted chats to show. All users are currently active or no chats deleted yet.</div>
    <?php endif; ?>
</div>
</body>
</html>
