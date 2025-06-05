<?php
session_start();
include 'db.php'; // Database Connection

if (!isset($_SESSION['user_id'])) {
    die("❌ Please login first.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id']; 
    $subject = mysqli_real_escape_string($conn, $_POST['subject']); 
    $message = mysqli_real_escape_string($conn, $_POST['message']); 

    $query = "INSERT INTO support_tickets (user_id, subject, message, status, created_at, updated_at) 
              VALUES ('$user_id', '$subject', '$message', 'Pending', NOW(), NOW())";

    if (mysqli_query($conn, $query)) {
        echo "✅ Support ticket submitted successfully!";
    } else {
        echo "❌ Error: " . mysqli_error($conn);
    }
}
?>