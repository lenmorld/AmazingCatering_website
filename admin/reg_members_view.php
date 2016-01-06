<?php  

//session timeout logs out the user automatically after a period of inactivity
require_once('../includes/session_timeout.inc.php');

//check if user comes from an admin page, require log-in if outside admin
require_once('../includes/check_admin.inc.php');   

// prepare database connection
require_once('../includes/my_connection.inc.php');
$conn = dbConnect('read');

//prepare SQL string

/* normal table JOIN used

*/

$sql = "SELECT user_id, username, name, contactno, email
          FROM users";

$result = $conn->query($sql) or die(mysqli_error());
                       
?>

<html>
<head>
<meta charset="utf-8">
<title>View Registered Members Page - Admin</title>
<link href="admin.css" rel="stylesheet" type="text/css">
</head>

<body>

<!--------------------------- USERNAME and LOGOUT ------------------------------->
<div style="float:right"><a href="./adminpage.php">Back to MAIN ADMIN PAGE</a></div>

<p style="float:left;">

<span style="font: bold 14px sans-serif; text-shadow:2px 2px #ff80ff;">
<?php
    echo "You are logged in, " . $_SESSION['username'];	// display username of admin
    
    include('../includes/logout_db.inc.php');		// logout button
	      
?>
</span>

</p>
<!-------------------------------------------------------------------------------->


<p style="clear:both"><h1>Registered Members</h1></p>

<div style="overflow:auto;margin: 20px auto;">

<center>

<table rules="all" border="3px" cellpadding="5px" cellspacing="5px" width="800px">
  <tr>
  
  
    <th scope="col">User_ID</th>
    <th scope="col">Username</th>
    <th scope="col">Name</th>
    <th scope="col">Contact No</th> 
    <th scope="col">Email</th>
  </tr> 
  <tr></tr>
        <?php  while($row = $result->fetch_assoc()) {   // get each row of query result 
														//display each field by TD and each row by TR					
																				?> 	
              <tr>
                <td align="center"><?php echo $row['user_id']; ?></td>
                <td><?php echo $row['username']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['contactno']; ?></td>
                <td><?php echo $row['email']; ?></td> 
						<?php //prepare query string and attach to URL to set the $_GET variable ?>

              </tr>
        <?php }  ?>
</table>



</center>

</div>


</body>
</html>

