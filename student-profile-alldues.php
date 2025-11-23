<?php
    // Enable error reporting for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    include('db_connection.php');

    // Start session if not started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Redirect if not logged in
    if (!isset($_SESSION['login_as'])) {
        header('location:index.php');
        exit();
    }

    // Check if GRN is set
    if (!isset($_SESSION['grn'])) {
        die("Error: GRN is not set in session.");
    }

    $grn = $_SESSION['grn'];

    // Initialize dues
    $mess_due = $hostel_due = $other_due = 0;

    // Fetch Mess Due
    $query = mysqli_query($connection, "SELECT due_amount FROM mess_due WHERE grn='$grn'");
    if ($query) {
        $row = mysqli_fetch_assoc($query);
        $mess_due = $row['due_amount'] ?? 0;
    } else {
        die("Query Failed (Mess Due): " . mysqli_error($connection));
    }

    // Fetch Hostel Due
    $query = mysqli_query($connection, "SELECT due_amount FROM hostel_due WHERE grn='$grn'");
    if ($query) {
        $row = mysqli_fetch_assoc($query);
        $hostel_due = $row['due_amount'] ?? 0;
    } else {
        die("Query Failed (Hostel Due): " . mysqli_error($connection));
    }

    // Fetch Other Dues
    $query = mysqli_query($connection, "SELECT due_amount FROM other_due WHERE grn='$grn'");
    if ($query) {
        $row = mysqli_fetch_assoc($query);
        $other_due = $row['due_amount'] ?? 0;
    } else {
        die("Query Failed (Other Due): " . mysqli_error($connection));
    }

    // Calculate Total Due
    $total_due = $mess_due + $hostel_due + $other_due;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $_SESSION['name']; ?> - All Dues</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/custom-style.css">
</head>
<body>
    <!-- Navigation Tabs -->
   <div class="container mt-5 pt-4">
        <ul class="nav nav-tabs">
            <li class="nav-item"><a class="nav-link active" href="#">Mess Due</a></li>
            <li class="nav-item"><a class="nav-link" href="student-profile-hostel.php">Hostel Due</a></li>
            <li class="nav-item"><a class="nav-link" href="student-profile-other.php">Other Due</a></li>
            <li class="nav-item"><a class="nav-link" href="student-profile-alldues.php">All Dues</a></li> <!-- NEW LINK -->
        </ul>
    </div>
    <!-- Due Details Table -->
    <div class="container mt-4">
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white text-center">
                <h4>All Dues Summary</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered text-center">
                        <thead class="thead-dark">
                            <tr>
                                <th>GRN</th>
                                <th>Mess Due</th>
                                <th>Hostel Due</th>
                                <th>Other Due</th>
                                <th>Total Due</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo htmlspecialchars($grn); ?></td>
                                <td><?php echo 'Rs. ' . number_format($mess_due, 2); ?></td>
                                <td><?php echo 'Rs. ' . number_format($hostel_due, 2); ?></td>
                                <td><?php echo 'Rs. ' . number_format($other_due, 2); ?></td>
                                <td><strong><?php echo 'Rs. ' . number_format($total_due, 2); ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Download Form Button -->
                <div class="text-center mt-4">
                    <button id="downloadBtn" class="btn btn-success" <?php echo ($total_due == 0 ? '' : 'disabled'); ?>>
                        Download Clearance Form
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('downloadBtn').addEventListener('click', function() {
            window.location.href = 'download_form.php';
        });
    </script>
</body>
</html>
