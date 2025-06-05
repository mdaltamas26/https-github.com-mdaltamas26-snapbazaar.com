<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Payments - SnapBazaar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <style>
        body { background-color: #f8f9fa; padding: 30px; }
        .card { border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .badge-success { background-color: #28a745; }
        .badge-warning { background-color: #ffc107; }
        .badge-danger { background-color: #dc3545; }

        /* 👇 PRINT CSS START */
        @media print {
            .btn,
            .row.mb-3,
            .mt-4,
            #searchInput,
            #paymentFilter,
            #statusFilter,
            #startDate,
            #endDate,
            .dataTables_filter,
            .dataTables_length,
            .dataTables_info,
            .dataTables_paginate {
                display: none !important;
            }

            a {
                text-decoration: none;
                color: black;
            }

            body {
                padding: 0;
                background-color: white;
            }

            .card {
                box-shadow: none;
            }
        }
        /* 👆 PRINT CSS END */
    </style>
</head>
<body>
<div class="container">
    <h2 class="text-center mb-4">💰 <span class="text-primary">Manage Payments</span></h2>
    <a href="dashboard.php" class="btn btn-secondary mb-3">&larr; Back to Dashboard</a>

    <div class="card p-4">
        <div class="row mb-3">
            <div class="col-md-3">
                <label>Date Range</label>
                <input type="date" id="startDate" class="form-control">
                <input type="date" id="endDate" class="form-control mt-2">
            </div>
            <div class="col-md-3">
                <label>Payment Method</label>
                <select id="paymentFilter" class="form-control">
                    <option value="">All</option>
                    <option value="UPI">UPI</option>
                    <option value="COD">COD</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>Status</label>
                <select id="statusFilter" class="form-control">
                    <option value="">All</option>
                    <option value="Paid">Paid</option>
                    <option value="Pending">Pending</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>🔍 Search</label>
                <input type="text" id="searchInput" class="form-control" placeholder="Search by anything...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-bordered" id="paymentsTable">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>User ID</th>
                        <th>Order ID</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Order Date</th>
                        <th>Address</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include 'db.php';
                    $query = "SELECT o.*, a.full_name, a.phone, a.address, a.city, a.state, a.pincode 
                              FROM my_orders o 
                              LEFT JOIN addresses a ON o.user_id = a.user_id 
                              ORDER BY o.created_at DESC";
                    $result = mysqli_query($conn, $query);
                    $i = 1;
                    $total = 0;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $full_address = $row['full_name'] . ', ' . $row['phone'] . ', ' . $row['address'] . ', ' . $row['city'] . ', ' . $row['state'] . ' - ' . $row['pincode'];

                        if ($row['payment_method'] === 'UPI' || ($row['payment_method'] === 'COD' && $row['order_status'] === 'Completed')) {
                            $status = 'Paid';
                            $statusBadge = 'success';
                            $total += $row['total_price'];
                        } else {
                            $status = 'Pending';
                            $statusBadge = 'warning';
                        }
                        echo "<tr>
                            <td>{$i}</td>
                            <td>{$row['user_id']}</td>
                            <td><a href='admin_manage_payment.php?id={$row['id']}'>{$row['id']}</a></td>
                            <td>₹{$row['total_price']}</td>
                            <td>{$row['payment_method']}</td>
                            <td>" . date('d M Y, h:i A', strtotime($row['created_at'])) . "</td>
                            <td>{$full_address}</td>
                            <td><span class='badge badge-{$statusBadge}'>{$status}</span></td>
                        </tr>";
                        $i++;
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            <strong>Total Earnings:</strong> ₹<span id="totalEarnings"><?php echo number_format($total, 2); ?></span>
        </div>

        <div class="mt-4">
            <button class="btn btn-danger" onclick="exportToPDF()">📄 Export PDF</button>
            <button class="btn btn-success" onclick="exportToExcel()">📁 Export Excel</button>
            <button class="btn btn-info" onclick="window.print()">🖨️ Print</button>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
$(document).ready(function () {
    const table = $('#paymentsTable').DataTable();

    $('#searchInput, #paymentFilter, #statusFilter, #startDate, #endDate').on('input change', function () {
        table.draw();
    });

    $.fn.dataTable.ext.search.push(function (settings, data) {
        const payment = $('#paymentFilter').val().toLowerCase();
        const status = $('#statusFilter').val().toLowerCase();
        const start = $('#startDate').val();
        const end = $('#endDate').val();
        const method = data[4].toLowerCase();
        const currentStatus = data[7].toLowerCase();
        const orderDate = new Date(data[5]);

        if ((payment && method !== payment) || (status && currentStatus !== status)) return false;

        if (start) {
            const startDate = new Date(start);
            if (orderDate < startDate) return false;
        }
        if (end) {
            const endDate = new Date(end);
            if (orderDate > endDate) return false;
        }
        return true;
    });
});

async function exportToPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    doc.text("Manage Payments", 14, 15);

    const headers = ["#", "User ID", "Order ID", "Amount", "Payment Method", "Order Date", "Address", "Status"];
    const rows = [];

    $('#paymentsTable tbody tr:visible').each(function () {
        const row = [];
        $(this).find('td').each(function () {
            row.push($(this).text());
        });
        rows.push(row);
    });

    doc.autoTable({ head: [headers], body: rows, startY: 20 });
    doc.save("payments.pdf");
}

function exportToExcel() {
    const table = document.getElementById("paymentsTable");
    const wb = XLSX.utils.table_to_book(table, { sheet: "Payments" });
    XLSX.writeFile(wb, "payments.xlsx");
}
</script>

</body>
</html>
