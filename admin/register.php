<?php 

//session timeout logs out the user automatically after a pperiod of inactivity
require_once('../includes/session_timeout.inc.php');


if (isset($_POST['register']))			//do this only if form is submitted
{
	$username = trim($_POST['username']);		//get from user input and remove spaces using trim()
	$password = trim($_POST['pwd']);
	$retyped = trim($_POST['retyped']);
	
	//call code that checks username and password validity
	require_once('../includes/register_2way_mysqli.inc.php');	
}
		
?>

<!DOCTYPE html>
<html lang="en-US" style="height: 100%">

<head>
<title>ADMIN - REGISTER

</title>
<link rel="stylesheet" type="text/css" href="admin.css" />
</head>

<body>
 
<!------------------------- USERNAME and LOGOUT ------------------------------->
                                                 
                                                      
<div style="float:right"><a href="./adminpage.php">Back to MAIN ADMIN PAGE</a></div>
<p style="float:left;">

<span style="font: bold 14px sans-serif; text-shadow:2px 2px #ff80ff;">
<?php

    echo "You are logged in, " . $_SESSION['username'];		//display username
	
    include('../includes/logout_db.inc.php');		//logout button
          
?>
</span>

</p>

<!-------------------------------------------------------------------------------->


	<div style="border:5px pink ridge;
								margin:0px auto; 
								padding:10px;  
								width:200px;
								text-align:center">
	
			<h1>Register User</h1>

		<form name="reg" action="" method="post" style="margin:0px auto;">

			<p>
				<label for="uname">Username: </label>
				<input type="text" name="username" id="username" >
			</p>
			<p>
				<label for="uname">Password: </label>
				<input type="password" name="pwd" id="pwd">
			</p>
			<p>
				<label for="uname">Retype Password: </label>
				<input type="password" name="retyped" id="retyped" >
			</p>
			<p	style="margin:20px 45px;">
			<input type="submit" name="register" id="register" value="Register">
			</p>
	
		</form>   
		
	</div>
    
<div>

	<?php
	if (isset($result) || isset($errors))  // display result and/or errors
		{
			echo '<ul>';
			
			if (!empty($errors))
			{foreach ($errors as $item)
				{echo "<li class=\"cas\">$item</li>";}
			}
			else
			{echo "<li class=\"cas\">$success</li>";}
			echo '</ul>';
		}
	?>
	
</div>


</body>
</html>