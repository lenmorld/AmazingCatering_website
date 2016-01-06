<?php

//session_timeout logs out the user automatically after a period of inactivity
require_once('./includes/user_session_timeout.inc.php');


if (isset($_SESSION['user_ID']))
{
    $userID = $_SESSION['user_ID']   ;       
}
else
{$userID = 0;}


//prepare database connection
require_once('./includes/my_connection.inc.php'); 
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
  $sql = 'SELECT reserve_ID,
                    event,
                    location,
                    DATE_FORMAT(date, "%m/%d/%Y") AS date_event,
                    DATE_FORMAT(time, "%h:%i %p") AS time_event,
                    date_reserved,
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
	  WHERE reserve_ID = ?';


	//prepare SQL
        $stmt->prepare($sql); 
        // bind the query parameter 
        $stmt->bind_param('i', $_GET['reserve_ID']); 
        // bind the results to variables 
        $stmt->bind_result($reserve_ID,$event,$location,
						   $date_event,$time_event,$date_reserved,
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
		    { if ($hour1 == 12) { $newTime->modify("- 12 hours ");   }    }                   // if 12 AM (midnyt) deduct 12 hours
		    elseif (($ampm == 'PM') && ($hour1 != 12))		// if PM but not 12 PM
		    {$newTime->modify("+ 12 hours ");	}		   // add 12 hours
		                                                             
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
							SET location = ?, date = ?, time = ? 
							WHERE reserve_ID = ?";   
							
					$stmt->prepare($sql);
					//bind parameters
					$stmt->bind_param('sssi', $_POST['location'], $_POST['date'], $_POST['time'], $_POST['reserve_ID']);

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
						SET location = ?, date = ? 
						WHERE reserve_ID = ?";   
						
				$stmt->prepare($sql);		//prepare SQL
				//bind paramaters to query
				$stmt->bind_param('ssi', $_POST['location'], $_POST['date'], $_POST['reserve_ID']);

				$done = $stmt->execute();   //execute query
				
				}
				 
            }     
    }
 
}

// redirect page on success or if $_GET['reserve_ID'] not defined 
if ($done  || !isset($_GET['reserve_ID'])) { 
  header('Location: ' . './memberpage.php'); 	//redirect to list of reservations
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
<script type="text/javascript" src="./resched_data/common/js/form_init.js" id="form_init_script"
data-name="">
</script>
                                                                  
<title>Resheduling - Member</title>
<!--link href="admin.css" rel="stylesheet" type="text/css"-->


 <style type="text/css">
 
 body {
     
     background-color: thistle;
 }
 
 h1 {color:purple;
        font: bold 20px "Trebuchet MS", Helvetica, sans-serif;}
        
  h2 {color:black;
        font: bold 15px sans-serif;}
        
label {
        font: bold 14px sans-serif;
        }
        
a
{
   color: #8a008a;
   text-decoration: underline;
}
a:visited
{
   color: #8a008a;
}
a:active
{
   color: #8a008a;
}
a:hover
{
   color: blue;
   text-decoration: none;
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


#allowNewTime, .optional {
	display:none;
}


/*  Rule for Submit button container */
#docContainer #fb-submit-button-div {height: 65px; padding: 10px 0 0 0;}

/*  Rule for Submit button */
#fb-submit-button {
	color: #fff;
	font-family: Helvetica, Arial;
	font-weight: bolder;
	font-size:15px;
	border: none;
	margin-right: 6%;
	margin-left: 6%;
	width: 102px; height: 31px;
	text-shadow: 0 1px 0 rgba(0,0,0,0.3);
	cursor: pointer;
	background: url('./resched_data/theme/default/images/btn_submit.png') no-repeat;
	padding:0;
}

#fb-submit-button:hover {
	background: url('./resched_data/theme/default/images/btn_submit_hov.png') no-repeat;

}

 </style>

</head>

<body>

<!--------------------------- USERNAME and LOGOUT ------------------------------->


<div style="border: 5px purple groove;
		margin:20px;
                background-color:pink;
                height:80px;">
    
    <div style="color:purple;
                    font: bold 15px  'Lucida Sans Unicode', 'Lucida Grande', sans-serif;
                    width:75%;
                    float:left;
                    padding:10px;">
    
    <?php
    
        echo "You are logged in, <u>" . $_SESSION['username'] . "</u>";            //display username
        echo "<br>";
        echo "<br>";
        include('./includes/user_logout.inc.php');        // log out button
              
    ?>
    
    </div>


    <div>
    
        <div style="float:left;padding:10px;">
        <a href="./memberpage.php">
        <img src="./images/cooltext774339533.png" onmouseover="this.src='./images/cooltext774339533MouseOver.png';" onmouseout="this.src='./images/cooltext774339533.png';" />
        </a>
        </div>
        
        <!--div style="float:left;padding:10px;">
        <a href="./home.php">
        <img src="./images/cooltext774344919.png" onmouseover="this.src='./images/cooltext774344919MouseOver.png';" onmouseout="this.src='./images/cooltext774344919.png';" />
        </a>
        </div-->
    
    </div>


</div>


<!-------------------------------------------------------------------------------->

<div style="margin:20px;">

<h1>Reschedule</h1>

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
                
   
</div>   
                
<div style="border: 1px red solid; width:500px; margin: 20px auto; padding:20px;">           
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

			  
			   <br/>
			  <!------------------------- contact no ------------------------------->

			  
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
				<tr><th>Paid</th><td><?php if (isset($paid)) {echo "P " . $paid;} ?></td></tr>				
		  </table>
		  
          <br />
		  
          
          <!----------------- SUBMIT button --------------------------------->
        
		  <br/>  
		  <div style="HEIGHT: 85px" id="fb-submit-button-div">
			<input style="BACKGROUND-IMAGE: url(resched_data/theme/default/images/btn_submit.png); margin-left:0px;"
			id="fb-submit-button" class="fb-button-special" type="submit" value="SAVE" name="update"/>
		  </div>
		  
		  <?php //hidden input is needed to pass the reserve_ID of record to be edited ?>
		  <input name="reserve_ID" type="hidden" value="<?php echo $reserve_ID; ?>">
      
		</div>
	</form> 
</div>   
           
                
                
    <?php } ?>
    
<script src="./resched_data/toggle_fields.js"></script>
</body>
</html>
