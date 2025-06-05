<!DOCTYPE html>
<html>
<head>
    <title>Auto GPS Delivery Update</title>
</head>
<body>
    <h2>Auto Location Update</h2>

    <form id="locationForm">
        <label>Order ID:</label>
        <input type="number" name="order_id" id="order_id" required><br><br>
        <button type="submit">Start Tracking</button>
    </form>

    <div id="status"></div>

    <script>
    document.getElementById("locationForm").addEventListener("submit", function(e) {
        e.preventDefault();
        const orderId = document.getElementById("order_id").value;

        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                fetch('update_location.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: order_id=${orderId}&lat=${lat}&lng=${lng}
                })
                .then(response => response.text())
                .then(data => {
                    document.getElementById("status").innerText = data;
                });
            });
        } else {
            alert("Geolocation not supported!");
        }
    });
    </script>
</body>
</html>