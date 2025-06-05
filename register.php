<?php
session_start();
include('db_connect.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

$referral_code_url = isset($_GET['ref']) ? trim($_GET['ref']) : '';
$referral_code_post = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $referral_code_post = isset($_POST['referral_code']) ? trim($_POST['referral_code']) : '';

    $referred_by = null;

    if (empty($name) || empty($email) || empty($mobile) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } elseif (!preg_match('/^[0-9]{10}$/', $mobile)) {
        $error = "Invalid mobile number! Must be 10 digits.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Referral code check
        if (!empty($referral_code_post)) {
            $stmt_ref = $conn->prepare("SELECT id FROM users WHERE referral_code = ?");
            $stmt_ref->bind_param("s", $referral_code_post);
            $stmt_ref->execute();
            $stmt_ref->bind_result($ref_id);
            if ($stmt_ref->fetch()) {
                $referred_by = $ref_id;
            } else {
                $error = "Invalid referral code!";
            }
            $stmt_ref->close();
        }

        if (!isset($error)) {
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR mobile = ?");
            $stmt->bind_param("ss", $email, $mobile);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 0) {
                $stmt->close();

                $my_ref_code = substr(md5(time() . $mobile), 0, 8);

                // Insert new user
                $stmt = $conn->prepare("INSERT INTO users (name, email, mobile, password, referral_code, referred_by) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssi", $name, $email, $mobile, $hashed_password, $my_ref_code, $referred_by);

                if ($stmt->execute()) {
                    $new_user_id = $stmt->insert_id;
                    $_SESSION['user_id'] = $new_user_id;
                    $_SESSION['user_name'] = $name;
                    $stmt->close();

                    // Reward to referrer
                    if ($referred_by) {
                        $reward = 20;
                        $update_ref = $conn->prepare("UPDATE users SET referral_earned = referral_earned + ?, wallet_balance = wallet_balance + ? WHERE id = ?");
                        $update_ref->bind_param("iii", $reward, $reward, $referred_by);
                        $update_ref->execute();
                        $update_ref->close();
                    }

                    // Send welcome email with formatted ID
                    $formatted_id = str_pad($new_user_id, 6, '0', STR_PAD_LEFT);
                    $mail = new PHPMailer(true);
                    try {
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'angryffgaming8@gmail.com';
                        $mail->Password = 'mnys tasw qbsx kbrw';
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;
                        $mail->SMTPOptions = [
                            'ssl' => [
                                'verify_peer' => false,
                                'verify_peer_name' => false,
                                'allow_self_signed' => true,
                            ],
                        ];
                        $mail->setFrom('angryffgaming8@gmail.com', 'SnapBazaar');
                        $mail->addAddress($email);
                        $mail->isHTML(true);
                        $mail->Subject = "Welcome to SnapBazaar!";
                        $mail->Body = "
Hi <b>$name</b>,<br><br>
🎉 <b>Welcome to SnapBazaar!</b><br><br>
We're thrilled to have you with us. SnapBazaar is your new destination for unbeatable deals, latest trends, and a seamless shopping experience.<br><br>
👉 <a href='https://www.youtube.com/@Mdabdulfree10m' target='_blank'>Click here to start shopping now</a><br><br>
If you ever need help, our support team is just a click away.<br><br>
<b>Team SnapBazaar</b><br><small>shop smart. shop SnapBazaar.</small>";
                        $mail->send();
                    } catch (Exception $e) {
                        // Log email error (optional)
                    }

                    header("Location: home.php");
                    exit();
                } else {
                    $error = "Something went wrong. Please try again!";
                }
            } else {
                $error = "Email or Mobile number already exists!";
                $stmt->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | SnapBazaar</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('https://via.placeholder.com/1500x1000');
            background-size: cover;
            background-position: center;
            font-family: 'Arial', sans-serif;
        }
        .register-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 40px;
            width: 450px;
            margin: 50px auto;
            text-align: center;
        }
        h2 { color: #333; margin-bottom: 20px; }
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
        .error { color: red; margin-bottom: 10px; }
        a { text-decoration: none; color: #007bff; }
        a:hover { text-decoration: underline; }
        .footer-text { text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Create Your SnapBazaar Account</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form action="register.php<?php if ($referral_code_url) echo '?ref=' . htmlspecialchars($referral_code_url); ?>" method="POST">
            <input type="text" name="name" class="form-control" placeholder="Enter your Name" required>
            <input type="email" name="email" class="form-control" placeholder="Enter your Email" required>
            <input type="number" name="mobile" class="form-control" placeholder="Enter your Mobile No" required>
            <input type="password" name="password" class="form-control" placeholder="Enter your Password" required>
            <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
            <input type="text" name="referral_code" class="form-control" placeholder="Referral Code (Optional)" value="<?php echo htmlspecialchars($referral_code_url ?: $referral_code_post); ?>" <?php if ($referral_code_url) echo 'readonly'; ?>>
            <button type="submit">Register</button>
        </form>
        <p class="mt-3">Already have an account? <a href="login.php">Login here</a></p>
    </div>
    <div class="footer-text">
        <p>&copy; 2025 SnapBazaar. All rights reserved.</p>
    </div>
</body>
</html>