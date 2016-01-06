<?php

//session_timeout logs out the user automatically after a period of inactivity
require_once('../includes/session_timeout.inc.php');

//check if user comes from an admin page, require log-in if outside admin
require_once('../includes/check_admin.inc.php');   

//prepare database connection
require_once('../includes/my_connection.inc.php'); 
$conn = dbConnect('write'); 

// initialize flags 
$OK = false; 
$done = false; 

$date1week = false; 
$datePast = false;
$timeOutOfRange = false; 
$duplicateDate = false; 

// initialize statement 
$stmt = $conn->stmt_init(); 
// get details of selected record 
if (isset($_GET['reserve_ID']) && !$_POST) { 		//first load of form, update is not clicked yet
  // prepare SQL query 
	  
$sql = "SELECT reserve_ID,
		    name,
		    contactno,
		    email,
			event,
			location,
			DATE_FORMAT(date, '%m/%d/%Y') AS date_event,
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
	        AND reserve_ID = ?		
        ORDER BY reserve_ID DESC";	  

	    //prepare SQL
        $stmt->prepare($sql); 
        // bind the query parameter 
        $stmt->bind_param('i', $_GET['reserve_ID']); 
        // bind the results to variables 
        $stmt->bind_result($reserve_ID,$name,$contactno,$email,$event,$location,
						   $date_event,$time_event,$date_reserved_format,
						   $numGuests,$guestPrice,$coverage,$extras,
						   $cakes,$treats,$personnel,$message,$totalPrice,$paid); 

		// execute the query, and fetch the result 
        $OK = $stmt->execute(); 
        $stmt->fetch(); 

        //free the database resources for the second query
        $stmt->free_result();
} 

