<?php

//session timeout logs out the user automatically after a period of inactivity
require_once('./includes/user_session_timeout.inc.php');


if (isset($_SESSION['user_ID']))
{
    $userID = $_SESSION['user_ID']   ;       
}
else
{$userID = 0;}

//check if user comes from an admin page, require log-in if outside admin
//require_once('./includes/check_admin.inc.php');

// prepare database connection
require_once('./includes/my_connection.inc.php');
$conn = dbConnect('read');


//prepare SQL string

$sql = "SELECT reserve_ID,
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
        FROM reservations
        WHERE user_ID = $userID  
        ORDER BY reserve_ID DESC";

$result = $conn->query($sql) or die(mysql_error());   

//$result = $conn->query($sql);


$numRows  = $result->num_rows;

//echo $sql;
//echo $_SESSION['user_ID'];
//$message = $conn->errorInfo();
//  echo $message;

// or die(mysql_error());   

?>


<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

                                                                                     
<title>Member Page Main</title>

<!--link href="admin.css" rel="stylesheet" type="text/css"-->

 <style type="text/css">
 
 body {
     
     background-color: thistle;
 }
 
 h1 {color:black;
        font: bold 20px sans-serif;}
        
  h2 {color:black;
        font: bold 15px sans-serif;}
        
label {
        font: bold 14px sans-serif;
        }
        
a
{
   color: #8a008a;
   font-weight: bold;
   text-decoration: underline;
}
a:visited
{
   color: #8a008a;
   font-weight: bold;
}
a:active
{
   color: #8a008a;
   font-weight: bold;
}
a:hover
{
   color: blue;
   text-decoration: none;
   font-weight: bold;
}

img {
opacity:0.7;
}

img:hover
    {
        opacity:1;
    }
    
span.a2 {
    font: 18px "Trebuchet MS", Helvetica, sans-serif;
    color: #8a008a;
}

span.a1 {
    font: 18px "Trebuchet MS", Helvetica, sans-serif;
    color: #800040;
}

 </style>
 


</head>

<body>
    

<div style="width:700px; border: 2px pink outset; margin: 20px auto; padding: 20px; font-family: sans-serif;">

<h1>Member's Page</h1> 


<p>

<span class="a2">
<?php
    echo "Welcome  <u>" . $_SESSION['username'] . "</u>";
    //echo "wa" . $userID . "numrows:" . $numRows;
?>
</span>

</p>

<br />

<?php

if ($numRows)
{ ?>

<h2>Reservation/s:</h2>

<div style="overflow:auto;">

    <center>

            <?php
            
                while($row = $result->fetch_assoc())
                    {   // get each row of query result 
                        //display each field by TD and each row by TR                    
                    ?>
                    
                  <table rules="all" border="3px" cellpadding="5px" cellspacing="5px" width="500px">  
                    <!--tr><th scope="col">Reserve_ID</th><td><?php echo $row['reserve_ID']; ?></td></tr-->
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
                    <tr align="center"><td colspan=2><a href="reschedule.php?reserve_ID=<?php echo $row['reserve_ID']; ?>">RESCHEDULE</a></td></tr>
                  
                    <tr align="center"><td colspan=2><a href="message.php?reserve_ID=<?php echo $row['reserve_ID']; ?>">MESSAGES</a>
 
                  
                  
                  </table>
                  <br />
                  
            <?php
                    } ?>

    

    </center>

</div>


<?php }
else
{ echo '<span class="a1">No reservation yet.</span>';  }
?>

<p><b><a href="reservation.php">
    
    
<img src="./images/cooltext774482834.png" onmouseover="this.src='./images/cooltext774482834MouseOver.png';" onmouseout="this.src='./images/cooltext774482834.png';" />
    
</a></b> </p>

<!--tr align="center"><td colspan=2><a href="reserve_update.php?reserve_ID=<?php echo $row['reserve_ID']; ?>">EDIT</a></td></tr-->




<br>

<p>

<?php

    include_once('./includes/user_logout.inc.php');        // log out button

?>


</p>


</div>



</body>

</html>