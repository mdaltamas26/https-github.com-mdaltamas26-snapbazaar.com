<?php
session_start();
include 'db.php';

$message = "";
$step = 1;

if (isset($_POST['verify_username'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $key = $_POST['key'];

    $check = mysqli_query($conn, "SELECT * FROM admin WHERE username='$username'");
    if (mysqli_num_rows($check) > 0) {
        if ($key === 'Angry9546') {
            $_SESSION['reset_admin_username'] = $username;
            $step = 2;
        } else {
            $message = "<div class='alert alert-danger'>❌ Invalid key.</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>❌ Username not found.</div>";
    }
}

// Step 2: Update Password
if (isset($_POST['reset_password'])) {
    $username = isset($_SESSION['reset_admin_username']) ? $_SESSION['reset_admin_username'] : null;

    if ($username) {
        $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

        $update = mysqli_query($conn, "UPDATE admin SET password='$new_password' WHERE username='$username'");
        if ($update) {
            $message = "<div class='alert alert-success'>✅ Password updated successfully! You can now login.</div>";
            session_destroy();
            $step = 1;
        } else {
            $message = "<div class='alert alert-danger'>❌ Failed to update password. Try again.</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>❌ Session expired. Please verify again.</div>";
        $step = 1;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Forgot Password - SnapBazaar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f1f1f1;
        }
        .container {
            max-width: 450px;
            margin-top: 80px;
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 {
            font-weight: bold;
        }
        .btn-back {
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center text-primary">🔐 Admin Forgot Password</h2>
    <?= $message ?>

    <?php if ($step === 1): ?>
        <form method="POST">
            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" required class="form-control" placeholder="Enter admin username">
            </div>
            <div class="mb-3">
                <label>Unique Key</label>
                <input type="text" name="key" required class="form-control" placeholder="Enter secret key">
            </div>
            <button type="submit" name="verify_username" class="btn btn-primary w-100">Verify</button>
            <a href="admin_login.php" class="btn btn-secondary w-100 btn-back mt-2">🔙 Back to Login</a>
        </form>
    <?php elseif ($step === 2): ?>
        <form method="POST">
            <div class="mb-3">
                <label>New Password</label>
                <input type="password" name="new_password" required class="form-control" placeholder="Enter new password">
            </div>
            <button type="submit" name="reset_password" class="btn btn-success w-100">Reset Password</button>
            <a href="admin_login.php" class="btn btn-secondary w-100 btn-back mt-2">🔙 Back to Login</a>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
