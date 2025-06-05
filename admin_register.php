<?php
session_start();
include 'db.php'; // Database Connection

// Define the required Unique Key
$required_unique_key = "Angry9546";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $unique_key = trim($_POST['unique_key']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($unique_key) && !empty($username) && !empty($password)) {
        // Check if Unique Key is correct
        if ($unique_key !== $required_unique_key) {
            $_SESSION['error'] = "❌ Invalid Unique Key!";
            header("Location: admin_register.php");
            exit();
        } 

        // Check if Username Already Exists
        $stmt_check = $conn->prepare("SELECT id FROM admin WHERE username = ?");
        $stmt_check->bind_param("s", $username);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $_SESSION['error'] = "⚠ Username already taken!";
            header("Location: admin_register.php");
            exit();
        }
        $stmt_check->close();

        // Hash Password (bcrypt)
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insert Admin into Database
        $stmt = $conn->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashed_password);

        if ($stmt->execute()) {
            $_SESSION['success'] = "✅ Admin registered successfully!";
            header("Location: admin_login.php");
            exit();
        } else {
            $_SESSION['error'] = "❌ Registration failed!";
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "⚠ Please fill all fields!";
    }
    header("Location: admin_register.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Registration | SnapBazaar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background: #f8f9fa; }
        .register-container { max-width: 400px; margin: 100px auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        .register-header { font-size: 24px; font-weight: bold; text-align: center; margin-bottom: 15px; }
        .btn-register { width: 100%; background: #28a745; color: white; font-size: 16px; padding: 10px; border-radius: 5px; transition: 0.3s; }
        .btn-register:hover { background: #218838; }
    </style>
</head>
<body>

<div class="register-container">
    <h2 class="register-header">🆕 Admin Registration</h2>

    <?php if (isset($_SESSION['error'])) { ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php } ?>
    <?php if (isset($_SESSION['success'])) { ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php } ?>

    <form method="POST">
        <div class="mb-3">
            <input type="text" name="unique_key" placeholder="Enter Unique Key" required class="form-control">
        </div>
        <div class="mb-3">
            <input type="text" name="username" placeholder="Enter Username" required class="form-control">
        </div>
        <div class="mb-3">
            <input type="password" name="password" placeholder="Enter Password" required class="form-control">
        </div>
        <button type="submit" class="btn btn-register">Register</button>
        <p>Already have an account? <a href="admin_login.php">Login as Admin</a></p>
    </form>
</div>

</body>
</html>