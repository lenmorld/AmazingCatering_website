<?php 
        $error = ''; 		//initialize error variable
        if (isset($_POST['login'])) 	//proceed only if log-in is clicked
		{ 
			session_start(); 		//start session

			$username = trim($_POST['username']); 	//get username, trim spaces
			
			$_SESSION['username'] = $username;		//assign username to global session variable
													//this will be used later to get username from other 
													//session-enabled pages
													
			$password = trim($_POST['pwd']);		//get password, trim spaces
				
			$redirect = './adminpage.php';			//redirect page after successful log-in
			  
			 //call code that processes user verification 
			require_once('../includes/authenticate_2way_mysqli.inc.php'); 		
        }

?>

<!DOCTYPE html>
<html lang="en-US" style="height: 100%">
<head>
<title>ADMIN - LOG IN
</title>
<link rel="stylesheet" type="text/css" href="admin.css" />
</head>

<body>

<div style="height:500px;">

	<?php
	if ($error)		//display errors if set
		{echo "<p class=\"warning\">$error</p>";}
	elseif (isset($_GET['expired']))		//expired session
		{  ?>	
		<p class="warning">Your session has expired due to inactivity. Please log in again.</p>
	<?php } ?>
	
	<div style="border:5px pink ridge;margin:20px;padding:20px; background-color:mistyrose;">
	
	<img src="../images/login.png" height="50px" style="float:left">
        <h1 >Log-In : Admin</h1>

	<h4>***Only authorized administrators are allowed to this page</h4>


	<form name="upload_login" action="" method="post">
		<fieldset id="login">
			<table cellspacing="20">
			<tr>
			<td>
				<label for="uname">Username</label>
				<input type="text" name="username" id="uname">
			</td>
			<td>
				<label for="pwd">Password</label>
				<input type="password" name="pwd" id="pwd">
			</td>
			<td>
			</td>
			<td> 
				<br/>
				<input type="submit" name="login" id="login" value="Log In">
			</td>
			
			</tr>
			</table>
		</fieldset>
	</form>

	</div>
	
	
    <div style="margin:20px; text-align: center;">
    <span style="font: bold 14px sans-serif"><a href="../index.php">
    <img src="../images/home2.png" height="100px"</a></span>

    </div>


</div>


</body>
</html>