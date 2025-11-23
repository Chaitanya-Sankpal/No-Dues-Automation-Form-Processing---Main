<?php
include('db_connection.php');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect if not logged in
if (!isset($_SESSION['login_as'])) {
    header('location:index.php');
    exit();
}

// Ensure encrypt function exists
if (!function_exists('encrypt')) {
    function encrypt($data, $key) {
        return hash('sha256', $data . $key); // Replace with actual encryption logic
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Validate form fields
    if (empty($_POST['password']) || empty($_POST['new-password']) || empty($_POST['confirm-password'])) {
        header('location:settings.php?error=empty');
        exit();
    }

    // Sanitize inputs
    $current_pass = mysqli_real_escape_string($connection, trim($_POST['password']));
    $new_pass = mysqli_real_escape_string($connection, trim($_POST['new-password']));
    $confirm_pass = mysqli_real_escape_string($connection, trim($_POST['confirm-password']));

    // Encrypt current password for validation
    $encrypted_current_pass = encrypt($current_pass, ENCRYPTION_KEY);

    // Verify current password
    if ($encrypted_current_pass !== $_SESSION['password']) {
        header('location:settings.php?error=cur_pass');
        exit();
    }

    // Check if new passwords match
    if ($new_pass !== $confirm_pass) {
        header('location:settings.php?error=pass_match');
        exit();
    }

    // Encrypt the new password
    $encrypted_new_pass = encrypt($new_pass, ENCRYPTION_KEY);

    // Update password in the database
    $query = "UPDATE student SET password='$encrypted_new_pass' WHERE roll='" . $_SESSION['roll'] . "'";
    $result = mysqli_query($connection, $query);

    if ($result) {
        // Update session password
        $_SESSION['password'] = $encrypted_new_pass;
        header('location:settings.php?success=Password updated successfully');
        exit();
    } else {
        header('location:settings.php?error=connection');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
</head>
<body>

    <h2>Change Password</h2>

    <!-- Show error/success messages -->
    <?php if (isset($_GET['error'])): ?>
        <p style="color: red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
        <p style="color: green;"><?php echo htmlspecialchars($_GET['success']); ?></p>
    <?php endif; ?>

    <form action="settings.php" method="POST">
        <label>Current Password:</label>
        <input type="password" name="password" required><br>

        <label>New Password:</label>
        <input type="password" name="new-password" required><br>

        <label>Confirm New Password:</label>
        <input type="password" name="confirm-password" required><br>

        <button type="submit">Change Password</button>
    </form>

</body>
</html>
