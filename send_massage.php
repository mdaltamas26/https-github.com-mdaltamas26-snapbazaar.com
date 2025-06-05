<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

$user_id = $_SESSION['user_id'];
$message = trim($_POST['message']);  // Trim message to avoid extra spaces

if (empty($message)) {
    die("Message cannot be empty");
}

$query = "INSERT INTO chat_messages (user_id, message, sender) VALUES (?, ?, 'user')";
$stmt = $conn->prepare($query);

if ($stmt) {
    $stmt->bind_param("is", $user_id, $message);  // "i" for integer, "s" for string
    if ($stmt->execute()) {
        echo "Message sent successfully";
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    echo "Error in prepared statement: " . $conn->error;
}

$stmt->close();
$conn->close();
?>