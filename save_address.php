<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $pincode = mysqli_real_escape_string($conn, $_POST['pincode']);

    // Check if user already has an address
    $check_query = "SELECT * FROM addresses WHERE user_id = '$user_id'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        // Update existing address
        $query = "UPDATE addresses SET full_name='$full_name', phone='$phone', address='$address', city='$city', state='$state', pincode='$pincode' WHERE user_id='$user_id'";
    } else {
        // Insert new address
        $query = "INSERT INTO addresses (user_id, full_name, phone, address, city, state, pincode) VALUES ('$user_id', '$full_name', '$phone', '$address', '$city', '$state', '$pincode')";
    }

    if (mysqli_query($conn, $query)) {
        header("Location: checkout.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>