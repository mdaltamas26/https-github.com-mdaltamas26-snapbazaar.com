<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $userId = $_POST['user_id'];
    $newReferralEarned = $_POST['referral_earned'];
    $newWalletBalance = $_POST['wallet_balance'];

    $updateQuery = $conn->prepare("UPDATE users SET referral_earned = ?, wallet_balance = ? WHERE id = ?");
    $updateQuery->bind_param("ddi", $newReferralEarned, $newWalletBalance, $userId);
    if ($updateQuery->execute()) {
        $_SESSION['success_msg'] = "User ID $userId updated successfully!";
    } else {
        $_SESSION['error_msg'] = "Failed to update user ID $userId.";
    }
    $updateQuery->close();

    header("Location: manage_referrals.php");
    exit();
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$whereClause = '';
if ($search != '') {
    $search = $conn->real_escape_string($search);
    $whereClause = "WHERE u.name LIKE '%$search%' OR u.email LIKE '%$search%' OR u.mobile LIKE '%$search%' OR u.referral_code LIKE '%$search%'";
}

$query = "SELECT 
    u.id, u.name, u.email, u.mobile, u.referral_code, u.referral_earned, u.wallet_balance, u.created_at,
    u.referred_by,
    r.name AS referred_by_name
    FROM users u
    LEFT JOIN users r ON u.referred_by = r.id
    $whereClause
    ORDER BY u.created_at DESC";

$result = $conn->query($query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Referrals | Admin - SnapBazaar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f1f2f6; font-family: 'Segoe UI', sans-serif; }
        .card { border: none; box-shadow: 0 4px 10px rgba(0,0,0,0.08); }
        .badge-ref { background-color: #1e90ff; font-size: 13px; }
        .form-control-sm { font-size: 14px; }
        th, td { vertical-align: middle; }
        .btn-sm { font-size: 13px; padding: 3px 10px; }
        @media (max-width: 768px) {
            .table-responsive { overflow-x: auto; }
        }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="card p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">📢 Manage Referrals</h4>
            <div>
                <button onclick="exportToPDF()" class="btn btn-danger btn-sm me-2">Export PDF</button>
                <button onclick="exportToExcel()" class="btn btn-success btn-sm me-2">Export Excel</button>
                <button onclick="window.print()" class="btn btn-secondary btn-sm">Print</button>
            </div>
        </div>

        <?php if (isset($_SESSION['success_msg'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error_msg'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error_msg']; unset($_SESSION['error_msg']); ?></div>
        <?php endif; ?>

        <form method="get" class="mb-3">
            <div class="input-group">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder="🔍 Search by name, email, mobile or referral code...">
                <button class="btn btn-primary">Search</button>
            </div>
        </form>

        <div class="table-responsive">
        <table class="table table-bordered align-middle table-hover" id="referralTable">
            <thead class="table-dark">
                <tr>
                    <th>#ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Referral Code</th>
                    <th>Referred By</th>
                    <th>Referral Earned</th>
                    <th>Wallet</th>
                    <th>Joined On</th>
                    <th>Referred Users</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()) {
                $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE referred_by = ?");
                $stmt->bind_param("i", $row['id']);
                $stmt->execute();
                $stmt->bind_result($total_referred);
                $stmt->fetch();
                $stmt->close();
            ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= $row['email'] ?></td>
                    <td><?= $row['mobile'] ?></td>
                    <td><span class="badge badge-ref"><?= $row['referral_code'] ?></span></td>
                    <td>
                        <?= $row['referred_by_name'] ? '#' . $row['referred_by'] . ' - ' . htmlspecialchars($row['referred_by_name']) : '—' ?>
                    </td>
                    <td>
                        <form method="post" class="d-flex">
                            <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                            <input type="number" step="0.01" name="referral_earned" value="<?= $row['referral_earned'] ?>" class="form-control form-control-sm me-2" style="width: 80px;">
                    </td>
                    <td>
                            <input type="number" step="0.01" name="wallet_balance" value="<?= $row['wallet_balance'] ?>" class="form-control form-control-sm me-2" style="width: 80px;">
                    </td>
                    <td><?= date("d M Y, h:i A", strtotime($row['created_at'])) ?></td>
                    <td>
                            <?= $total_referred ?> users
                            <button type="submit" name="update_user" class="btn btn-success btn-sm ms-2">Update</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
<script>
function exportToPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    doc.text("SnapBazaar - Referral Report", 14, 10);
    doc.autoTable({ html: '#referralTable' });
    doc.save("referral_report.pdf");
}

function exportToExcel() {
    let table = document.getElementById("referralTable");
    let csv = [];
    for (let row of table.rows) {
        let cols = Array.from(row.cells).map(cell => '"' + cell.innerText + '"');
        csv.push(cols.join(","));
    }
    let csvFile = new Blob([csv.join("\n")], { type: "text/csv" });
    let downloadLink = document.createElement("a");
    downloadLink.download = "referral_report.csv";
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
}
</script>
</body>
</html>