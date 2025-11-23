<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection credentials
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'dbms2';

// Attempt to connect to the database
$connection = mysqli_connect($host, $username, $password, $database);

// Check if connection was successful
if (!$connection) {
    die("Connection Failed: " . mysqli_connect_error());
} else {
    echo "Database connection successful!";
}

// Close the connection after testing
mysqli_close($connection);
?>
