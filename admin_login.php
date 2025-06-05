<?php
session_start();

// Ensure db.php is included correctly
$path = dirname(__FILE__) . '/db.php'; // Corrected path
if (file_exists($path)) {
    include($path);
} else {
    die("Error: db.php file not found in " . dirname(__FILE__));
}

// Redirect if already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        // Secure Query with Prepared Statement
        $stmt = $conn->prepare("SELECT id, password FROM admin WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($admin_id, $hashed_password);
            $stmt->fetch();

            // Verify Password
            if (password_verify($password, $hashed_password)) {
                $_SESSION['admin_id'] = $admin_id;
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "❌ Incorrect Password!";
            }
        } else {
            $error = "❌ Admin not found!";
        }
        $stmt->close();
    } else {
        $error = "⚠ Please fill in all fields!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login | SnapBazaar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .login-header {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
        }
        .btn-login {
            width: 100%;
            background: #007bff;
            color: white;
            font-size: 16px;
            padding: 10px;
            border-radius: 5px;
            transition: 0.3s;
        }
        .btn-login:hover {
            background: #0056b3;
        }
        .register-link {
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2 class="login-header">🔑 Admin Login</h2>
    
    <?php if (!empty($error)) { ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php } ?>

    <form method="POST">
        <div class="mb-3">
            <input type="text" name="username" placeholder="Enter Username" required class="form-control">
        </div>
        <div class="mb-3">
            <input type="password" name="password" placeholder="Enter Password" required class="form-control">
        </div>
        <button type="submit" class="btn btn-login">Login</button>
    </form>

    <div class="register-link">
        <p>Don't have an account? <a href="admin_register.php">Register as Admin</a></p>
        <a href="admin_forgot_password.php" class="d-block mt-3 text-center">Forgot Password?</a>
    </div>
</div>

</body>
</html>