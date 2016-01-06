<?php

//session timeout logs out the user automatically after a period of inactivity
require_once('../includes/session_timeout.inc.php');

//check if user comes from an admin page, require log-in if outside admin
require_once('../includes/check_admin.inc.php');   

//prepare database connection
require_once('../includes/my_connection.inc.php');
$conn = dbConnect('write');

// initialize flags
$OK = false;
$deleted = false;

// initialize statement
$stmt = $conn->stmt_init();

// get details of selected record
if (isset($_GET['reserve_ID']) && !$_POST) {		//first load of page, deletion not confirmed yet
 //prepare SQL to get some details about the record to be deleted
  $sql = 'SELECT reserve_ID,
		      event,
		      name,
		      location,
		      date		
          FROM reservations, users
		  WHERE reservations.user_ID = users.user_ID
		  AND reserve_ID = ?';

	$stmt->prepare($sql);		// prepare SQL
    // bind the query parameters
    $stmt->bind_param('i', $_GET['reserve_ID']);
    // bind the result to variables
    $stmt->bind_result($reserve_ID,$event,$name,$location,$date);
    // execute the query, and fetch the result
    $OK = $stmt->execute();
    $stmt->fetch();
  
}
// if confirm deletion button has been clicked, delete record
if (isset($_POST['delete'])) 
{
	//prepare SQL to delete record
    $sql = "DELETE 
            FROM reservations 
            WHERE reserve_ID = ?";    
    
    $stmt->prepare($sql);   // prepare SQL
    $stmt->bind_param('i', $_POST['reserve_ID']);  // bind the query parameters
    $stmt->execute();   // execute the query
    
    // if there's an error affected_rows is -1
    if ($stmt->affected_rows > 0) {
      $deleted = true;
    } else {
      $error = 'There was a problem deleting the record. '; 
    }

}

// redirect the page if deletion is successful, 
// cancel button clicked, or $_GET['article_id'] not defined
if ($deleted || isset($_POST['cancel_delete']) || !isset($_GET['reserve_ID']))  {
  header('Location: ' . './reserve_view.php');
  exit;
  }
// if any SQL query fails, display error message
if (isset($stmt) && !$OK && !$deleted) {
  $error .= $stmt->error;
}
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>Delete Reservations Page - Admin</title>
<link href="admin.css" rel="stylesheet" type="text/css">
</head>

<body>

 <!--------------------------- USERNAME and LOGOUT ------------------------------->

<p style="float:left;">

<?php
    echo "You are logged in, " . $_SESSION['username'];			//display username
    include('../includes/logout_db.inc.php');		// log out button
?>

</p>

<!-------------------------------------------------------------------------------->

<h1>Delete Reservation Entry </h1>
<?php  
if (isset($error)  && !empty($error)) {		//if there are errors
  echo "<p class='warning'>Error: $error</p>";
}
if($reserve_ID == 0) {    //record to be deleted does not exist ?>			
	<p class="warning">Invalid request: record does not exist.</p>
	<?php 
} 
else { ?>
	<p class="warning">Please confirm that you want to delete the following item. This action cannot be undone.</p>
	<p><?php    //display some details about reservation before deletion
				echo  'Event: ' .    htmlentities($event, ENT_COMPAT, 'utf-8') . '<br .>' .
				   'Name: ' .  htmlentities($name, ENT_COMPAT, 'utf-8')  . '<br .>' .      
				   'Location: ' .  htmlentities($location, ENT_COMPAT, 'utf-8')  . '<br .>' .    
				   'Event Date: ' .  htmlentities($date, ENT_COMPAT, 'utf-8')  . '<br .>' ;    ?>

	</p>
	<?php 
} ?>
<form id="form1" method="post" action="">
    <p>
    <?php if(isset($reserve_ID) && $reserve_ID > 0) { ?>
                    <input type="submit" name="delete" value="Confirm Deletion">
                <?php } ?>
                <input name="cancel_delete" type="submit" id="cancel_delete" value="Cancel">
            <?php if(isset($reserve_ID) && $reserve_ID > 0) { ?>
                <input name="reserve_ID" type="hidden" value="<?php echo $reserve_ID; ?>">
    <?php } ?>
    </p>
</form>
</body>
</html>