//if form has been submitted, update record
if (isset($_POST['update']))
{      

    //############## time validation ##############################
    
	//proceed only if user inputs hour, minutes, and am/pm and if hour is not set to 0
    if ((isset($_POST['hour1'])) && (isset($_POST['min1'])) && (isset($_POST['ampm'])) && ($_POST['hour1'] > 0))
        {
			$hour1 = (int) $_POST['hour1'];      // get hours from user input - parse to integer
			$min1 = (int) $_POST['min1'];        // get minutes from user input - parse to integer    
			$ampm =  $_POST['ampm'] ;            // get am:pm from user input    
		    
		    $newTime = new DateTime("1/25/2012"); //declare a DateTime object, can use any date, only the time will be used here
		                                          // but for here we use Jan. 25 2012 
                                                  
                                                  //declaring a date sets it automatically to 00:00:00 in 24-hour format (12 midnight)
		    if ($ampm == 'AM')
		    {		}                   // if AM do nothing
		    elseif ($ampm == 'PM')
		    {$newTime->modify("+ 12 hours ");	}		   // if PM  add 12 hours
		                                                             
		    $newTime->modify("+ $hour1 hours ");           // add the user-input hours
		    $newTime->modify("+ $min1 minutes ");          // add the user-input minutes
        
			//checking of time range accepted -------------------------------------
			
			$lowerLimit = new DateTime("1/25/2012");
			$lowerLimit->modify("+ 8 hours ");              //8 AM lower limit
			
			$upperLimit = new DateTime("1/25/2012");
			$upperLimit->modify("+ 22 hours ");             //10 PM upper limit
			
			//---------------------------------------------------------------------
			
			if(($newTime < $lowerLimit) || ($newTime > $upperLimit)) // if lower than lower limit or higher than upper limit, error
			{
				$timeOutOfRange = true;
			} 

			$eventTime =  $newTime->format('h:i A');              //finalize eventtime for email -12-hour format with am-pm
			                                                      
			$eventTime24 =  $newTime->format('H:i:s');            //finalize eventtime for database, mysql needs 24-hour format 
			                                                      
			$_POST['time'] = $eventTime24;         				//set time for update                
			
			}
        
        
        //##################################################
        
	//proceed if time is in range	
    if ($timeOutOfRange == false)
    {
		//if user inputs new time and inputs hour, minutes, and am/pm and if hour is not set to 0
        if ((isset($_POST['hour1'])) && (isset($_POST['min1'])) && (isset($_POST['ampm'])) && ($_POST['hour1'] > 0))
            {
                    if (isset($_POST['date'])) 		// process date if it is inserted
                    {
                        $newDate = $_POST['date'];        // get date from user input
                                                         
                        $format = 'Y-m-d';               // Year - month - date  eg. 2012-01-25
                        
                        $tempDate = new DateTime($newDate);        // create a DateTime object using user-input date
                        $mySQLdate = $tempDate->format($format);   // format date

                        $now = new DateTime();			// create a DateTime object using current date
                        
                        
                        //compare date entered to 1 week after today (reservation must be at least 1 week earlier)
                        $reserveDate = new DateTime($newDate);
                        $reserveDate->modify("- 9 days ");   
                        
                        //compare date entered to any day in the past 
                        if ($mySQLdate < $now->format($format))
                        { 
                          $datePast = true;			//set datePast flag to true
                        }		 //compare date entered to 1 week after today 	          	
                        else if ($reserveDate->format($format) <  $now->format($format))  
                        {
                            $date1week = true;		//set date1week flag to true
                        } 
                        // -------------------------------------------------------


                         // find out if the date is duplicate ---------------------------
							//prepare SQL to get all dates from reservation table
                            $sql = "SELECT date
                              FROM reservations";

                         // submit the query and capture the result 
                         $result = $conn->query($sql) or die(mysqli_error()); 
                         // find out how many records were retrieved 
                         $numRows = $result->num_rows; 
                         
                         while ($row = $result->fetch_assoc()) {  //loop through each record
                            if ( ($mySQLdate == $row['date']))	//if user date matches a date in the table
                            { $duplicateDate = $row['date'];
                            }
                          }
                               
                         $_POST['date'] = $mySQLdate;  	// set update date

                }
            
				// if there are no errors in date, proceed with update
				if(($datePast == false) && ($date1week == false))		
				{		
				 //prepare update query
					$sql = "UPDATE reservations
							SET location = ?, date = ?, time = ?, paid = ? 
							WHERE reserve_ID = ?";   
							
					$stmt->prepare($sql);
					//bind parameters
					$stmt->bind_param('sssii', $_POST['location'], $_POST['date'], $_POST['time'],  $_POST['paid'], $_POST['reserve_ID']);

					$done = $stmt->execute();    //execute query
				
				}      

            }
			//if user did not enter new time, only update date
            else
            {    
                    if (isset($_POST['date'])) 		// process date if it is inserted
                    {
                        $newDate = $_POST['date'];        // get date from user input
                                                         
                        $format = 'Y-m-d';               // Year - month - date  eg. 2012-01-25
                        
                        $tempDate = new DateTime($newDate);        // create a DateTime object using user-input date
                        $mySQLdate = $tempDate->format($format);   // format date

                        $now = new DateTime();			// create a DateTime object using current date
                        
                        
                        //compare date entered to 1 week after today (reservation must be at least 1 week earlier)
                        $reserveDate = new DateTime($newDate);
                        $reserveDate->modify("- 9 days ");   
                        
                        //compare date entered to any day in the past 
                        if ($mySQLdate < $now->format($format))
                        { 
                          $datePast = true;			//set datePast flag to true
                        }		 //compare date entered to 1 week after today 	          	
                        else if ($reserveDate->format($format) <  $now->format($format))  
                        {
                            $date1week = true;		//set date1week flag to true
                        } 
                        // -------------------------------------------------------


                         // find out if the date is duplicate ---------------------------
							//prepare SQL to get all dates from reservation table
                            $sql = "SELECT date
                              FROM reservations";

                         // submit the query and capture the result 
                         $result = $conn->query($sql) or die(mysqli_error()); 
                         // find out how many records were retrieved 
                         $numRows = $result->num_rows; 
                         
                         while ($row = $result->fetch_assoc()) {  //loop through each record
                            if ( ($mySQLdate == $row['date']))	//if user date matches a date in the table
                            { $duplicateDate = $row['date'];
                            }
                          }
                               
                         $_POST['date'] = $mySQLdate;  	// set update date

                }
                // if there are no errors in date, proceed with update
				if(($datePast == false) && ($date1week == false))
				{
			   //prepare update query, time is not included
				$sql = "UPDATE reservations
						SET location = ?, date = ?, paid = ? 
						WHERE reserve_ID = ?";   
						
				$stmt->prepare($sql);		//prepare SQL
				//bind paramaters to query
				$stmt->bind_param('ssii', $_POST['location'], $_POST['date'],  $_POST['paid'], $_POST['reserve_ID']);

				$done = $stmt->execute();   //execute query
				
				}
				 
            }     
    }
 
}

// redirect page on success or if $_GET['reserve_ID'] not defined 
if ($done  || !isset($_GET['reserve_ID'])) { 
  header('Location: ' . './reserve_view.php'); 	//redirect to list of reservations
  exit; 
} 
// display error message if query fails 
if (isset($stmt) && !$OK && !$done ) { 
  $error = $stmt->error; 
} 

