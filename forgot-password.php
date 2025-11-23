<?php 
  include('db_connection.php');
  if(session_id()==""){
    session_start();
  }
  if(isset($_SESSION['login_as'])&&strcmp($_SESSION['login_as'],"student")==0){
    header('location:student-profile.php');
  }
  if(isset($_POST['submit'])){
  	$grn = mysql_real_escape_string(stripslashes($_POST['grn']));
  	$query1 = mysql_query("select * from email where grn='$grn'",$connection);
  	if(mysql_num_rows($query1)==0){
  		header('location:forgot-password.php?error=email');
  	}
  	else{
  		$keys = generateRandomString(10);
      $query = mysql_query("update `email` set `key`='$keys' where `grn`='$grn';",$connection);
  		if($query){
			$row1 = mysql_fetch_assoc($query1);
			$url = "https://no-dues.appspot.com/password-reset.php?email=".urlencode($row1['email'])."&key=".$keys;
			sendMail($row1['email'].'@iitg.ernet.in',$url);
		  	header('location:forgot-password.php?error=none');
  		}
  		else{
  			header('location:forgot-password.php?error=connection');
  		}
  	}
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="img/icon.png" type="image/png">
    <title>Dues Management Portal</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/signin.css" rel="stylesheet">
    <script src="js/bootstrap.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.js"></script>
    <script src="js/npm.js"></script> 
  </head>
  <body>
    <div align="center">
      <h2>Welcome to Dues Management Portal!</h2>
    </div>
    <br/><br/><br/><br/>
    <div align="center">
    	<h3>Forgot password? Enter your GRN.</h3>
    	<form class="form-signin" action="" method="post">
    		<input type="number" class="form-control" name="grn" placeholder="Enter your GRN" required>
    		<br>
    		<div id="error-div" class="alert alert-danger" role="alert" style="display:none;">
	          <span class="glyphicon glyphicon-exclamation-sign" id="error-glyphicon" aria-hidden="true"></span>
	          <span id="error-span">Error - Invalid GRN/Password</span>
	        </div>
    		<button class="btn btn-lg btn-primary btn-block" type="submit" name="submit">Submit</button>
    	</form>
    </div>
    <?php
      if(isset($_GET['error'])){
        if(strcasecmp($_GET['error'],"email")==0){
          echo "<script>
                document.getElementById('error-span').innerHTML = 'Invalid GRN';
                document.getElementById('error-div').style.display = 'block';
                </script>
          ";
        }
        else if(strcasecmp($_GET['error'],"connection")==0){
          echo "<script>
                document.getElementById('error-span').innerHTML = 'Connection problem';
                document.getElementById('error-div').style.display = 'block';
                </script>
          ";
        }
        else if(strcasecmp($_GET['error'],"none")==0){
        	echo "<script>
                document.getElementById('error-span').innerHTML = 'Mail sent';
                document.getElementById('error-glyphicon').className='glyphicon glyphicon-ok';
                document.getElementById('error-div').className = 'alert alert-success';
                document.getElementById('error-div').style.display = 'block';
                </script>
          ";
        }
      }
    ?>
  </body>
</html>
