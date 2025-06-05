<?php
// Database connection
include 'db.php'; // Is file mein apna DB connection include karen

// Check if the current user is an admin to prevent unauthorized access
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: home.php"); // Agar current user admin nahi hai, to home page pe redirect
    exit();
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get data from the form
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = 'admin';  // Set the role as admin

    // Password hash karna for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // SQL Query to insert admin into users table
    $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

    if ($stmt->execute()) {
        echo "✅ Admin account created successfully!";
    } else {
        echo "❌ Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin</title>
</head>
<body>
    <h2>Create New Admin</h2>
    <form action="admin_creation.php" method="POST">
        Name: <input type="text" name="name" required><br>
        Email: <input type="email" name="email" required><br>
        Password: <input type="password" name="password" required><br>
        <button type="submit">Create Admin</button>
    </form>
</body>
</html>
