<?php
require_once __DIR__ . '/vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $pincode = $_POST['pincode'];
    $mobile = $_POST['mobile'];
    $account_number = $_POST['account_number'];
    $cif_id = $_POST['cif_id'];

    // Upload banner
    $banner_path = '';
    if (isset($_FILES['banner']) && $_FILES['banner']['error'] === 0) {
        $ext = pathinfo($_FILES['banner']['name'], PATHINFO_EXTENSION);
        $filename = 'banner_' . time() . '.' . $ext;
        $upload_path = 'uploads/' . $filename;
        move_uploaded_file($_FILES['banner']['tmp_name'], $upload_path);
        $banner_path = $upload_path;
    }

    // Get transaction data
    $transactions = [];
    for ($i = 0; $i < count($_POST['txn_date']); $i++) {
        $transactions[] = [
            'date' => $_POST['txn_date'][$i],
            'id' => $_POST['txn_id'][$i],
            'remarks' => $_POST['txn_remarks'][$i],
            'amount' => $_POST['txn_amount'][$i],
            'balance' => $_POST['txn_balance'][$i],
        ];
    }

    // Create PDF using mPDF
    $mpdf = new \Mpdf\Mpdf();
    ob_start();
    ?>

    <div style="text-align: center;">
        <img src="<?= $banner_path ?>" style="width: 100%; max-height: 100px;"><br><br>
        <h2>Details of Statement</h2>
    </div>

    <table style="width: 100%; margin-bottom: 20px;" border="1" cellspacing="0" cellpadding="5">
        <tr><td><b>Name:</b></td><td><?= htmlspecialchars($name) ?></td><td><b>CIF ID:</b></td><td><?= $cif_id ?></td></tr>
        <tr><td><b>Address:</b></td><td colspan="3"><?= htmlspecialchars($address) ?>, <?= $city ?>, <?= $state ?> - <?= $pincode ?></td></tr>
        <tr><td><b>Mobile:</b></td><td><?= $mobile ?></td><td><b>Account Number:</b></td><td><?= $account_number ?></td></tr>
    </table>

    <table width="100%" border="1" cellspacing="0" cellpadding="5">
        <thead>
        <tr>
            <th>S.No</th>
            <th>Date</th>
            <th>Txn ID</th>
            <th>Remarks</th>
            <th>Amount (Rs.)</th>
            <th>Balance (Rs.)</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($transactions as $index => $txn): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= htmlspecialchars($txn['date']) ?></td>
                <td><?= htmlspecialchars($txn['id']) ?></td>
                <td><?= htmlspecialchars($txn['remarks']) ?></td>
                <td><?= htmlspecialchars($txn['amount']) ?></td>
                <td><?= htmlspecialchars($txn['balance']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php
    $html = ob_get_clean();
    $mpdf->WriteHTML($html);
    $mpdf->Output("statement.pdf", "I"); // Output to browser
}