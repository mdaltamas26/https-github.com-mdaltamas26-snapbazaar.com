<?php
session_start();
include 'config.php';

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo "<div class='alert alert-warning text-center mt-5'>Please login to view your address.</div>";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $pincode = mysqli_real_escape_string($conn, $_POST['pincode']);

    $check = mysqli_query($conn, "SELECT * FROM addresses WHERE user_id = '$user_id'");
    if (mysqli_num_rows($check) > 0) {
        // Update existing
        mysqli_query($conn, "UPDATE addresses SET name='$name', phone='$phone', address='$address', city='$city', state='$state', pincode='$pincode' WHERE user_id='$user_id'");
        $success = "Address updated successfully!";
    } else {
        // Insert new
        mysqli_query($conn, "INSERT INTO addresses (user_id, name, phone, address, city, state, pincode) VALUES ('$user_id', '$name', '$phone', '$address', '$city', '$state', '$pincode')");
        $success = "Address added successfully!";
    }
}

// Fetch current address
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM addresses WHERE user_id = '$user_id'")) ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Address | SnapBazaar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f6f8fb;
            font-family: 'Segoe UI', sans-serif;
        }
        .address-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.05);
            padding: 35px;
            margin: 50px auto;
            max-width: 750px;
        }
        .address-header {
            font-size: 24px;
            font-weight: 600;
            color: #343a40;
            margin-bottom: 25px;
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 12px;
        }
        .form-label {
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="address-card">
        <div class="address-header">📍 My Delivery Address</div>

        <?php if (!empty($success)) : ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($data['name'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-control" required value="<?= htmlspecialchars($data['phone'] ?? '') ?>">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Full Address</label>
                <textarea name="address" class="form-control" rows="2" required><?= htmlspecialchars($data['address'] ?? '') ?></textarea>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" required value="<?= htmlspecialchars($data['city'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">State</label>
                    <input type="text" name="state" class="form-control" required value="<?= htmlspecialchars($data['state'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Pincode</label>
                    <input type="text" name="pincode" class="form-control" required value="<?= htmlspecialchars($data['pincode'] ?? '') ?>">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Country</label>
                <input type="text" class="form-control" value="India" readonly>
            </div>

            <div class="d-grid gap-2">
                <button class="btn btn-dark btn-lg" type="submit">💾 Save Address</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
