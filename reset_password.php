<?php
include("db_connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $new_password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Token check karna
    $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Password update karna
        $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = ?");
        $stmt->bind_param("ss", $new_password, $token);
        $stmt->execute();
        echo "✅ Password changed successfully!";
    } else {
        echo "❌ Invalid or expired token!";
    }
}
?>

<form method="POST">
    <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>">
    <input type="password" name="password" required placeholder="Enter new password">
    <button type="submit">Update Password</button>
</form>