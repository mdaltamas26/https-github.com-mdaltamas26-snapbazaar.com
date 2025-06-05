<?php
session_start();
include('db.php'); // Database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT id, name, password, status FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $name, $hashed_password, $status);
            $stmt->fetch();

            // *Banned User Check*
            if ($status === 'banned') {
                $error = "Your account has been banned! Contact support.";
            } elseif (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['user_name'] = $name;
                header("Location: home.php");
                exit();
            } else {
                $error = "Invalid email or password!";
            }
        } else {
            $error = "User not found!";
        }
        $stmt->close();
    } else {
        $error = "All fields are required!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | SnapBazaar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background-image: url('https://via.placeholder.com/1500x1000'); /* Replace with actual image */
            background-size: cover;
            background-position: center;
            font-family: 'Arial', sans-serif;
        }
        .login-container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 40px;
            width: 400px;
            margin: 50px auto;
            text-align: center;
        }
        h2 { color: #333; }
        .form-control { border-radius: 25px; margin-bottom: 15px; }
        button {
            width: 100%;
            background-color: #007bff;
            border: none;
            border-radius: 25px;
            color: white;
            padding: 10px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover { background-color: #0056b3; }
        .error { color: red; }
        a { text-decoration: none; color: #007bff; }
        a:hover { text-decoration: underline; }
        .footer-text { text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login to SnapBazaar</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form action="login.php" method="POST">
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Enter your Email" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Enter your Password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <p><a href="forgot_password.php">Forgot Password?</a></p>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>

    <div class="footer-text">
        <p>&copy; 2025 SnapBazaar. All rights reserved.</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>