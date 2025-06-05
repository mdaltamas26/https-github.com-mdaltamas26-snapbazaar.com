<?php include 'db.php'; ?><!DOCTYPE html><html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reports - SnapBazaar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.1/xlsx.full.min.js"></script>
  <style>
    body { background: #f4f6f9; padding: 20px; }
    .card { border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.06); }
    .progress { height: 20px; }
    .filter-bar .form-control { min-width: 200px; margin-right: 10px; }
  </style>
</head>
<body>
<div class="container">
  <h2 class="text-center text-primary mb-4">Reports Dashboard</h2>
  <a href="dashboard.php" class="btn btn-secondary mb-3">&larr; Back to Dashboard</a>  <!-- Filters -->  <form method="GET" class="filter-bar d-flex flex-wrap mb-4">
    <input type="date" name="from" value="<?= $_GET['from'] ?? '' ?>" class="form-control mb-2">
    <input type="date" name="to" value="<?= $_GET['to'] ?? '' ?>" class="form-control mb-2">
    <select name="payment" class="form-control mb-2">
      <option value="">All Payment Methods</option>
      <option value="UPI" <?= ($_GET['payment'] ?? '') === 'UPI' ? 'selected' : '' ?>>UPI</option>
      <option value="COD" <?= ($_GET['payment'] ?? '') === 'COD' ? 'selected' : '' ?>>Cash on Delivery</option>
    </select>
    <select name="status" class="form-control mb-2">
      <option value="">All Statuses</option>
      <option value="Pending" <?= ($_GET['status'] ?? '') === 'Pending' ? 'selected' : '' ?>>Pending</option>
      <option value="Delivered" <?= ($_GET['status'] ?? '') === 'Delivered' ? 'selected' : '' ?>>Delivered</option>
      <option value="Cancelled" <?= ($_GET['status'] ?? '') === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
    </select>
    <select name="chart_range" class="form-control mb-2">
      <option value="7" <?= ($_GET['chart_range'] ?? '') == '7' ? 'selected' : '' ?>>Last 7 Days</option>
      <option value="30" <?= ($_GET['chart_range'] ?? '') == '30' ? 'selected' : '' ?>>Last 30 Days</option>
      <option value="365" <?= ($_GET['chart_range'] ?? '') == '365' ? 'selected' : '' ?>>Last 1 Year</option>
      <option value="1" <?= ($_GET['chart_range'] ?? '') == '1' ? 'selected' : '' ?>>Today</option>
      <option value="all" <?= ($_GET['chart_range'] ?? '') == 'all' ? 'selected' : '' ?>>All Time</option>
    </select>
    <button type="submit" class="btn btn-primary mb-2">Filter</button>
    <button type="button" onclick="window.print()" class="btn btn-secondary mb-2 ms-2">Print</button>
    <button type="button" onclick="exportToExcel()" class="btn btn-success mb-2 ms-2">Export Excel</button>
  </form>  <?php
  $where = "WHERE 1=1";
  if (!empty($_GET['from'])) $where .= " AND DATE(created_at) >= '" . $_GET['from'] . "'";
  if (!empty($_GET['to'])) $where .= " AND DATE(created_at) <= '" . $_GET['to'] . "'";
  if (!empty($_GET['payment'])) $where .= " AND payment_method = '" . $_GET['payment'] . "'";
  if (!empty($_GET['status'])) $where .= " AND order_status = '" . $_GET['status'] . "'";

  $summary = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as orders, SUM(total_price) as revenue FROM my_orders $where AND order_status='Delivered'"));
  $totalOrders = $summary['orders'];
  $totalRevenue = $summary['revenue'] ?? 0;

  $topProducts = mysqli_query($conn, "SELECT product_name, COUNT(*) as count FROM my_orders $where AND order_status='Delivered' GROUP BY product_name ORDER BY count DESC LIMIT 5");

  $chartRange = $_GET['chart_range'] ?? 7;
  if ($chartRange === 'all') {
    $datesQuery = mysqli_query($conn, "SELECT DATE(created_at) as date FROM my_orders $where AND order_status='Delivered' GROUP BY DATE(created_at) ORDER BY date ASC");
    $dates = [];
    while ($r = mysqli_fetch_assoc($datesQuery)) {
      $dates[] = $r['date'];
    }
  } else {
    $range = is_numeric($chartRange) ? intval($chartRange) : 7;
    $dates = [];
    for ($i = $range - 1; $i >= 0; $i--) {
      $dates[] = date('Y-m-d', strtotime("-$i days"));
    }
  }

  $dailyData = [];
  foreach ($dates as $date) {
    $orders = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM my_orders $where AND order_status='Delivered' AND DATE(created_at) = '$date'"));
    $revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_price) as total FROM my_orders $where AND order_status='Delivered' AND DATE(created_at) = '$date'"))['total'] ?? 0;
    $dailyData[] = ['date' => $date, 'orders' => $orders, 'revenue' => $revenue];
  }

  $statusQuery = mysqli_query($conn, "SELECT order_status, COUNT(*) as count FROM my_orders $where GROUP BY order_status");
  $statusData = [];
  $totalStatusOrders = 0;
  while($row = mysqli_fetch_assoc($statusQuery)) {
    $statusData[] = $row;
    $totalStatusOrders += $row['count'];
  }
  ?>  <div class="row mb-4">
    <div class="col-md-6">
      <div class="card bg-primary text-white p-4">
        <h5>Total Delivered Orders</h5>
        <h3><?= $totalOrders ?></h3>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card bg-success text-white p-4">
        <h5>Total Revenue</h5>
        <h3>₹<?= number_format($totalRevenue, 2) ?></h3>
      </div>
    </div>
  </div>  <div class="card p-4 mb-4">
    <h5 class="mb-3">📊 Orders Over Selected Period</h5>
    <canvas id="ordersChart" height="100"></canvas>
  </div>  <div class="card p-4 mb-4" id="topProductsTable">
    <h5 class="mb-3">⭐ Top 5 Best-Selling Products</h5>
    <ul class="list-group">
      <?php while ($row = mysqli_fetch_assoc($topProducts)) { ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <?= $row['product_name'] ?>
          <span class="badge bg-primary rounded-pill"><?= $row['count'] ?> sold</span>
        </li>
      <?php } ?>
    </ul>
  </div>  <div class="card p-4 mb-5">
    <h5 class="mb-3">📦 Order Status Distribution</h5>
    <?php foreach ($statusData as $s) {
      $percentage = round(($s['count'] / $totalStatusOrders) * 100);
    ?>
      <div class="mb-2">
        <strong><?= $s['order_status'] ?> (<?= $s['count'] ?> orders)</strong>
        <div class="progress">
          <div class="progress-bar bg-info" style="width: <?= $percentage ?>%"><?= $percentage ?>%</div>
        </div>
      </div>
    <?php } ?>
  </div>
</div><script>
const ctx = document.getElementById('ordersChart').getContext('2d');
new Chart(ctx, {
  type: 'line',
  data: {
    labels: [<?= implode(",", array_map(fn($d) => "'" . date('d M', strtotime($d['date'])) . "'", $dailyData)) ?>],
    datasets: [{
      label: 'Orders',
      data: [<?= implode(",", array_column($dailyData, 'orders')) ?>],
      borderColor: '#0d6efd',
      backgroundColor: 'rgba(13,110,253,0.1)',
      tension: 0.4,
      fill: true
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: true } },
    scales: { y: { beginAtZero: true } }
  }
});
</script><script>
function exportToExcel() {
  const table = document.getElementById('topProductsTable');
  const wb = XLSX.utils.table_to_book(table, { sheet: "Top Products" });
  XLSX.writeFile(wb, "snapbazaar_reports.xlsx");
}
</script></body>
</html>