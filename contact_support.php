<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/src/PHPMailer.php';  
require 'PHPMailer/src/SMTP.php';  
require 'PHPMailer/src/Exception.php';

$message = ""; // Message to display

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $issue_type = trim($_POST['issue_type'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<div class='alert alert-danger text-center'>❌ Invalid email format!</div>";
    } elseif (empty($issue_type) || empty($description)) {
        $message = "<div class='alert alert-warning text-center'>⚠ Please fill in all fields!</div>";
    } else {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'angryffgaming888@gmail.com'; // Your email
            $mail->Password = 'uzbf ixml ibel tgpx'; // Use App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom($email, 'User - SnapBazaar Support');
            $mail->addAddress('angryffgaming888@gmail.com'); // Admin email
            $mail->isHTML(true);
            $mail->Subject = "Support Request - $issue_type";
            $mail->Body = "<strong>Email:</strong> $email <br> <strong>Issue:</strong> $issue_type <br> <strong>Description:</strong> <p>$description</p>";
            
            $mail->send();
            $message = "<div class='alert alert-success text-center'>✅ Your request has been sent successfully!</div>";
        } catch (Exception $e) {
            $message = "<div class='alert alert-danger text-center'>❌ Message could not be sent. Mailer Error: {$mail->ErrorInfo}</div>";
        }
    }
}
?><!DOCTYPE html><html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Support - SnapBazaar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #4facfe, #00f2fe);
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 80px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }
        .btn-custom {
            background: #4facfe;
            border: none;
            color: white;
            font-weight: bold;
        }
        .btn-custom:hover {
            background: #00c3ff;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4">
                    <h3 class="text-center text-primary">📞 Contact Support</h3>
                    <p class="text-center text-muted">Facing login issues? Let us know!</p>
                    <?= $message; ?>  <form method="POST">
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control" required placeholder="Your Email">
                    </div>
                    <div class="mb-3">
                        <select name="issue_type" class="form-control" required>
                            <option value="">Select Issue Type</option>
                            <option value="Login Issue">Login Issue</option>
                            <option value="Registration Issue">Registration Issue</option>
                            <option value="Payment Issue">Payment Issue</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <textarea name="description" class="form-control" rows="4" required placeholder="Describe your issue..."></textarea>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-custom">Submit Request</button>
                    </div>
                </form>

                <p class="text-center mt-3">
                    <a href="login.php" class="text-decoration-none">🔙 Back to Login</a>
                </p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>