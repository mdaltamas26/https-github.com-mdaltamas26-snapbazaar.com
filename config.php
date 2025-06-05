<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection settings
$servername = "localhost"; // Default XAMPP server
$username = "root"; // Default MySQL username
$password = ""; // Default XAMPP has no password
$database = "snapbazaar"; // Tumhara database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("❌ Database Connection Failed: " . $conn->connect_error);
}

// Set character encoding to UTF-8
$conn->set_charset("utf8");
?>