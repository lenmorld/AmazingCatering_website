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

$sql = "SELECT reserve_ID,
		    name,
		    contactno,
		    email,
			event,
			location,
			DATE_FORMAT(date, '%b-%d-%Y') AS date_event,
			DATE_FORMAT(time, '%h:%i %p') AS time_event,
			DATE_FORMAT(date_reserved, '%b-%d-%Y') AS date_reserved_format,
			numGuests,
			guestPrice,
			coverage,
			extras,
			cakes,
			treats,
			personnel,
			message,
			totalPrice,			
			paid
        FROM reservations, users
		WHERE reservations.user_ID = users.user_ID
        ORDER BY reserve_ID DESC";

$result = $conn->query($sql) or die(mysqli_error());
                       
?>

<html>
<head>
<meta charset="utf-8">
<title>View Reservations Page - Admin</title>
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

</p>
</span>
<!-------------------------------------------------------------------------------->


<p style="clear:both"><h1>Reservations - Admin</h1></p>

<div style="overflow:scroll;">

<center>

		<?php
		
			while($row = $result->fetch_assoc())
				{   // get each row of query result 
					//display each field by TD and each row by TR                    
				?>
				
			  <table rules="all" border="3px" cellpadding="5px" cellspacing="5px" width="500px">  
				
				<tr><th scope="col">Customer Name</th><td><?php echo $row['name']; ?></td></tr>
				<tr><th scope="col">Contact Number</th><td><?php echo $row['contactno']; ?></td></tr>
				<tr><th scope="col">Email</th><td><?php echo $row['email']; ?></td></tr>
				<tr><th scope="col">Event</th><td><?php echo $row['event']; ?></td></tr>
				<tr><th scope="col">Location</th> <td nowrap><?php echo $row['location']; ?></td></tr>
				<tr><th scope="col">Event Date</th><td><?php echo $row['date_event']; ?></td></tr>
				<tr><th scope="col">Event Time</th> <td nowrap><?php echo $row['time_event']; ?></td></tr>
				<tr><th scope="col">Date Reserved</th><td><?php echo $row['date_reserved_format']; ?></td></tr>

				<tr><th>Number of Guests</th> <td><?php echo $row['numGuests']; ?></td></tr>
				<tr><th>Initial Price for Num. of Guests</th> <td><?php echo "P " . $row['guestPrice']; ?></td></tr>
				<tr><th colspan=2>Additions</th></tr>
				<tr><th>Coverage</th> <td><?php echo str_replace(array(',', '_'),'<br/>',$row['coverage']); ?></td></tr>
				<tr><th>Extras</th> <td><?php echo str_replace(array(',', '_'),'<br/>',$row['extras']); ?></td></tr>
				<tr><th>Treats</th> <td><?php echo str_replace(array(',', '_'),'<br/>',$row['treats']); ?></td></tr>
				<tr><th>Personnel</th> <td><?php echo str_replace(array(',', '_'),'<br/>',$row['personnel']); ?></td></tr>
				<tr><th>Message</th> <td><?php echo $row['message']; ?></td></tr>
				<tr><th>Total Price</th> <td><?php echo "P " . $row['totalPrice']; ?></td></tr>
				
				<tr><th scope="col">Paid</th><td><?php if (isset($row['paid'])) {echo "P " . $row['paid'];} ?></td></tr>
						  
						  
						<?php //prepare query string and attach to URL to set the $_GET variable ?>
				<tr align="center"><td colspan=2><a href="reserve_update.php?reserve_ID=<?php echo $row['reserve_ID']; ?>">EDIT</a></td></tr>
				<tr align="center"><td colspan=2><a href="reserve_delete.php?reserve_ID=<?php echo $row['reserve_ID']; ?>">DELETE</a></td></tr>
				<tr align="center"><td colspan=2><a href="reserve_message.php?reserve_ID=<?php echo $row['reserve_ID']; ?>">MESSAGES</a></td></tr>
			  </table>
			  <br />
			  
		<?php
				} ?>


</center>

</div>


</body>
</html>

