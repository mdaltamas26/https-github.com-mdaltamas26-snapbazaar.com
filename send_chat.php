<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'];
$message = mysqli_real_escape_string($conn, $_POST['message']);

mysqli_query($conn, "INSERT INTO chats (user_id, message, sender) VALUES ('$user_id', '$message', 'user')");
?>
