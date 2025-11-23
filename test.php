<?php 
  // Start the session at the very beginning
  session_start();

  // Redirect if the user is already logged in as a student
  if(isset($_SESSION['login_as']) && strcmp($_SESSION['login_as'], "student") == 0){
    header('location:student-profile.php');
    exit();  // Always call exit() after header redirection
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8"> <!-- Ensure proper character encoding -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="img/icon.png" type="image/png">
    <title>Dues Management Portal</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom styles for this template -->
    <link href="css/signin.css" rel="stylesheet">

    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>

    <style>
      /* Global styles */
      body {
        margin: 0;
        padding: 0;
        font-family: 'Arial', sans-serif;
        background: #000;
        background-size: cover;
        background-position: center center;
        animation: backgroundSlider 15s infinite;
      }

      /* Animation for the background image banner */
      @keyframes backgroundSlider {
        0% {
          background-image: url('img/banner1.jpg');
        }
        25% {
          background-image: url('img/banner2.jpg');
        }
        50% {
          background-image: url('img/banner3.jpg');
        }
        75% {
          background-image: url('img/banner4.jpg');
        }
        100% {
          background-image: url('img/banner1.jpg');
        }
      }

      .container {
        max-width: 400px;
        margin: 0 auto;
        padding: 40px;
        background: rgba(255, 255, 255, 0.8);
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      }

      h2 {
        text-align: center;
        color: #333;
        font-size: 32px;
        margin-bottom: 30px;
      }

      .form-signin {
        margin-top: 20px;
      }

      .form-signin-heading {
        font-size: 24px;
        color: #333;
        margin-bottom: 20px;
      }

      .form-control {
        margin-bottom: 15px;
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
      }

      .btn-primary {
        background-color: #5cb85c;
        border: none;
        border-radius: 5px;
        padding: 12px;
        font-size: 18px;
        width: 100%;
      }

      .btn-primary:hover {
        background-color: #4cae4c;
        transition: background-color 0.3s ease;
      }

      .alert {
        padding: 10px;
        margin-bottom: 20px;
        border-radius: 5px;
      }

      .alert-danger {
        background-color: #f2dede;
        color: #a94442;
      }

      .alert-success {
        background-color: #dff0d8;
        color: #3c763d;
      }

      .alert .glyphicon {
        margin-right: 10px;
      }

      .link-container {
        text-align: center;
        margin-top: 20px;
      }

      .link-container a {
        color: #0275d8;
        font-size: 14px;
        text-decoration: none;
      }

      .link-container a:hover {
        text-decoration: underline;
      }

      /* Logo Styling */
      .logo {
        display: block;
        margin: 0 auto 20px;
        width: 100px; /* Adjust the logo size */
        height: auto;
      }
    </style>

  </head>

  <body>

    <!-- College Logo Above Signin -->
    <div align="center">
      <img src="img/college-logo.png" alt="College Logo" class="logo"> <!-- Replace with actual logo path -->
      <h2>Welcome to Dues Management Portal</h2>
    </div>

    <!-- Signin Form -->
    <div class="container">
      <form class="form-signin" action="login.php" method="post">
        <div align="center">
          <h3 class="form-signin-heading">Please sign in</h3>
        </div>
        <label for="inputRoll" class="sr-only">Roll number</label>
        <input type="number" step="any" name="roll" id="inputRoll" class="form-control" placeholder="Roll number" required autofocus>
        
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Password" required>

        <!-- Error Message Box -->
        <div id="error-div" class="alert alert-danger" role="alert" style="display:none;">
          <span class="glyphicon glyphicon-exclamation-sign" id="error-glyphicon" aria-hidden="true"></span>
          <span id="error-span">Error - Invalid Roll/Password</span>
        </div>

        <button class="btn btn-lg btn-primary btn-block" type="submit" name="submit">Sign in</button>
      </form>
      
      <div class="link-container">
        <h5><a href="admin/index.php">Admin or Manager Login?</a></h5>
        <h5><a href="forgot-password.php">Forgot Password?</a></h5>
      </div>
    </div> <!-- End of Signin Form -->

    <?php
      // Handling error messages based on URL parameters
      if(isset($_GET['error'])){
        $error_type = htmlspecialchars($_GET['error']);  // Sanitize error string to avoid XSS attacks
        if(strcasecmp($error_type, "credential") == 0){
          echo "<script>
                document.getElementById('error-span').innerHTML = 'Error - Invalid Roll/Password';
                document.getElementById('error-div').style.display = 'block';
                </script>";
        }
        else if(strcasecmp($error_type, "noneReset") == 0){
          echo "<script>
                document.getElementById('error-span').innerHTML = 'Reset Successful.';
                document.getElementById('error-div').className = 'alert alert-success';
                document.getElementById('error-glyphicon').className = 'glyphicon glyphicon-ok';
                document.getElementById('error-div').style.display = 'block';
                </script>";
        }
        else if(strcasecmp($error_type, "matchReset") == 0){
          echo "<script>
                document.getElementById('error-span').innerHTML = 'Password did not match. Apply for a reset again.';
                document.getElementById('error-div').style.display = 'block';
                </script>";
        }
      }
    ?>

  </body>
</html>
