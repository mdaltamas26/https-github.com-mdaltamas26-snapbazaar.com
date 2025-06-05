<?php
include 'db.php';
session_start();

if (!isset($_GET['order_id']) || !isset($_SESSION['user_id'])) {
    echo "Unauthorized access!";
    exit;
}

$order_id = intval($_GET['order_id']);
$user_id = $_SESSION['user_id'];

// Fetch order from my_orders
$stmt = $conn->prepare("SELECT * FROM my_orders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Order not found.";
    exit;
}

$order = $result->fetch_assoc();
$status = strtolower($order['order_status']);
$cancelled = ($status === 'cancelled');

// Define steps for progress bar
$steps = ['pending' => 0, 'processing' => 1, 'shipped' => 2, 'out for delivery' => 3, 'delivered' => 4];
$current_step = isset($steps[$status]) ? $steps[$status] : 0;

// Payment status display logic
$payment_method = strtolower($order['payment_method']);
$payment_status = strtolower($order['payment_status']);
$final_payment_status = ($payment_method === 'upi' || $payment_status === 'paid') ? 'Paid' : ucfirst($order['payment_status']);

// Badge classes for order status
$status_badge = 'secondary';
switch ($status) {
    case 'delivered': $status_badge = 'success'; break;
    case 'processing': $status_badge = 'warning'; break;
    case 'cancelled': $status_badge = 'danger'; break;
    case 'pending': $status_badge = 'info'; break;
}

