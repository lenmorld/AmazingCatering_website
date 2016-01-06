<?php

//session timeout logs out the user automatically after a period of inactivity
require_once('../includes/session_timeout.inc.php');

//check if user comes from an admin page, require log-in if outside admin
require_once('../includes/check_admin.inc.php');   

?>


<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<script type="text/javascript" src="common/js/form_init.js" id="form_init_script"
data-name="">
</script>

                                                                                     
<title>Admin Page</title>

<style type="text/css">



body {
	margin:25px;
	color:#000;
	background-color: pink;
	font-family:Arial, Helvetica, sans-serif;
	font-size:85%;
}
h1 {
	font-size: 140%;
}
form {
	margin-left: 25px;
}
label {
	font-weight: bold;
	display:block;
}
.warning {
	color:#f00;
	font-weight:bold;
}
.widebox {
	width: 500px;
}
td {
	padding: 2px 5px 2px 10px;
}


a
{
   color: #800080;
   font-weight:bold;
   text-decoration: none;
}
a:visited
{
   color: #800080;
   font-weight:bold;
}
a:active
{
   color: #800080;
   font-weight:bold;
}
a:hover
{
   color: #ff0080;
   font-weight:bold;
   text-decoration: underline;
}

       

/***************************************************************/
</style>
<!--link href="admin.css" rel="stylesheet" type="text/css"-->
</head>

<body>

<h1  style="text-shadow:2px 2px #ff80ff;">ADMIN PAGE</h1> 

<p>
<span style="font: bold 14px sans-serif; text-shadow:2px 2px #ff80ff;">Welcome </span>
<span style="text-decoration: underline"> <?php echo $_SESSION['username'];?>
</span>
</p>

<p>
<?php
    include('../includes/logout_db.inc.php');  
?> 
</p>

<br />

<br />

<center>

<a href="register.php">
<img src="./images/cooltext789065468.png" onmouseover="this.src='./images/cooltext789065468MouseOver.png';" onmouseout="this.src='./images/cooltext789065468.png';" />
</a>

<br/>

<a href="reg_members_view.php">
<img src="./images/cooltext789070740.png" onmouseover="this.src='./images/cooltext789070740MouseOver.png';" onmouseout="this.src='./images/cooltext789070740.png';" />    
</a>

<br/>

<a href="reserve_view.php">
<img src="./images/cooltext789092019.png" onmouseover="this.src='./images/cooltext789092019MouseOver.png';" onmouseout="this.src='./images/cooltext789092019.png';" />
</a>

<br/>

<a href="upload.php">
<img src="./images/cooltext789089422.png" onmouseover="this.src='./images/cooltext789089422MouseOver.png';" onmouseout="this.src='./images/cooltext789089422.png';" />
</a>


</center>

<br>





</body>

</html>