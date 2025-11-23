<?php 
  session_start();

  if(isset($_SESSION['login_as']) && strcmp($_SESSION['login_as'], "student") == 0){
    header('location:student-profile.php');
    exit();
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dues Management Portal</title>
    <link rel="stylesheet" href="css/styles.css">
    <script defer src="script.js"></script>
</head>
<body>
    <div class="container">
        <div class="login-box">
            <img src="img/logo.jpeg" alt="Logo" class="logo">
            <h2>Welcome to Dues Management Portal!</h2>
            <p>Please sign in to continue.</p>
            <form class="form-signin" action="login.php" method="post">
                <div class="input-container">
                    <input type="text" name="grn" id="inputGrn" placeholder="Enter your GRN" required autofocus>
                </div>
                <div class="input-container password-container">
                    <input type="password" id="inputPassword" name="password" placeholder="Password" required>
                    <span class="toggle-password" onclick="togglePassword()">👁️</span>
                </div>
                <button type="submit" name="submit">Sign in</button>
            </form>
              <!--<button class="register">Register as Student</button>-->
            <button class="admin"><a href="admin/index.php">Admin or Manager Login?</a></button>
            <h5><a href="forgot-password.php">Forgot Password?</a></h5>
            <div id="error-div" class="alert alert-danger" role="alert" style="display:none;">
                <span class="glyphicon glyphicon-exclamation-sign" id="error-glyphicon" aria-hidden="true"></span>
                <span id="error-span">Error - Invalid GRN/Password</span>
            </div>
        </div>
    </div>
    <?php
      if(isset($_GET['error'])){
        $error_type = htmlspecialchars($_GET['error']);
        if(strcasecmp($error_type, "credential") == 0){
          echo "<script>
                document.getElementById('error-span').innerHTML = 'Error - Invalid GRN/Password';
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