<?php 
        $error = '';         //initialize error variable
        if (isset($_POST['login']))     //proceed only if log-in is clicked
        { 
            session_start();         //start session

            $username = trim($_POST['username']);     //get username, trim spaces
            
             $_SESSION['username'] = $username;        //assign username to global session variable
                                                    //this will be used later to get username from other 
                                                    //session-enabled pages     
                                                    
            $password = trim($_POST['pwd']);        //get password, trim spaces
                
            $redirect = './memberpage.php';            //redirect page after successful log-in
              
             //call code that processes user verification 
            require_once('./includes/user_authenticate_2way_mysqli.inc.php');   
             
        }

?>

<!DOCTYPE html>
<html lang="en-US" style="height: 100%">
<head>
<title>Members - LOG IN
</title>
<!--link rel="stylesheet" type="text/css" href="admin.css" /-->

 <style type="text/css">
 
 body {
     
     background-color: thistle;
 }
 
 h1 {color:hotpink;
        font: bold 20px sans-serif;}
        
 h4 {color:purple;
        font: bold 15px sans-serif;}
        
label {
        font: bold 14px sans-serif;
        }
        
p.warning
    {
        color:red; font: bold 14px sans-serif; 
    }
    
span
    {
        color:purple; font: bold 14px sans-serif; 
    }
    
a
{
   color: #376BAD;
   text-decoration: none;
}
a:visited
{
   color: #376BAD;
}
a:active
{
   color: #C8D7EB;
}
a:hover
{
   color: #376BAD;
   text-decoration: underline;
}
 
 img{opacity:0.5; }

img:hover{opacity:1; }
 
 </style>



</head>

<body>

<div style="height:500px;">

    <?php
    if ($error)        //display errors if set
        {echo "<p class=\"warning\">$error</p>";}
    elseif (isset($_GET['expired']))        //expired session
        {  ?>    
        <p class="warning">Your session has expired due to inactivity. Please log in again.</p>
    <?php } ?>
    
    <div style="border:5px pink ridge;margin:20px;padding:20px; background-color:mistyrose; overflow:auto;">
    
    <img src="./images/login.png" height="50px" style="float:left">
        <h1 >Log-In : Member</h1>
    

    <h4>***Only members can place reservations</h4>


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
            <td valign="middle"> 
                <br/>
                <input type="submit" name="login" id="login" value="Log In"  style="margin-bottom:20px">
            </td>
            
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            
            <td >
                <span>Not a member? <br/><a href="./user_register.php">Register Now</a>   </span>
            </td>
            
            </tr>
            </table>
        </fieldset>
    </form>

    </div>
    
    
    <div style="margin:20px; text-align: center;">
    <span style="font: bold 14px sans-serif"><a href="./index.php">
    <img src="./images/home2.png" height="100px"</a></span>

    </div>


</div>


</body>
</html>