<?php
session_start();
include 'db.php'; 

// Handle File Upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['upi_qr_code'])) {
    $target_dir = "uploads/"; // Upload folder
    $file_name = basename($_FILES["upi_qr_code"]["name"]);
    $target_file = $target_dir . $file_name;
    
    // Allow only image files
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    if (in_array($imageFileType, ["jpg", "jpeg", "png"])) {
        if (move_uploaded_file($_FILES["upi_qr_code"]["tmp_name"], $target_file)) {
            // Save to database (Only 1 QR Code)
            mysqli_query($conn, "DELETE FROM settings");
            mysqli_query($conn, "INSERT INTO settings (upi_qr_code) VALUES ('$target_file')");
            $_SESSION['success'] = "UPI QR Code uploaded successfully!";
        } else {
            $_SESSION['error'] = "Error uploading file.";
        }
    } else {
        $_SESSION['error'] = "Only JPG, JPEG, and PNG files are allowed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload UPI QR Code</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">🔄 Upload UPI QR Code</h2>

    <?php if (isset($_SESSION['success'])) { ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php } ?>
    <?php if (isset($_SESSION['error'])) { ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php } ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Upload UPI QR Code (PNG, JPG, JPEG)</label>
            <input type="file" name="upi_qr_code" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Upload</button>
    </form>
</div>
</body>
</html>