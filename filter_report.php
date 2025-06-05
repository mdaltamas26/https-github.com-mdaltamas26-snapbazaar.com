<?php
include 'db.php';

$start = $_GET['start'];
$end = $_GET['end'];

$data = [
  "dates" => [],
  "revenues" => []
];

$query = "SELECT DATE(created_at) as date, SUM(total_price) as revenue 
          FROM my_orders 
          WHERE created_at BETWEEN '$start' AND '$end' 
            AND order_status = 'Delivered'
          GROUP BY DATE(created_at)
          ORDER BY date ASC";

$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
  $data['dates'][] = $row['date'];
  $data['revenues'][] = $row['revenue'];
}

echo json_encode($data);
?>