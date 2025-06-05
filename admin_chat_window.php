<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

$receiver_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

// Get user name
$user_name = '';
$result = mysqli_query($conn, "SELECT name FROM users WHERE id = '$receiver_id'");
if ($row = mysqli_fetch_assoc($result)) {
    $user_name = $row['name'];
}

// Update admin last active
mysqli_query($conn, "UPDATE users SET last_active = NOW() WHERE id = 0");

// Check user last active
$userCheck = mysqli_query($conn, "SELECT last_active FROM users WHERE id = '$receiver_id'");
$isUserOnline = false;

if ($userRow = mysqli_fetch_assoc($userCheck)) {
    $last_active = strtotime($userRow['last_active']);
    $now = time();
    $inactiveDuration = $now - $last_active;

    $isUserOnline = $inactiveDuration <= 10;

    // If user is inactive for more than 60 seconds (1 minute)
    if ($inactiveDuration > 60) {
        mysqli_query($conn, "DELETE FROM messages WHERE 
            (sender_id = 'admin' AND receiver_id = '$receiver_id') OR 
            (sender_id = '$receiver_id' AND receiver_id = 'admin')");
    }
}

// Send message/file
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $msgSent = false;

    if (!empty($_POST['message'])) {
        $message = mysqli_real_escape_string($conn, $_POST['message']);
        mysqli_query($conn, "INSERT INTO messages (sender_id, receiver_id, message, timestamp, is_read) 
                             VALUES ('admin', '$receiver_id', '$message', NOW(), 0)");
        $msgSent = true;
    }

    if (!empty($_FILES['file']['name'])) {
        $fileName = basename($_FILES['file']['name']);
        $uniqueName = time() . "_" . $fileName;
        $uploadPath = 'livechat/' . $uniqueName;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadPath)) {
            $fileUrl = "livechat/" . $uniqueName;
            $fileMsg = "<a href='$fileUrl' target='_blank'>📎 " . htmlspecialchars($fileName) . "</a>";
            $fileMsgEscaped = mysqli_real_escape_string($conn, $fileMsg);

            mysqli_query($conn, "INSERT INTO messages (sender_id, receiver_id, message, timestamp, is_read)
                                 VALUES ('admin', '$receiver_id', '$fileMsgEscaped', NOW(), 0)");
            $msgSent = true;
        }
    }

    if ($msgSent) {
        header("Location: admin_chat_window.php?user_id=$receiver_id");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Chat Window</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .chat-container { max-width: 800px; margin: 30px auto; }
        .chat-box {
            background: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            height: 500px;
            overflow-y: scroll;
            padding: 20px;
        }
        .msg-left, .msg-right {
            display: flex;
            margin-bottom: 15px;
        }
        .msg-left { justify-content: flex-start; }
        .msg-right { justify-content: flex-end; }
        .msg-bubble {
            padding: 12px 18px;
            border-radius: 20px;
            max-width: 70%;
            font-size: 15px;
            word-break: break-word;
        }
        .admin-msg { background-color: #d1e7dd; }
        .user-msg { background-color: #cfe2ff; }
        .timestamp {
            font-size: 12px;
            color: gray;
            margin-top: 5px;
        }
        .chat-header {
            background: #0d6efd;
            color: white;
            padding: 15px 20px;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .chat-footer {
            background: #f1f3f5;
            padding: 15px 20px;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
        }
        .file-input { max-width: 220px; }
    </style>
</head>
<body>

<div class="container chat-container">
    <div class="card shadow">
        <div class="chat-header">
        <a href="dashboard.php" class="btn btn-secondary float-end">← Back to Dashboard</a>
            <div>
                <strong>Chat with <?= htmlspecialchars($user_name) ?></strong> (User ID: <?= $receiver_id ?>)
            </div>
            <div>
                <?php if ($isUserOnline): ?>
                    <span class="badge bg-success">Online</span>
                <?php else: ?>
                    <span class="badge bg-danger">Offline</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="chat-box" id="chatBox"></div>

        <div class="chat-footer">
            <form method="post" enctype="multipart/form-data" class="row g-2">
                <div class="col-md-6 col-12">
                    <input type="text" name="message" class="form-control" placeholder="Type your message...">
                </div>
                <div class="col-md-4 col-12">
                    <input type="file" name="file" class="form-control file-input">
                </div>
                <div class="col-md-2 col-12">
                    <button type="submit" class="btn btn-primary w-100">Send</button>
                </div>
            </form>
            <a href="admin_chat.php" class="btn btn-sm btn-secondary mt-3">⬅ Back to Chat List</a>
        </div>
    </div>
</div>

<script>
let isVideoPlaying = false;

// Check if any video is playing
function checkVideoPlaying() {
    const videos = document.querySelectorAll("video");
    for (let video of videos) {
        if (!video.paused && !video.ended) {
            return true;
        }
    }
    return false;
}

function loadChat() {
    const chatBox = document.getElementById("chatBox");
    const userId = <?= $receiver_id ?>;

    const atBottom = chatBox.scrollTop + chatBox.clientHeight >= chatBox.scrollHeight - 50;
    isVideoPlaying = checkVideoPlaying();

    if (isVideoPlaying || !atBottom) {
        return; // Skip refresh
    }

    fetch("fetch_admin_chat.php?user_id=" + userId)
        .then(response => response.text())
        .then(data => {
            chatBox.innerHTML = data;
            chatBox.scrollTop = chatBox.scrollHeight;
        });
}

// Load initially
loadChat();

// Refresh every 5 seconds
setInterval(loadChat, 5000);
</script>

</body>
</html>
