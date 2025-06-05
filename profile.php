<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

$check_query = "SELECT status FROM users WHERE id = '$user_id'";
$check_result = mysqli_query($conn, $check_query);
$user_status = mysqli_fetch_assoc($check_result);

if ($user_status && $user_status['status'] === 'banned') {
    session_unset();
    session_destroy();
    header("Location: login.php?error=Your account has been banned.");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    $update_query = "UPDATE users SET name='$name', email='$email', phone='$phone' WHERE id='$user_id'";

    if (mysqli_query($conn, $update_query)) {
        $_SESSION['success'] = "Profile Updated Successfully!";
        header("Location: profile.php");
        exit();
    } else {
        $_SESSION['error'] = "Something went wrong!";
    }
}

$tickets_query = "SELECT * FROM support_tickets WHERE user_id = '$user_id' ORDER BY created_at DESC";
$tickets_result = mysqli_query($conn, $tickets_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile - SnapBazaar</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f1f3f6; font-family: 'Segoe UI', sans-serif; }
        .sidebar {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            height: 100%;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .sidebar a {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            margin-bottom: 10px;
            border-radius: 6px;
            text-decoration: none;
            color: #333;
            transition: all 0.2s;
        }
        .sidebar a i {
            margin-right: 10px;
            font-size: 18px;
        }
        .sidebar a:hover {
            background-color: #f0f0f0;
        }
        .content-area {
            background-color: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .table td, .table th {
            vertical-align: middle;
        }
        .navbar {
            background-color: #ffffff;
            padding: 10px 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 22px;
            color: #0d6efd;
        }
    </style>
</head>
<body>

<!-- 🔹 Top Navbar -->
<nav class="navbar mb-4">
    <a class="navbar-brand" href="home.php">
        SnapBazaar
    </a>
</nav>

<!-- 🔹 Main Container -->
<div class="container my-4">
    <div class="row">
        <!-- 🔹 Sidebar -->
        <div class="col-md-3">
            <div class="sidebar">
                <h5 class="mb-4">👤 Hello, <?= htmlspecialchars($user['name']); ?></h5>
                <a href="profile.php"><i class="bi bi-person-circle"></i> My Profile</a>
                <a href="my_orders.php"><i class="bi bi-box-seam"></i> My Orders</a>
                <a href="my_address.php"><i class="bi bi-geo-alt"></i> My Address</a>
                <a href="my_wishlist.php"><i class="bi bi-heart"></i> My Wishlist</a>
                <a href="support_tickets.php"><i class="bi bi-life-preserver"></i> Support Tickets</a>
                <a href="live_chat.php"><i class="bi bi-chat-dots"></i> Live Chat</a>
                <a href="logout.php" class="text-danger" onclick="return confirmLogout();"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </div>

        <!-- 🔹 Content Area -->
        <div class="col-md-9">
            <div class="content-area">
                
                <!-- 🔸 Wallet & Referral Earnings -->
                <div class="alert alert-success d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">👛 Wallet Balance</h5>
                        <small>Total Referral Earning: ₹<?= number_format($user['referral_earned'], 2); ?></small>
                    </div>
                    <h4 class="mb-0 text-primary">₹<?= number_format($user['wallet_balance'], 2); ?></h4>
                </div>

                <!-- 🔸 Update Profile Form -->
                <h4 class="mb-4">📝 Update Profile</h4>

                <?php if (isset($_SESSION['success'])) { ?>
                    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php } ?>
                <?php if (isset($_SESSION['error'])) { ?>
                    <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php } ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']); ?>" required>
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-primary w-100">Update Profile</button>
                </form>

                <!-- 🔸 Support Tickets -->
                <h5 class="mt-5">📩 My Support Tickets</h5>
                <div class="table-responsive">
                    <table class="table table-bordered mt-3">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($ticket = mysqli_fetch_assoc($tickets_result)) { ?>
                                <tr>
                                    <td><?= $ticket['id']; ?></td>
                                    <td><?= htmlspecialchars($ticket['subject']); ?></td>
                                    <td>
                                        <span class="badge bg-<?= ($ticket['status'] == 'Resolved') ? 'success' : 'warning'; ?>">
                                            <?= htmlspecialchars($ticket['status']); ?>
                                        </span>
                                    </td>
                                    <td><?= date('d M Y, H:i', strtotime($ticket['created_at'])); ?></td>
                                    <td>
                                        <a href="view_ticket.php?id=<?= $ticket['id']; ?>" class="btn btn-sm btn-outline-primary">View</a>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php if (mysqli_num_rows($tickets_result) == 0) { ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No tickets found.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

               <!-- 🔸 Refer & Earn -->
<div class="mt-5 p-4 bg-light rounded">
    <h5 class="mb-3">🎁 Refer & Earn</h5>
    <p>Your Referral Code: <strong><?= htmlspecialchars($user['referral_code']); ?></strong></p>
    <?php $referral_link = "http://localhost/snapbazaar/register.php?ref=" . urlencode($user['referral_code']); ?>
    <div class="input-group mb-2">
        <input type="text" class="form-control" id="refLink" value="<?= $referral_link ?>" readonly>
        <button class="btn btn-outline-primary" onclick="copyReferral()">Copy Link</button>
    </div>
    <small class="text-muted">Refer your friends and earn wallet cash when they register and place their first order.</small>
</div>


            </div>
        </div>
    </div>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function confirmLogout() {
    return confirm("Are you sure you want to logout?");
}
function copyReferral() {
    const copyText = document.getElementById("refLink");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");
    alert("Referral link copied to clipboard!");
}
</script>

</body>
</html>
