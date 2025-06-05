<?php
session_start();
include 'db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Update Ticket Response
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ticket_id'])) {
    $ticket_id = $_POST['ticket_id'];
    $admin_reply = mysqli_real_escape_string($conn, $_POST['admin_reply']);
    $status = $_POST['status'];

    // Ensure the user_id exists before inserting the reply
    $user_id = $_SESSION['user_id'];  // Ensure the user is logged in

    // Check if user exists in the users table
    $user_check_query = "SELECT id FROM users WHERE id = '$user_id'";
    $user_check_result = mysqli_query($conn, $user_check_query);

    if (mysqli_num_rows($user_check_result) > 0) {
        // User exists, proceed to insert reply
        $query = "INSERT INTO support_replies (ticket_id, user_id, reply_message, created_at) 
                  VALUES ('$ticket_id', '$user_id', '$admin_reply', NOW())";
        mysqli_query($conn, $query);

        // Update ticket status
        $update_ticket_query = "UPDATE support_tickets SET status='$status', updated_at=NOW() WHERE id='$ticket_id'";
        mysqli_query($conn, $update_ticket_query);
    } else {
        echo "Invalid user ID!";
    }
}

// Fetch Tickets
$query = "SELECT * FROM support_tickets ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Support Tickets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>📩 Support Tickets (Admin)</h2>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Subject</th>
                <th>Priority</th>
                <th>Message</th>
                <th>File</th>
                <th>Status</th>
                <th>Admin Reply</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['user_id'] ?></td>
                    <td><?= $row['subject'] ?></td>
                    <td><?= $row['priority'] ?></td>
                    <td><?= $row['message'] ?></td>
                    <td>
                        <?php if ($row['file_path']) { ?>
                            <a href="<?= $row['file_path'] ?>" target="_blank">View File</a>
                        <?php } else { echo "No File"; } ?>
                    </td>
                    <td><?= $row['status'] ?></td>
                    <td><?= $row['admin_reply'] ?? "No Reply Yet" ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="ticket_id" value="<?= $row['id'] ?>">
                            <textarea name="admin_reply" class="form-control" placeholder="Write a reply..." required></textarea>
                            <select name="status" class="form-control mt-2">
                                <option value="Pending">Pending</option>
                                <option value="Resolved">Resolved</option>
                                <option value="Rejected">Rejected</option>
                            </select>
                            <button type="submit" class="btn btn-primary mt-2">Reply</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>