// Refund status check
$refund_status = '';
$refund_requested = false;
$check_refund = $conn->prepare("SELECT status FROM refund_requests WHERE order_id = ? AND user_id = ? AND type = 'refund' LIMIT 1");
$check_refund->bind_param("ii", $order_id, $user_id);
$check_refund->execute();
$refund_result = $check_refund->get_result();
if ($refund_result->num_rows > 0) {
    $row = $refund_result->fetch_assoc();
    $refund_requested = true;
    $refund_status = $row['status'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Order Details - SnapBazaar</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        .card { border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .badge-status { font-size: 0.9rem; padding: 5px 10px; border-radius: 10px; }
        .btn-back { margin-bottom: 20px; }
    </style>
</head>
<body>
<div class="container mt-5">
    <h3 class="mb-4">
        Order Details <span class="text-muted">(Order ID: #<?= htmlspecialchars($order['id']); ?>)</span>
    </h3>
    <a href="home.php" class="btn btn-secondary btn-back">&larr; Back to Home</a>

    <!-- Live Tracking -->
    <div class="card p-4 mb-4">
        <h5 class="mb-4">Live Order Tracking</h5>
        <div class="row text-center justify-content-between px-2">
            <?php
            $active_color = '#28a745';
            $inactive_color = '#ccc';

            if ($cancelled) {
                // Cancelled flow
                $labels = ['Order Placed', 'Cancelled'];
                $icons = ['bi-cart-check', 'bi-x-circle'];
                $subtexts = ['We received your order.', 'Your order has been cancelled.'];
                $cancel_step = 1;
                foreach ($labels as $i => $label):
            ?>
            <div class="col d-flex flex-column align-items-center position-relative">
                <?php if ($i !== 0): ?>
                    <div class="position-absolute top-50 start-0 translate-middle-y w-100" style="height: 4px; z-index: 0;">
                        <div style="height: 100%; width: 100%; background-color: <?= $i <= $cancel_step ? $active_color : $inactive_color ?>;"></div>
                    </div>
                <?php endif; ?>
                <div class="rounded-circle d-flex justify-content-center align-items-center mb-2" style="z-index: 1; width: 45px; height: 45px; font-size: 20px; background-color: <?= $i <= $cancel_step ? $active_color : $inactive_color ?>; color: white;">
                    <i class="bi <?= $icons[$i] ?>"></i>
                </div>
                <div class="fw-semibold <?= $i <= $cancel_step ? 'text-success' : 'text-muted' ?>"><?= htmlspecialchars($label) ?></div>
                <small class="text-muted"><?= htmlspecialchars($subtexts[$i]) ?></small>
            </div>
            <?php endforeach;
            } else {
                // Normal flow
                $labels = ['Order Placed', 'Processing', 'Shipped', 'Out for Delivery', 'Delivered'];
                $subtexts = ['We have received your order.', 'Packing your items.', 'Courier picked it up.', 'It\'s on the way.', 'Delivered successfully.'];
                $icons = ['bi-cart-check', 'bi-gear', 'bi-truck', 'bi-box-seam', 'bi-check2-circle'];

                foreach ($labels as $i => $label):
            ?>
            <div class="col d-flex flex-column align-items-center position-relative">
                <?php if ($i !== 0): ?>
                    <div class="position-absolute top-50 start-0 translate-middle-y w-100" style="height: 4px; z-index: 0;">
                        <div style="height: 100%; width: 100%; background-color: <?= $i <= $current_step ? $active_color : $inactive_color ?>;"></div>
                    </div>
                <?php endif; ?>
                <div class="rounded-circle d-flex justify-content-center align-items-center mb-2" style="z-index: 1; width: 45px; height: 45px; font-size: 20px; background-color: <?= $i <= $current_step ? $active_color : $inactive_color ?>; color: white;">
                    <i class="bi <?= $icons[$i] ?>"></i>
                </div>
                <div class="fw-semibold <?= $i <= $current_step ? 'text-success' : 'text-muted' ?>"><?= htmlspecialchars($label) ?></div>
                <small class="text-muted"><?= htmlspecialchars($subtexts[$i]) ?></small>
            </div>
            <?php endforeach; 
            }
            ?>
        </div>
    </div>

    <!-- Order Info -->
    <div class="card p-4 mb-4">
        <div class="row mb-2"><div class="col-md-6"><strong>Total Price:</strong></div><div class="col-md-6">₹<?= number_format($order['total_price'], 2); ?></div></div>
        <div class="row mb-2"><div class="col-md-6"><strong>Payment Method:</strong></div><div class="col-md-6"><?= htmlspecialchars(ucfirst($order['payment_method'])); ?></div></div>
        <div class="row mb-2"><div class="col-md-6"><strong>Payment Status:</strong></div>
            <div class="col-md-6">
                <span class="badge <?= $final_payment_status === 'Paid' ? 'bg-success' : 'bg-warning text-dark' ?> badge-status">
                    <?= htmlspecialchars($final_payment_status); ?>
                </span>
            </div>
        </div>
        <div class="row mb-2"><div class="col-md-6"><strong>Order Status:</strong></div>
            <div class="col-md-6">
                <span class="badge bg-<?= $status_badge ?> text-uppercase badge-status"><?= htmlspecialchars(ucfirst($status)); ?></span>
            </div>
        </div>
        <div class="row mb-2"><div class="col-md-6"><strong>Tracking ID:</strong></div><div class="col-md-6"><?= !empty($order['tracking_id']) ? htmlspecialchars($order['tracking_id']) : '<span class="text-muted">Not Available</span>'; ?></div></div>
        <div class="row mb-2"><div class="col-md-6"><strong>Ordered On:</strong></div><div class="col-md-6"><?= date('d M Y, h:i A', strtotime($order['created_at'])); ?></div></div>
    </div>

    <!-- Cancel Order Button & Form -->
    <?php if ($status === 'pending' || $status === 'processing'): ?>
        <button class="btn btn-danger mb-2" onclick="document.getElementById('cancelForm').classList.toggle('d-none')">Cancel Order</button>
        <form method="POST" action="cancel_order.php" class="d-none" id="cancelForm">
            <input type="hidden" name="order_id" value="<?= htmlspecialchars($order_id); ?>">
            <div class="mb-2">
                <label for="cancel_reason" class="form-label">Reason for Cancellation:</label>
                <textarea name="cancel_reason" class="form-control" required></textarea>
            </div>
            <button type="submit" class="btn btn-outline-danger">Submit Cancellation</button>
        </form>
    <?php endif; ?>

    <!-- Refund Request Button & Form -->
    <?php if ($status === 'delivered'): ?>
        <?php if ($refund_requested && $refund_status !== 'rejected'): ?>
            <div class="alert alert-info mb-2">
                You have already requested a refund.<br>
                Status: <strong><?= htmlspecialchars(ucfirst($refund_status)); ?></strong>
            </div>
        <?php else: ?>
            <button class="btn btn-warning mb-2" onclick="document.getElementById('refundForm').classList.toggle('d-none')">Request Refund</button>
            <form method="POST" action="request_refund.php" class="d-none" id="refundForm">
                <input type="hidden" name="order_id" value="<?= htmlspecialchars($order_id); ?>">
                <div class="mb-2">
                    <label for="refund_reason" class="form-label">Reason for Refund:</label>
                    <textarea name="refund_reason" class="form-control" required></textarea>
                </div>
                <button type="submit" class="btn btn-outline-warning">Submit Refund Request</button>
            </form>
        <?php endif; ?>
    <?php endif; ?>

    <a href="my_orders.php" class="btn btn-secondary mt-3">Back to Orders</a>
</div>
</body>
</html>
