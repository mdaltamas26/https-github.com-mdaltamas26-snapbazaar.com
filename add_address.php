<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Address Save Logic
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $user_id = intval($_SESSION['user_id']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $pincode = mysqli_real_escape_string($conn, $_POST['pincode']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $house_no = mysqli_real_escape_string($conn, $_POST['house_no']);
    $area = mysqli_real_escape_string($conn, $_POST['area']);
    $address_type = mysqli_real_escape_string($conn, $_POST['address_type']);

    $query = "INSERT INTO user_addresses (user_id, full_name, phone, pincode, state, city, house_no, area, address_type) 
              VALUES ('$user_id', '$full_name', '$phone', '$pincode', '$state', '$city', '$house_no', '$area', '$address_type')";

    if (mysqli_query($conn, $query)) {
        $_SESSION['selected_address'] = mysqli_insert_id($conn);
        header("Location: checkout.php");
        exit();
    } else {
        echo "<script>alert('Error saving address. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Delivery Address | SnapBazaar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 600px; margin: auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); }
        .form-group { margin-bottom: 15px; }
        .btn-primary { width: 100%; }
    </style>
</head>
<body>

<div class="container mt-4">
    <h2 class="text-center">📍 Add Delivery Address</h2>
    
    <form method="POST">
        <div class="form-group">
            <label>Full Name (Required) *</label>
            <input type="text" name="full_name" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Phone Number (Required) *</label>
            <input type="text" name="phone" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Pincode (Required) *</label>
            <input type="text" name="pincode" class="form-control" required>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>State (Required) *</label>
                    <input type="text" name="state" class="form-control" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>City (Required) *</label>
                    <input type="text" name="city" class="form-control" required>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>House No., Building Name (Required) *</label>
            <input type="text" name="house_no" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Road name, Area, Colony (Required) *</label>
            <input type="text" name="area" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Type of Address *</label>
            <select name="address_type" class="form-control" required>
                <option value="Home">Home</option>
                <option value="Work">Work</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Save Address</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>