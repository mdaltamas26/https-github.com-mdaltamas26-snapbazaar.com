<?php
include 'db.php';

if (isset($_POST['order_id']) && isset($_POST['lat']) && isset($_POST['lng'])) {
    $order_id = $_POST['order_id'];
    $lat = $_POST['lat'];
    $lng = $_POST['lng'];

    $query = "UPDATE my_orders SET delivery_lat='$lat', delivery_lng='$lng' WHERE id='$order_id'";
    if (mysqli_query($conn, $query)) {
        echo "Location updated successfully!";
    } else {
        echo "Error updating location!";
    }
} else {
    echo "Invalid data!";
}
?>