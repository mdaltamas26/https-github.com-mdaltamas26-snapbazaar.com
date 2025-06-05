<!DOCTYPE html>
<html>
<head>
    <title>Update Delivery Location</title>
</head>
<body>
    <h2>Update Order Location</h2>
    <form action="update_location.php" method="POST">
        <label>Order ID:</label>
        <input type="number" name="order_id" required><br><br>

        <label>Latitude:</label>
        <input type="text" name="lat" required><br><br>

        <label>Longitude:</label>
        <input type="text" name="lng" required><br><br>

        <button type="submit">Update Location</button>
    </form>
</body>
</html>