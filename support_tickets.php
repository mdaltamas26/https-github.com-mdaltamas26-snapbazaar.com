<?php
session_start();
include 'db_connect.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';  
require 'PHPMailer/src/SMTP.php';  
require 'PHPMailer/src/Exception.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message_text = mysqli_real_escape_string($conn, $_POST['message']);
    $priority = mysqli_real_escape_string($conn, $_POST['priority']);

    // Fetch user email and name
    $userResult = mysqli_query($conn, "SELECT name, email FROM users WHERE id = '$user_id'");
    $userData = mysqli_fetch_assoc($userResult);
    $user_name = $userData['name'];
    $user_email = $userData['email'];

    // File Upload Handling
    $file_path = NULL;
    if (!empty($_FILES['attachment']['name'])) {
        $target_dir = "uploads/";
        $file_name = time() . "_" . basename($_FILES["attachment"]["name"]);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $target_file)) {
            $file_path = $target_file;
        }
    }

    $query = "INSERT INTO support_tickets (user_id, subject, message, status, priority, file_path, created_at, updated_at) 
              VALUES ('$user_id', '$subject', '$message_text', 'Pending', '$priority', '$file_path', NOW(), NOW())";

    if (mysqli_query($conn, $query)) {
        // Send Email to Admin
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'angryffgaming8@gmail.com';
            $mail->Password = 'mnys tasw qbsx kbrw';  
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('angryffgaming888@gmail.com', 'SnapBazaar');
            $mail->addAddress('angryffgaming888@gmail.com'); // Admin Email
            $mail->isHTML(true);
            $mail->Subject = "🛠 New Support Ticket Submitted";

            $file_link = $file_path ? "<br><b>File:</b> <a href='http://localhost/snapbazaar/uploads/' target='_blank'>View Attachment</a>" : "";

            $mail->Body = "
                <h3>📩 New Support Ticket Received</h3>
                <p><b>User Name:</b> $user_name</p>
                <p><b>Email:</b> $user_email</p>
                <p><b>Subject:</b> $subject</p>
                <p><b>Priority:</b> $priority</p>
                <p><b>Message:</b><br>$message_text</p>
                $file_link
                <hr>
                <small>SnapBazaar Support System</small>
            ";

            $mail->send();
            $message = "<div class='alert alert-success'>✅ Support ticket submitted successfully!</div>";
        } catch (Exception $e) {
            $message = "<div class='alert alert-warning'>⚠ Ticket saved, but email not sent. Error: {$mail->ErrorInfo}</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>❌ Error: " . mysqli_error($conn) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Tickets - SnapBazaar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <a href="home.php" class="btn btn-primary">🏠 Home</a>
    <h2 class="mt-3">📩 Raise a Support Ticket</h2>
    <?= $message ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Subject</label>
            <select name="subject" class="form-control" required>
                <option value="Order Issue">Order Issue</option>
                <option value="Payment Problem">Payment Problem</option>
                <option value="Product Not Delivered">Product Not Delivered</option>
                <option value="Refund Request">Refund Request</option>
                <option value="Other">Other</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Priority</label>
            <select name="priority" class="form-control">
                <option value="Low">Low</option>
                <option value="Medium" selected>Medium</option>
                <option value="High">High</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Message</label>
            <textarea name="message" class="form-control" rows="4" placeholder="Describe your issue..." required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Attach File (Optional)</label>
            <input type="file" name="attachment" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Submit Ticket</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>