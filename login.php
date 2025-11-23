<?php
// Include database connection file
include('db_connection.php');

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    
    // Get the GRN and password from form input
    $grn = trim($_POST['grn']);
    $password = trim($_POST['password']);
    
    // Check if the database connection is valid
    if (!$connection) {
        die("Database connection failed: " . mysqli_connect_error());
    }
    
    // Query to check if the GRN exists in the database
    $sql = "SELECT grn, password, name, hostel FROM student WHERE grn = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("s", $grn);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $stored_password = $row['password']; // Plain text password from DB

        // Compare entered password with stored password (No Hashing)
        if ($password === $stored_password) {
            // Set session variables
            $_SESSION['login_as'] = "student";
            $_SESSION['grn'] = $row['grn'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['hostel'] = $row['hostel'];

            // Redirect to student profile page
            echo "<script>
                    alert('Login Successful!');
                    window.location.href = 'student-profile.php';
                  </script>";
            exit();
        } else {
            // Incorrect password
            echo "<script>
                    alert('Error - Invalid GRN/Password');
                    window.location.href = 'index.php';
                  </script>";
            exit();
        }
    } else {
        // GRN not found
        echo "<script>
                alert('Error - Invalid GRN/Password');
                window.location.href = 'index.php';
              </script>";
        exit();
    }
    
    // Close the statement and connection
    $stmt->close();
    $connection->close();
} else {
    // Redirect back to the login page if the form was not submitted
    header('Location: index.php');
    exit();
}
?>
