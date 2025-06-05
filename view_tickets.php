<?php
include 'db.php';
$result = mysqli_query($conn, "SELECT * FROM support_tickets");

while ($row = mysqli_fetch_assoc($result)) {
    echo "<p><b>Subject:</b> " . $row['subject'] . "</p>";
    echo "<p><b>Message:</b> " . $row['message'] . "</p>";
    echo "<p><b>Status:</b> " . $row['status'] . "</p>";
}
?>