<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $delete = $conn->query("DELETE FROM users WHERE id = $id");

    if ($delete) {
        header("Location: manage_users.php?msg=User+deleted+successfully");
    } else {
        header("Location: manage_users.php?error=Failed+to+delete+user");
    }
    exit();
} else {
    header("Location: manage_users.php?error=Invalid+request");
    exit();
}
