<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
mysqli_query($conn, "UPDATE users SET last_active = NOW() WHERE id = '$user_id'");

// Check admin last active (for badge only)
$adminCheck = mysqli_query($conn, "SELECT last_active FROM users WHERE id = 0");
$isAdminOnline = false;
if ($adminRow = mysqli_fetch_assoc($adminCheck)) {
    $adminLast = strtotime($adminRow['last_active']);
    $isAdminOnline = (time() - $adminLast) <= 10;
}

// Check if user inactive for more than 1 minute
$userCheck = mysqli_query($conn, "SELECT last_active FROM users WHERE id = '$user_id'");
if ($userRow = mysqli_fetch_assoc($userCheck)) {
    $userLast = strtotime($userRow['last_active']);
    if ((time() - $userLast) > 60) {
        $messages = mysqli_query($conn, "SELECT * FROM messages 
            WHERE (sender_id = 'admin' AND receiver_id = '$user_id') 
               OR (sender_id = '$user_id' AND receiver_id = 'admin')");

        while ($row = mysqli_fetch_assoc($messages)) {
            $sender = $row['sender_id'];
            $receiver = $row['receiver_id'];
            $message = $row['message'];
            $timestamp = $row['timestamp'];
            $deletedAt = date("Y-m-d H:i:s");

            mysqli_query($conn, "INSERT INTO deleted_messages 
                (sender_id, receiver_id, message, timestamp, deleted_at)
                VALUES ('$sender', '$receiver', '" . mysqli_real_escape_string($conn, $message) . "', '$timestamp', '$deletedAt')");
        }

        mysqli_query($conn, "DELETE FROM messages 
            WHERE (sender_id = 'admin' AND receiver_id = '$user_id') 
               OR (sender_id = '$user_id' AND receiver_id = 'admin')");
    }
}

// Message Handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $msgSent = false;

    if (!empty($_POST['message'])) {
        $message = mysqli_real_escape_string($conn, $_POST['message']);
        mysqli_query($conn, "INSERT INTO messages (sender_id, receiver_id, message, timestamp, is_read) 
                             VALUES ('$user_id', 'admin', '$message', NOW(), 0)");
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
                                 VALUES ('$user_id', 'admin', '$fileMsgEscaped', NOW(), 0)");
            $msgSent = true;
        }
    }

    if ($msgSent) {
        header("Location: live_chat.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Live Chat</title>
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
            background: #198754;
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
            <a href="home.php" class="btn btn-sm btn-light">⬅ Back</a>
            <div><strong>Chat with Admin</strong></div>
            <div>
                <?php if ($isAdminOnline): ?>
                    <span class="badge bg-success">Admin Online</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="chat-box" id="chatBox">
            <!-- Messages will be loaded here via fetch_chat.php -->
        </div>

        <div class="chat-footer">
            <form method="post" enctype="multipart/form-data" class="row g-2">
                <div class="col-md-6 col-12">
                    <input type="text" name="message" class="form-control" placeholder="Type your message...">
                </div>
                <div class="col-md-4 col-12">
                    <input type="file" name="file" class="form-control file-input">
                </div>
                <div class="col-md-2 col-12">
                    <button type="submit" class="btn btn-success w-100">Send</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let chatBox = document.getElementById("chatBox");
let isTyping = false;
let shouldAutoScroll = true;

function isUserAtBottom() {
    return Math.abs(chatBox.scrollHeight - chatBox.scrollTop - chatBox.clientHeight) < 5;
}

chatBox.addEventListener('scroll', () => {
    shouldAutoScroll = isUserAtBottom();
});

function fetchMessages() {
    if (isTyping) return;

    let xhr = new XMLHttpRequest();
    xhr.open("GET", "fetch_chat.php", true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            const prevScrollTop = chatBox.scrollTop;
            const prevScrollHeight = chatBox.scrollHeight;

            let tempDiv = document.createElement('div');
            tempDiv.innerHTML = xhr.responseText.trim();

            if (chatBox.innerHTML.trim() !== tempDiv.innerHTML.trim()) {
                chatBox.innerHTML = tempDiv.innerHTML;

                if (shouldAutoScroll) {
                    chatBox.scrollTop = chatBox.scrollHeight;
                } else {
                    const newScrollHeight = chatBox.scrollHeight;
                    const scrollDiff = newScrollHeight - prevScrollHeight;
                    chatBox.scrollTop = prevScrollTop + scrollDiff;
                }
            }
        }
    };
    xhr.send();
}

fetchMessages();
setInterval(fetchMessages, 3000);

const messageInput = document.querySelector('input[name="message"]');
messageInput.addEventListener("focus", () => isTyping = true);
messageInput.addEventListener("blur", () => isTyping = false);
</script>

</body>
</html>
