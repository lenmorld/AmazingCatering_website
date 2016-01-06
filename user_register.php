<?php


// check if ran locally (testing) or online ################################ 

$mystring = $_SERVER['HTTP_HOST'];          // $_SERVER['HTTP_HOST'] holds the hostname,
                                                // if tested locally, it is ->  http://localhost/ 
                                                // if online, it is ->  http://amazingcoveragecatering.hostzi.com/  
                                                
$posLocal = strpos($mystring, 'localhost');       // returns true if 'localhost' is find in hostname        
$posOnline =  strpos($mystring, 'hostzi');        // returns true if 'hostzi' is find in hostname

if ($posLocal !== false) {
    //string 'localhost is find in address- offline'
    $online = false;  
} 
else if ($posOnline !== false) {
    //string 'hostzi' - online host is find in address- online'
    $online = true; 
}


if ($online)    //if website is executed for real online, run captcha confirmation, and hide possible errors
{
require_once('./includes/recaptchalib.php'); 
$public_key = '6Lf_DdQSAAAAAKgRpEc0lWuvBebmUH3LREUj9qR-';
$private_key = '6Lf_DdQSAAAAAMxi_Mu8elnTH8PTX4UYvNCl1A-o'; 

// prevents display of errors
ini_set('display_errors', '0');      
} 


if (isset($_POST['register']))			//do this only if form is submitted
{
	$username = trim($_POST['username']);		//get from user input and remove spaces using trim()
	$password = trim($_POST['pwd']);
	$retyped = trim($_POST['retyped']);
    
    
    $fname =  $_POST['fname'];  
    $contactno =  $_POST['contactno'];  
    $email =  $_POST['email'];
    
    
    
    //reCaptcha ##############################################################
    
    if ($online)  // run reCaptcha only if online
    {
        $response = recaptcha_check_answer($private_key, $_SERVER['REMOTE_ADDR'], 
          $_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field']); 
        if (!$response->is_valid) { 
          $errors['recaptcha'] = true; 
        }            
    }

	//call code that checks username and password validity
	require_once('./includes/user_register_2way_mysqli.inc.php');	
}

		
?>

<!DOCTYPE html>
<html lang="en-US" style="height: 100%">
<head>
<title>Member Registration

</title>
<!--link rel="stylesheet" type="text/css" href="admin.css" /-->  

 <style type="text/css">
 
 body {
     
     background-color: thistle;
 }
 
 h1 {color:hotpink;
        font: bold 20px sans-serif;}
        
ul {color:#800080;
        font: bold 14px sans-serif;}
        
label {
        font: bold 14px sans-serif;
        }
	
a
{color:purple;text-decoration:none}
a:hover{color:hotpink;text-decoration:underline}
 
 img{opacity:0.7; }
 
 img:hover{opacity:1.0;}
 
 
 </style>


</head>

<body>


<div style="margin-top:10px;">

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
	
	<div style="border:5px pink ridge;
                        margin:0px auto; 
                        padding:10px;  
                        width:500px;
                        text-align:center">
	
			<h1>Register User</h1>

		<form name="reg" action="" method="post" style="margin:0px auto;">
            <center>
            <table cellpadding="2px" cellspacing="5px">
            <tr>
				<td><label for="uname">Username: </label> </td>
				<td><input type="text" name="username" id="username" > </td>
            </tr>
            
            <tr>
                <td><label for="fname">Full Name: </label></td>
                <td><input type="text" name="fname" id="fname" ></td>
            </tr>
            
            <tr>
				<td><label for="uname">Password: </label></td>
				<td><input type="password" name="pwd" id="pwd"></td>
            </tr>
			<tr>
				<td><label for="uname">Retype Password: </label></td>
				<td><input type="password" name="retyped" id="retyped" ></td>
			</tr>
            
            <!---------------------------------------------------->
            

            
            <tr>
                <td><label for="contactno">Contact No: </label></td>
                <td><input type="tel" name="contactno" id="contactno" ></td>
            </tr>
            
            <tr>
                <td><label for="email">Email: </label></td>
                <td><input type="text" name="email" id="email" ></td>
            </tr>
            
            </table>
            
            
          <?php if ($online)        //execute reCaptcha only if online
          { ?>                      
                <div style="width:300px; margin: 10px auto; text-align: center;">
                    <hr />
                    <div style="font:bold 14px Arial,sans-serif;">CAPTCHA confirmation
                    </div>
                          <!--recaptcha --------->
                            <?php if (isset($errors['recaptcha']))
                                { ?>    
                                    <p class="warning">The values didn't match. Try again</p>
                            <?php } 
                            echo recaptcha_get_html($public_key);   ?>
                            <!--------------------------->
                    <hr /> 
                </div>
              <?php 
          } ?>
            
            
            
           <p    style="margin:20px 45px;">
            <input type="submit" name="register" id="register" value="Register">
            </p>
            
            
            </center>
            
            <!----------------------------------------------------->
            

	
		</form>   
		
	</div>
    
<center>
<a href="./user_login.php">
<img src="./images/login.png" height="100px">
<br>
<span style="font: 14px bold sans-serif">LOG-IN</span>
</a>

<div style="margin:20px; text-align: center;">
<span style="font: bold 14px sans-serif"><a href="./index.php">
<img src="./images/home2.png" height="100px"</a></span>

</div>

</center>  


</body>
</html>