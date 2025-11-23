<?php
    include('db_connection.php');
    
    // Start session if not started
    if (session_id() == "") {
        session_start();
    }

    // Redirect if not logged in
    if (!isset($_SESSION['login_as'])) {
        header('location:index.php');
        exit();
    }

    $due = 0;
    
    // Fetch mess due details
    $query = mysqli_query($connection, "SELECT * FROM mess_due WHERE grn='" . $_SESSION['grn'] . "'");
    
    if ($query) {
        if (mysqli_num_rows($query) == 1) {
            $row = mysqli_fetch_assoc($query);
            $due = 1;
        }
    } else {
        die("Error executing query: " . mysqli_error($connection));
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $_SESSION['name']; ?> - Profile</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/custom-style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-dark bg-dark navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">Home</a>
            <a class="navbar-brand" href="setting.php">Settings</a>
            <span class="navbar-text">Welcome, <?php echo $_SESSION['name']; ?></span>
            <a class="btn btn-danger" href="logout.php">Log Out</a>
        </div>
    </nav>

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
                <h4>Mess Due Details</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered text-center">
                        <thead class="thead-dark">
                            <tr>
                                <th>GRN</th>
                                <th>Due Amount</th>
                                <th>Added On</th>
                                <th>Added By</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo $_SESSION['grn']; ?></td>
                                <td><?php echo ($due == 1) ? 'Rs. ' . $row['due_amount'] : 'Rs. 0'; ?></td>
                                <td><?php echo ($due == 1) ? $row['added_on'] : 'NA'; ?></td>
                                <td><?php echo ($due == 1) ? $row['added_by'] : 'NA'; ?></td>
                                <td><?php echo ($due == 1) ? $row['reason'] : 'NA'; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
