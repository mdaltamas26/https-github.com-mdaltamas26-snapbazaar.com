<?php
include 'db.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    exit("Admin login required.");
}

$sender = $_SESSION['admin_id'];  // Logged-in admin's ID
$receiver = $_POST['user_id'] ?? '';
$message = trim($_POST['message'] ?? '');
$filename = '';

// Debug: Check if user_id is set
if (!$receiver) {
    echo "No receiver found. POST data: <br>";
    echo "<pre>";
    print_r($_POST);  // Print POST data to debug
    echo "</pre>";
    exit();
}

// File Upload - Validate file upload
if (!empty($_FILES['file']['name'])) {
    $filename = time() . "_" . basename($_FILES['file']['name']);
    $target = "uploads/" . $filename;

    // Check if the file upload is successful
    if (!move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
        exit("File upload failed.");
    }
}

// Insert message into the database
if ($message !== '' || $filename !== '') {
    // Use prepared statements to prevent SQL injection
    $query = "INSERT INTO messages (sender_id, receiver_id, message, file, timestamp, is_read) 
              VALUES (?, ?, ?, ?, NOW(), 0)";

    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, "iiss", $sender, $receiver, $message, $filename);

        if (!mysqli_stmt_execute($stmt)) {
            echo "DB Error: " . mysqli_error($conn);
        } else {
            echo "Message sent successfully!";
        }
        
        mysqli_stmt_close($stmt);
    } else {
        echo "Prepared statement error: " . mysqli_error($conn);
    }
} else {
    echo "Please enter a message or attach a file.";
}
?>