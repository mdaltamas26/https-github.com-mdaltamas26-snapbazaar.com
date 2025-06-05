<?php
// generate_statement.php
require_once __DIR__ . '/vendor/autoload.php'; // Make sure you installed mPDF via Composer

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $pincode = $_POST['pincode'];
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];
    $account = $_POST['account'];
    $cif = $_POST['cif'];
    $ifsc = $_POST['ifsc'];
    $branch = $_POST['branch'];
    $from = $_POST['from'];
    $to = $_POST['to'];
    $initial_balance = (float) $_POST['initial_balance'];

    // Save banner
    $banner_path = '';
    if (isset($_FILES['banner']) && $_FILES['banner']['error'] == 0) {
        $ext = pathinfo($_FILES['banner']['name'], PATHINFO_EXTENSION);
        $banner_path = 'uploads/banner_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['banner']['tmp_name'], $banner_path);
    }

    // Process transaction table
    $transactions = json_decode($_POST['transactions'], true);
    $balance = $initial_balance;
    
    $transaction_rows = '';
    $i = 1;
    foreach ($transactions as $txn) {
        $amount = (float)$txn['amount'];
        $type = $txn['type'];
        $balance += ($type == 'Cr') ? $amount : -$amount;
        $transaction_rows .= "<tr>
            <td>{$i}</td>
            <td>{$txn['date']}</td>
            <td>{$txn['txn_id']}</td>
            <td>{$txn['remarks']}</td>
            <td>{$amount} ({$type})</td>
            <td>" . number_format($balance, 2) . "</td>
        </tr>";
        $i++;
    }

    $mpdf = new \Mpdf\Mpdf();
    ob_start();
?><html>
<head><style>
body { font-family: sans-serif; font-size: 12px; }
table { border-collapse: collapse; width: 100%; }
th, td { border: 1px solid #aaa; padding: 6px; text-align: left; }
.heading { font-size: 18px; font-weight: bold; margin-top: 10px; }
</style></head>
<body>
<?php if ($banner_path): ?>
    <img src="<?= $banner_path ?>" style="width: 100%; height: auto;">
<?php endif; ?>
<h2>Details of Statement</h2>
<table>
<tr><td><b>Name</b></td><td><?= $name ?></td><td><b>Customer/CIF ID</b></td><td><?= $cif ?></td></tr>
<tr><td><b>Address</b></td><td><?= $address ?></td><td><b>Account No</b></td><td><?= $account ?></td></tr>
<tr><td><b>City</b></td><td><?= $city ?></td><td><b>IFSC</b></td><td><?= $ifsc ?></td></tr>
<tr><td><b>State</b></td><td><?= $state ?></td><td><b>Branch</b></td><td><?= $branch ?></td></tr>
<tr><td><b>Pincode</b></td><td><?= $pincode ?></td><td><b>Email</b></td><td><?= $email ?></td></tr>
<tr><td><b>Mobile</b></td><td><?= $mobile ?></td><td><b>Statement Period</b></td><td><?= $from ?> to <?= $to ?></td></tr>
</table>
<br>
<h3>Transactions</h3>
<table>
<tr><th>S.No</th><th>Date</th><th>Transaction Id</th><th>Remarks</th><th>Amount (Rs.)</th><th>Balance (Rs.)</th></tr>
<?= $transaction_rows ?>
</table>
</body>
</html>
<?php
    $html = ob_get_clean();
    $mpdf->WriteHTML($html);
    $mpdf->Output('statement.pdf', 'I');
}
?>