?>

<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<script type="text/javascript" src="common/js/form_init.js" id="form_init_script"
data-name="">
</script>
                                                                  
<title>Update Reservations Page - Admin</title>
<link href="admin.css" rel="stylesheet" type="text/css">
</head>

<body>

  <!--------------------------- USERNAME and LOGOUT ------------------------------->

<p style="float:left;">
<?php
    echo "You are logged in, " . $_SESSION['username'];		//display username
    include('../includes/logout_db.inc.php');		//logout button    
?>
</p>

<!-------------------------------------------------------------------------------->


<h1>Update Reservations</h1>
<p><a href="reserve_view.php">List all reservations </a></p>
    <?php     

            if (isset($error)) 	// if there are errors
            { 
                if($datePast)		//past date inserted
                {
                     echo "<p class='warning'>Cannot set a date from past</p>";
                     echo "<br/><a href=" . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'] . ">BACK to UPDATE PAGE</a>";
                }
                else if($date1week)		//date 1 week from today inserted
                {
                     echo "<p class='warning'>Cannot set a date 1 week from now</p>";
                     echo "<br/><a href=" . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'] . ">BACK to UPDATE PAGE</a>";
                }                
                else if($duplicateDate)		//date already reserved
                {
                 echo "<p class='warning'>There was already a reservation for $duplicateDate </p>";
                 echo "<br/><a href=" . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'] . ">BACK to UPDATE PAGE</a>"; 
                }
                else if($timeOutOfRange)	//time is out of range
                {
                 echo "<p class='warning'>Time not accepted. Choose a time from 8:00 AM to 10:00 PM.</p>";
                 echo "<br/><a href=" . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'] . ">BACK to UPDATE PAGE</a>";  
                }
                else			//other errors
                {
                echo "<p class='warning'>Error: $error</p>"; 
                }
            }
            else if ($reserve_ID == 0) { ?> 	
                <p class="warning">Record does not exist.</p> 
                    <?php } 
            else { 		//display update form if record exists ?> 
                
                
<div style="border: 1px red solid; width:500px; margin: 0px auto; padding:20px;">           
	<form style="BACKGROUND-COLOR: transparent; MARGIN: 5px; WIDTH: 500px; FONT-SIZE: 14px; WebkitTransform: "
	id="docContainer" class="fb-75-item-column fb-leftlabel selected-object"
	enctype="multipart/form-data" method="post" action="" novalidate="novalidate"
	data-margin="custom">

       <div style="MIN-HEIGHT: 20px" id="fb-form-header1" class="fb-form-header">
        <a id="fb-link-logo1" class="fb-link-logo" href="" target="_blank"><img style="DISPLAY: none" id="fb-logo1" class="fb-logo" title="Alternative text" alt="Alternative text" src="common/images/image_default.png"/></a>
       </div>
       <div id="section1" class="section">
			<div id="column1" class="column ui-sortable">
			
			  <!----------------- heading ------------------------------------->
			  <div style="FILTER: " id="item1" class="fb-item fb-100-item-column">
				<div class="fb-header">
				  <h2 style="DISPLAY: inline">
					Update Reservation Details
				  </h2>
				</div>
			  </div>
			  
			  <br/>
			  <!------------------- event -------------------------------------->
			  <div style="FILTER: " id="item26" class="fb-item fb-20-item-column">
				<div class="fb-header">
				  <h2 style="DISPLAY: inline; FONT-SIZE: 14px; FONT-WEIGHT: bold">
					Event:
				  </h2>
				</div>
			  </div>
			  
			  <div style="FILTER: " id="item27" class="fb-item fb-75-item-column">
				<div class="fb-header fb-item-alignment-center">
				  <h2 style="DISPLAY: inline; FONT-SIZE: 14px; FONT-WEIGHT: normal">

					<?php	//display event type 
						echo htmlentities($event, ENT_COMPAT, 'utf-8'); ?>
				  </h2>
				</div>
			  </div>
			  
			  <!-------------------- end of event ------------------------------>
			  
			   <br/>
			  <!-------------------- name ---------------------------------------->
			  <div style="FILTER: " id="item28" class="fb-item fb-20-item-column">
				<div class="fb-header">
				  <h2 style="DISPLAY: inline; FONT-SIZE: 14px; FONT-WEIGHT: bold">
					Name:
				  </h2>
				</div>
			  </div>
			  <div style="FILTER: " id="item29" class="fb-item fb-75-item-column">
				<div class="fb-header fb-item-alignment-center">
				  <h2 style="DISPLAY: inline; FONT-SIZE: 14px; FONT-WEIGHT: normal">
					
					<?php	//display name of customer 
						echo htmlentities($name, ENT_COMPAT, 'utf-8'); ?>
				  </h2>
				</div>
			  </div>
			  
			  
			  <!---------------------- end of name --------------------------------->
			  
			   <br/>
			  <!------------------------- contact no ------------------------------->
			  <div style="FILTER: " id="item30" class="fb-item fb-33-item-column">
				<div class="fb-header">
				  <h2 style="DISPLAY: inline; FONT-SIZE: 14px; FONT-WEIGHT: bold">
					Contact No:
				  </h2>
				</div>
			  </div>
			  <div style="FILTER: " id="item31" class="fb-item fb-50-item-column">
				<div class="fb-header fb-item-alignment-center">
				  <h2 style="DISPLAY: inline; FONT-SIZE: 14px; FONT-WEIGHT: normal">
					<?php	//display contact no of customer 
						echo htmlentities($contactno, ENT_COMPAT, 'utf-8'); ?>
				  </h2>
				</div>
			  </div>
			  <!--------------------- end of contact ----------------------------->
			  
			   <br/>
			  <!--------------------- email ---------------------------------------->
			  <div style="FILTER: " id="item35" class="fb-item fb-33-item-column">
				<div class="fb-header">
				  <h2 style="DISPLAY: inline; FONT-SIZE: 14px; FONT-WEIGHT: bold">
					Email:
				  </h2>
				</div>
			  </div>
			  <div style="FILTER: " id="item36" class="fb-item fb-50-item-column">
				<div class="fb-header fb-item-alignment-center">
				  <h2 style="DISPLAY: inline; FONT-SIZE: 14px; FONT-WEIGHT: normal">
				  <?php //display email of customer
						echo htmlentities($email, ENT_COMPAT, 'utf-8'); ?>
				  </h2>
				</div>
			  </div>
			  <!------------- end of email ------------------------------------>

			   <br/>
			  <!---------------- location ------------------------------------->

			  <div style="FILTER: " id="item5" class="fb-item fb-100-item-column">
				<div class="fb-grouplabel">
				  <label style="DISPLAY: inline" id="item5_label_0">Location of Event:</label>
				</div>
				<div class="fb-input-box">
				  <input id="item5_text_1" maxlength="100" name="location" data-hint=""
				  required autocomplete="off" placeholder="" type="text" style="width:350px;"
				  value="<?php  //display location of event for edit
						echo htmlentities($location, ENT_COMPAT, 'utf-8'); ?>"/>
				</div>
			  </div>
			  
			  <!------------------- end of location -------------------------->
			   <br/>
			  <!-------------------------------- date ------------------------->
			  <div style="FILTER: " class="fb-item fb-50-item-column">
				<div class="fb-grouplabel">
				  <label style="DISPLAY: inline" id="item21_label_0">Date</label>
				</div>

				<div class="fb-input-date">
				  <input id="item21_date_1" class="datepicker" name="date" data-hint=""
				  required type="date" 
				  value="<?php  //display date of event for edit
						echo htmlentities($date_event, ENT_COMPAT, 'utf-8'); ?>" />
				</div>

			  </div>
			   <!-------------------------------- end of date ------------------------->

			   <!-------------------------------- time ------------------------->
				<div>
					<div class="fb-header">
					  <h2 style="FONT-SIZE: 15px">
						Time:
					  </h2>
					  <h2 style="FONT-SIZE: 15px; font-weight: normal;">
						<?php  //display time of event
								echo htmlentities($time_event, ENT_COMPAT, 'utf-8'); ?>
					  </h2></div>              
				</div>
			</div>
          
			<?php //HTML checkbox element to allow user enter new time ?>
            <p id="allowNewTime">
                <input type="checkbox" name="time_new" id="time_new" >
                <label for="time_new" style="display:inline;">Enter new time</label>
            </p>
                            
            
         <div style="PADDING-LEFT: 10px; FILTER: " style="PADDING-LEFT: 10px" >
            <div class="fb-grouplabel" style="display:none;">
              <label style="DISPLAY: inline" id="item17_label_0">Hour</label>
            </div>
            <div class="fb-input-number" style="display:none;">
              <input id="item17_number_1" name="hour1" min="1" max="12" data-hint=""
              autocomplete="off" step="1" type="number"  <?php 
                //display value if it is entered already
                if (isset($hour1))
                    {echo 'value="'.htmlentities($hour1,ENT_COMPAT,'UTF-8').'"';}
                ?>/>
            </div>
          </div>
          
          <div style="PADDING-LEFT: 10px; FILTER: " style="PADDING-LEFT: 10px" >
            <div class="fb-grouplabel" style="display:none;">
                    <label style="DISPLAY: inline" id="item18_label_0">Minute</label>
            </div>
            <div class="fb-input-number" style="display:none;">
                  <input id="item18_number_0" name="min1" min="0" max="59" data-hint=""
                  autocomplete="off" step="1" type="number" <?php 
                //display value if it is entered already
                if (isset($min1))
                    {echo 'value="'.htmlentities($min1,ENT_COMPAT,'UTF-8').'"';}
                ?>/>
            </div>
          </div>
          
          <div style="PADDING-LEFT: 10px" class="optional">
            <div id="item22_select_1">
                <div class="fb-grouplabel" >
                  <label style="DISPLAY: inline" id="item22_label_0">AM / PM:</label>
                </div>
                <div class="fb-dropdown" id="item22_select_1">
                  <select  name="ampm" data-hint="">
					<?php //dropdown listbox AM/PM, default is AM ?>
                    <option id="item22_1_option" value="AM" >
                      AM
                    </option>
                    <option id="item22_2_option" value="PM" >
                      PM
                    </option>
                  </select>
                </div>
            </div>
          </div>  
          
          <!---------------------------- end of time ------------------------->
		  
		  <br />
		  <table rules="all" border="3px" cellpadding="5px" cellspacing="5px" width="500px"> 
				<tr><th>Number of Guests</th> <td><?php echo $numGuests; ?></td></tr>
				<tr><th>Initial Price for Num. of Guests</th> <td><?php echo "P " . $guestPrice; ?></td></tr>
				<tr><th colspan=2>Additions</th></tr>
				<tr><th>Coverage</th> <td><?php echo str_replace(array(',', '_'),'<br/>',$coverage); ?></td></tr>
				<tr><th>Extras</th> <td><?php echo str_replace(array(',', '_'),'<br/>',$extras); ?></td></tr>
				<tr><th>Treats</th> <td><?php echo str_replace(array(',', '_'),'<br/>',$treats); ?></td></tr>
				<tr><th>Personnel</th> <td><?php echo str_replace(array(',', '_'),'<br/>',$personnel); ?></td></tr>
				<tr><th>Message</th> <td><?php echo $message; ?></td></tr>
				<tr><th>Total Price</th> <td><?php echo "P " . $totalPrice; ?></td></tr>
				<tr><th>Paid</th> <td><?php echo "P " . $paid; ?></td></tr>				
		  </table>
		  
          <br />
            <!---------------------------------- paid? -------------------->
          <div id="item32" class="fb-item fb-75-item-column">
            <div class="fb-grouplabel">
              <label style="DISPLAY: inline" id="item32_label_0">Paid</label>
            </div>
            <!--div class="fb-dropdown">
              <select id="item32_select_1" name="paid" data-hint="" required>
			  <?php 
			  // determine if already paid or not
			  // attach 'selected' to YES opton if paid and to NO option if not paid
			  ?>
                <option id="item32_1_option" value="1" <?php /*
                        if (isset($paid) && ($paid != 1))
                        {echo 'selected ';} */ ?>>
						YES
                </option>
                <option id="item32_2_option" value="0" <?php /*
                        if (isset($paid) && ($paid == 0))
                        { echo 'selected ';} */ ?>>
						NO
                </option>
              </select>
            </div-->
			
			<label for="paid" style="float:left;">PAID: P</label>
				<input type="text" name="paid" id="paid" size=10
				<?php if (isset($paid)) {echo $paid;  } ?> ><b>.00</b>
			
			
          </div>
          
          <!---------------------------------- end of paid? -------------------->
          
          <!----------------- SUBMIT button --------------------------------->
        
		  <br/>  
		  <div style="HEIGHT: 85px" id="fb-submit-button-div">
			<input style="BACKGROUND-IMAGE: url(theme/default/images/btn_submit.png); margin-left:0px;"
			id="fb-submit-button" class="fb-button-special" type="submit" value="SAVE" name="update"/>
		  </div>
		  
		  <?php //hidden input is needed to pass the reserve_ID of record to be edited ?>
		  <input name="reserve_ID" type="hidden" value="<?php echo $reserve_ID; ?>">
      
		</div>
	</form> 
</div>     
                
    <?php } ?>
    
<script src="toggle_fields.js"></script>
</body>
</html>
