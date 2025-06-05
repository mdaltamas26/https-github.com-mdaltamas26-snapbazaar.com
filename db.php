<?php
$servername = "localhost";
$username = "root";  // XAMPP ke liye mostly "root" hota hai
$password = "";      // Default XAMPP ke liye password blank hota hai
$dbname = "snapbazaar"; // Tumhara database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>