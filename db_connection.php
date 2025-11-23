<?php
// Use Google App Engine's mail API
use \google\appengine\api\mail\Message;

// Establish MySQL connection using localhost (consider using environment variables for production)
$connection = mysqli_connect('localhost', 'root', '', 'dbms2');

// Check connection
if (!$connection) {
    die("Connection Failed: " . mysqli_connect_error());
}

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect based on session login status
if (isset($_SESSION['login_as'])) {
    if ($_SESSION['login_as'] === "admin") {
        header('location:admin/admin-profile.php');
        exit();
    } elseif ($_SESSION['login_as'] === "manager") {
        header('location:admin/manager-profile.php');
        exit();
    }
}

// Define a secure encryption key (change this to an environment variable for production)
define("ENCRYPTION_KEY", "!@#$%^&*");  // Replace this with a secure key for production

// Secure encryption function using OpenSSL (AES-128-CTR encryption)
function encrypt($pure_string, $encryption_key) {
    // Ensure the IV is fixed and 16 bytes
    $iv = '1234567891011121';
    return openssl_encrypt($pure_string, 'AES-128-CTR', $encryption_key, 0, $iv);
}

function decrypt($encrypted_string, $encryption_key) {
    // Ensure the IV is fixed and 16 bytes
    $iv = '1234567891011121';
    return openssl_decrypt($encrypted_string, 'AES-128-CTR', $encryption_key, 0, $iv);
}

// Function to generate a random string (if needed for password reset or similar purposes)
function generateRandomString($length = 10) {
    return substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
}

// Function to send a reset password email using Google App Engine's mail API
function sendMail($email, $url) {
    try {
        $message = new Message();
        $message->setSender("no-dues@appspot.gserviceaccount.com");
        $message->addTo($email);
        $message->setSubject("Forgot Password");
        $message->setTextBody('Please visit the following link to reset your password: ' . $url);
        $message->send();
    } catch (InvalidArgumentException $e) {
        // Log error if mail sending fails
        error_log("Mail sending failed: " . $e->getMessage());
    }
}
?>
