<?php
include 'db.php';

header('Content-Type: application/json');

$range = $_GET['range'] ?? 'week';
$start = $_GET['start'] ?? null;
$end = $_GET['end'] ?? null;

$labels = [];
$orders = [];

if ($range === 'custom' && $start && $end) {
    $query = "SELECT DATE(order_date) AS order_day, COUNT(*) AS total 
              FROM my_orders 
              WHERE order_status = 'Delivered' AND DATE(order_date) BETWEEN '$start' AND '$end' 
              GROUP BY DATE(order_date)";
} elseif ($range === 'month') {
    $query = "SELECT DATE(order_date) AS order_day, COUNT(*) AS total 
              FROM my_orders 
              WHERE order_status = 'Delivered' AND MONTH(order_date) = MONTH(CURDATE()) 
              AND YEAR(order_date) = YEAR(CURDATE()) 
              GROUP BY DATE(order_date)";
} elseif ($range === 'year') {
    $query = "SELECT MONTH(order_date) AS month, COUNT(*) AS total 
              FROM my_orders 
              WHERE order_status = 'Delivered' AND YEAR(order_date) = YEAR(CURDATE()) 
              GROUP BY MONTH(order_date)";
} else {
    // Default: last 7 days
    $query = "SELECT DATE(order_date) AS order_day, COUNT(*) AS total 
              FROM my_orders 
              WHERE order_status = 'Delivered' AND order_date >= CURDATE() - INTERVAL 6 DAY 
              GROUP BY DATE(order_date)";
}

$result = mysqli_query($conn, $query);

if ($range === 'year') {
    $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    $data = array_fill(0, 12, 0);

    while ($row = mysqli_fetch_assoc($result)) {
        $monthIndex = $row['month'] - 1;
        $data[$monthIndex] = (int)$row['total'];
    }

    $labels = $months;
    $orders = $data;
} else {
    while ($row = mysqli_fetch_assoc($result)) {
        $labels[] = $row['order_day'];
        $orders[] = (int)$row['total'];
    }
}

echo json_encode(['labels' => $labels, 'orders' => $orders]);