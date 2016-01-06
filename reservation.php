<?php

$online = false;

//session timeout logs out the user automatically after a period of inactivity
require_once('./includes/user_session_timeout.inc.php');


if (isset($_SESSION['user_ID']))
{
    $userID = $_SESSION['user_ID']   ;       
}
else
{$userID = 0;}


//set up database connection
require_once('./includes/my_connection.inc.php');
$conn = dbConnect('write');

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
// prevents display of errors
ini_set('display_errors', '0');      
}

//initialize arrays
$missing = array();
$errors = array();

$otherError = false;


$freeVideo = false;
$free2LayerCake = false;
$freeBubbleMachine = false;
$freeChocoFountain = false;
$free50Cupcakes = false;

//check if form has been submitted
if (isset($_POST['send']))
{
		
	//expected fields
	$expected = array('txtNumGuests','hGuestPrice','event','package','location','date','eventtime','eventtime24','cupcakes','hCupcakePrice','h2LayerCakePrice','hChocoFountainPrice','coverage','extras','cakes','treats','personnel','hTotalPrice','message','mascotWho');

	//required fields
	$required = array('txtNumGuests','hGuestPrice','event','package','location','date','eventtime','eventtime24','hTotalPrice');

	
	//##################### input VALIDATION and PROCESSING #####################
	
	//event
	
		if (isset($_POST['txtNumGuests']))			//check Freebies based on num of guests
		{
		$number = $_POST['txtNumGuests'];

						//Video
						if ($number >= 100)
						{
								$freeVideo = true;	
						}
		
						//2 Layer Cake freebie
						if ($number >= 150)
						{
								$free2LayerCake = true;		
						}
		
						//Bubble Machine Freebie
						if ($number >=200)
						{
							$freeBubbleMachine = true;
						}
		
						//Cupcake and Choco Fountain Freebie
						
						if ($number >=300)
						{
							$free50Cupcakes = true;
							$freeChocoFountain = true;
						}
						 
		}
	
	if (isset($_POST['event'])==false)		//set event variable empty if none selected
	{
		$_POST['event'] = '';
	}
	else if ($_POST['event'] == 'Other')	//if 'Other' event selected, check if text box is filled 
		{	
			if (isset($_POST['other_event']))
			{
				if (strlen($_POST['other_event']) > 3)
				{}
				else					//if event has less than 3 characters, set error
				{
				 $_POST['event'] = '';
				 $otherError = true;
				}										
			}
			else
			{
			 $_POST['event'] = '';		//if event text box is not filled, set error
			 $otherError = true;
			}
			
		}
												//initialize arrays for each checkbox set
	if (isset($_POST['coverage'])==false)
	{
		$_POST['coverage'] = array();
	}		

	if (isset($_POST['extras'])==false)
	{
		$_POST['extras'] = array();
	}
	
	if (isset($_POST['cakes'])==false)
	{
		$_POST['cakes'] = array();
	}
	
	if (isset($_POST['treats'])==false)
	{
		$_POST['treats'] = array();
	}
	
	if (isset($_POST['personnel'])==false)
	{
		$_POST['personnel'] = array();
	}
		
	//#############################################################

	//date validation  #############################################
	
	if (isset($_POST['date']))      // process date if it is inserted
		{
		  $newDate = $_POST['date'];        // get date from user input
 
		  $format = 'Y-m-d';               // Year - month - date  eg. 2012-01-25
			
		  $newDate1 = new DateTime($newDate);   // create a DateTime object using user-input date
		  $now = new DateTime();                // create a DateTime object using current date
            
            //compare date entered to any day in the past ----------------------
			if ($newDate1->format($format) < $now->format($format))
			{   
                $errors['pastdate'] = true;
			}
            //-------------------------------------------------------------------
            
            
            //compare date entered to 1 week after today 
            //(reservation must be at least 1 week earlier)----------------------
            
            $reserveDate = new DateTime($newDate);
            $reserveDate->modify("- 9 days ");
            
            if ($reserveDate->format($format) <  $now->format($format))  
            {
               //cannot reserve 1 week before
               $errors['toosoondate'] = true;     
            }
            
            //-------------------------------------------------------------------
            
            // find out if the date is already reserved or others  ---------------------------
				//prepare SQL to get all dates from reservation table
                $sql2 = "SELECT date		
                  FROM reservations";
                  
                  $mySQLdate = $newDate1->format($format); 

             // submit the query and capture the result 
             $result = $conn->query($sql2) or die(mysqli_error()); 
             // find out how many records were retrieved 
             $numRows = $result->num_rows; 
             
             while ($row = $result->fetch_assoc()) { 	//loop through each record
                if ($mySQLdate == $row['date'])			//if user date matches a date in the table
                { $errors['duplicatedate'] = true;			
                }
              }

             //----------------------------------------------------------------------------------- 
		}
        
        //########################################################################   
	
	//time validation  #############################################   
	
	if ((isset($_POST['hour1'])) && (isset($_POST['min1'])) && (isset($_POST['ampm'])))  //process time if hours,mins, 
		{                                                                                // and am:pm is set
			$hour1 = (int) $_POST['hour1'];      // get hours from user input - parse to integer
			$min1 = (int) $_POST['min1'];        // get minutes from user input - parse to integer    
			$ampm =  $_POST['ampm'] ;            // get am:pm from user input    
		    
		    $newTime = new DateTime("1/25/2012"); //declare a DateTime object, can use any date, only the time will be used here
		                                          // but for here we use Jan. 25 2012 
                                                  
                                                  //declaring a date sets it automatically to 00:00:00 in 24-hour format (12 midnight)
		    if ($ampm == 'AM')
		    { if ($hour1 == 12) { $newTime->modify("- 12 hours ");   }    }            // if 12 AM (12 midnight), deduct 12 hours
		    elseif (($ampm == 'PM') && ($hour1 != 12))				// if PM (but not 12 PM)
		    {$newTime->modify("+ 12 hours ");	}		   //   add 12 hours
		                                                             
		    $newTime->modify("+ $hour1 hours ");           // add the user-input hours
		    $newTime->modify("+ $min1 minutes ");          // add the user-input minutes
		

        //checking of time range  ------------------------------------------------
        
        $lowerLimit = new DateTime("1/25/2012");
        $lowerLimit->modify("+ 8 hours ");              //8 AM lower limit
		
        $upperLimit = new DateTime("1/25/2012");
		$upperLimit->modify("+ 22 hours ");             //10 PM upper limit
        

        if(($newTime < $lowerLimit) || ($newTime > $upperLimit))    // if lower than lower limit or higher than upper limit, error
        {
            $errors['time'] = true;
        }
        
        $eventTime =  $newTime->format('h:i A');              //12-hour format with am-pm
        
        $_POST['eventtime'] = $eventTime;                     //finalize eventtime for email
        
        $eventTime24 =  $newTime->format('H:i:s');            //finalize eventtime for database, mysql needs 24-hour format
		
		$_POST['eventtime24'] = $eventTime24;

		}
        
        //-------------------------------------------------------------------- 

		
//get form input from POST array and assign them one by one to a variable								
foreach ($_POST as $key => $value) { 
	  // assign to temporary variable and strip whitespace if not an array 
	  $temp = is_array($value) ? $value : trim($value); 
	  // if empty and required, add to $missing array 
	  if (empty($temp) && in_array($key, $required)) { 
		$missing[] = $key; 
		${$key} = '';        //errata update
							//prevents 'undefined var' warning
	  } elseif (in_array($key, $expected)) { 
		// otherwise, assign to a variable of the same name as $key 
		${$key} = $temp; 
	  } 
	}
}

if (isset($_POST['finish']))		// if finish is clicked, transaction must be saved to database and sent to email
{
		
//eliminate magic quotes, which is enabled in 000webhost.com
include('./includes/nuke_magic_quotes.php')   ;

//set e-mail address to send reservations to
$to = 		'camillejoane@yahoo.com';
$subject =  'Reservation from amazingCatering';

//additional headers
$headers = "Content-Type: text/plain; charset=utf-8\r\n";
$headers .= "From: amazingCatering_Reservation <reservation@amazingCatering.com>";


//reservation processing script
require('./includes/processreservation.inc.php');   

if ($reservationSaved && $messageSaved)		//if successfully saved, redirect to thank you page
{header('Location: ./thank_you/thank_you.php');}
else
{
echo $messageNotSaved;
echo $sql;
}

//echo "<pre>";			//used for testing code
//print_r($_SESSION);
//echo "</pre>";		
}


?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  
 <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <script type="text/javascript" src="./js/form_init.js" id="form_init_script"
    data-name="">
    </script>
    <link rel="stylesheet" type="text/css" href="default.css"
    id="theme" />
    <title>
      Reservation Page
    </title>
    
		<style type="text/css">
 
				body {
					background-color: pink;	
				}
				
				div#container
				{
				   width: 1024px;
				   position: relative;
				   margin-top: 0px;
				   margin-left: auto;
				   margin-right: auto;
				   text-align: left;
				}	
				
					table {
						border-collapse: separate;
					}
				
				td.rytcol {
					margin-top:20px;
					font: bold 15px sans-serif;
					color:blue;
					padding:10px;
					border-width:3px;
					border-color: purple;"
				}
				
				td.head {
					margin-top:20px;
					font: bold 15px sans-serif;
					color:#0000a0;
					border-width:3px;
					padding:10px;"
				}
				
				td.package {
					margin-top:20px;
					font: bold 15px sans-serif;
					color:#0000a0;
					border-width:3px;
					padding:10px;"
				}
				
				td, th {
					border-width:3px;
					border-color: purple;
				}
				
					span.warning
					{
						color:red;
						font: bold 14px "Lucida Sans Unicode", "Lucida Grande", sans-serif;
					}
					
					p.warning
					{
						color:blue;
						font: bold 14px "Lucida Sans Unicode", "Lucida Grande", sans-serif;
						margin-left:30px;
					}
			
					#overlay {
						 visibility: hidden;
						 position: absolute;
						 left: 0px;
						 top: 0px;
						 width:100%;
						 height:100%;
						 z-index: 1000;
						 /*background-image:url('./images/background-trans.png');*/
						 background-color:pink;
						 opacity:0.9;
					}
					
					
					#overlay div {
						 width:500px;
						 margin: 100px auto;
						 background-color: #fff;
						 border:1px solid #000;
						 padding:20px;
					}
					
				a:link,
				a:visited
						{
						color:blue;
						text-decoration:none;
						}
			
				a:hover,
				a:active
						{
						color: white;
						text-decoration:underline;
						}			
 
		</style>
	
</head>
 
<body>
		
<div id="container">
			
<div style="width:800px;
	    margin: 20px auto;">
		

  <?php
			############################ if there are ERRORS, inform user ############################ 
  
	if ($_POST && isset($errors['database']))		// some errors in database
	{ ?>  
		<p style="color:white; font:12px bold sans-serif;">Sorry, the reservation is not saved.<br/>Please try again later.</p>
		<?php 
		echo $errors['database'];
	}
	elseif ($missing || $errors)				// there are some required fields not filled, or incorrectly filled
	{ ?>
		<p style="color:white; font:bold 14px sans-serif;">
			Please fix the item(s) indicated.
		</p>

	    <?php
		
		if ($missing && in_array('hTotalPrice',$missing))		// Total price is not processed
		{ ?>
				<p style="color:red; font:bold 14px sans-serif;">
					Please total the items by clicking Total below the form.
				</p>		
				
		<?php }
			
		//echo "<pre>";			//used for testing code
		//print_r($missing);
		//print_r($errors);
		//echo "</pre>";
		
		
		######################################################################################
		
	} ?>
	
<!--------------------------- USERNAME and LOGOUT ------------------------------->

<div style="border: 5px purple groove;
		display:block;
		color:purple;
		font: bold 15px  'Lucida Sans Unicode', 'Lucida Grande', sans-serif;
		background-color:pink;
		text-align:center;
		margin:10px auto;
		height:150px;">

<?php

    echo "You are logged in, <u>" . $_SESSION['username'] . "</u>";            //display username
    
    include('./includes/user_logout.inc.php');        // log out button
        
?>

<center>
<div style="padding:10px;">
<a href="./memberpage.php">
<img src="./images/cooltext774339533.png" onmouseover="this.src='./images/cooltext774339533MouseOver.png';" onmouseout="this.src='./images/cooltext774339533.png';" />
</a>


</div>

<div><a onclick="overlay()" style="cursor: pointer; font-size: 12px; ">Reservation Agreement</a>
</div>
</center>

</div>

<!----------------------- end of USERNAME and LOGOUT -------------------------------->

<?php

//display form only if not submitted yet, or there are missing fields or errors

if (!isset($_POST['send']) || $missing  ||  $errors   )
{	

?>

<!--############################ FORM ###############################-->
    
    <form style="BACKGROUND-COLOR: #f0daf0; MARGIN: 5px auto; WIDTH: 750px; FONT-SIZE: 13px"
    id="docContainer" class="fb-toplabel fb-100-item-column fb-large selected-object" 
    enctype="multipart/form-data" method="post" action="" novalidate="novalidate" data-margin="custom">
	    <div style="MIN-HEIGHT: 20px" id="fb-form-header1" class="fb-form-header">
			<a id="fb-link-logo1" class="fb-link-logo" href="" target="_blank">
				<img style="DISPLAY: none" id="fb-logo1" class="fb-logo" title="Alternative text" alt="Alternative text" src="common/images/image_default.png"/>
			</a>
	    </div>
		<div id="section1" class="section">
			<div id="column1" class="column ui-sortable">
			  <div style="FILTER: " id="item1" class="fb-item fb-100-item-column">
				<div class="fb-header">
				   <h2 style="DISPLAY: inline">
					AMAZING COVERAGE and CATERING
				  </h2>
				  <h2 style="DISPLAY: inline">
					Reservation Form
				  </h2>
				</div>
			  </div>
			  
			<div id="item43" class="fb-item">
			  <div class="fb-sectionbreak">
			    <hr>
			  </div>
			</div>
			
			
			  <!--###########################-- PACKAGE CONTENTS --###################################---->
			  
			  <div style="FILTER: " id="item10" class="fb-item fb-50-item-column">
				<div style="FILTER: " id="item40" class="fb-item fb-100-item-column">
				  <div class="fb-header">
				    <h2 style="DISPLAY: inline; COLOR: #b058b0">
				      Package Contents
				    </h2>
				  </div>
				</div>	

				<div style="margin: 20px auto; width: 650px; border:3px purple outset;">
					
					<table cellpadding=5px cellspacing=5px width=100% border=2>
						<tr>
						<th >Inclusion</th>
						<th >Amenities</th>
						</tr>
						
						<tr>
							<td>
								<ul style="list-style: square; padding-left:20px">
									<li>5 Main Courses</li>
									<li>1 Pasta </li>
									<li>Steamed Rice </li>
									<li>1 Dessert </li>
									<li>Iced Tea </li>
									<li>Mineral Water</li>	
								</ul>

							</td>
							
							<td>
								<ul style="list-style: disc; padding-left:20px">
									<li>Elegant Buffet set up with drop light and props </li>
									<li>8 Seater Round Table with Cover table settings </li>
									<li>Chair with cover and ribbon following color motif </li>
									<li>Complete catering equipment & utensils for 100 guest </li>
									<li>Mirror table top with centerpiece and table number </li>
									<li>Special stage light	</li>
									
								</ul>
							</td>
							
						</tr>
						
						<tr>
							<th colspan ="3">Freebies</th>	
								
						</tr>
						
						<tr>
								<td colspan="2">
								<ul style="list-style:  circle; padding-left:20px">
									<li>100 Person = Free Video Coverage</li>
									<li>150 Person = Free Video Coverage + 2 Layer Cake</li>
									<li>200 Person = Free Video Coverage + 2 Layer Cake + Bubble Machine</li>	
									<li>300 Person = Free Video Coverage + 2 Layer Cake + Bubble Machine + 50 Pcs. Cupcake
										+ Bubble Machine and Chocolate Fountain</li>	
								</ul>
								</td>
						</tr>

					</table>
				     
				</div>
				
				
				<!--###########################-- end of PACKAGE CONTENTS --###################################---->


				<!--################ NUM of GUESTS and FREEBIE COMPUTATION ###############################-->				
					
				<div style="FILTER: " id="item40" >
				  <div class="fb-header">
				    <h2 style="DISPLAY: inline; COLOR: #b058b0">
				      Number of Guests
				    </h2>
					
				   <div style="width:1000px; margin-top:10px;">
					* P 300 per head <br />
					* minimum of 100 persons <br />
					* Please enter number of guests and click Compute before proceeding to the next sections
				  </div>
				  </div>
				</div>
				
				<table cellpadding=="2px" cellspacing="2px" width="500px">

				<tr>
						
						<td valign="top" height="100px" width="100px">
						
				<div id="item27" style="width:100px">
						<!------------------- NUM of GUESTS field ---------------
							executes JavaScript function 	getFreebies()-->
						<div class="fb-input-number" style="margin-top:10px;">
						  <input id="txtNumGuests" name="txtNumGuests" data-hint="" step="1" max="99999"
						  min="100" autocomplete="off" required type="number" onchange="getFreebies()" 
								 
						<?php if ($missing || $errors)
						{echo 'value="'.htmlentities($txtNumGuests,ENT_COMPAT,'UTF-8').'"';} ?>
								 />
						</div>
					       
				</div>
				
						</td>
						
						<td  valign="top" height="100px" width="100px">
				
				<div id="item27" style="margin:20px;">	
				  <!------------------- COMPUTE NUM of GUESTS button --------------
							executes JavaScript function 	funcProcessNumGuests()-->
				  <div style="width:100px;height:20px; border: 2px solid purple; font: bold 14px sans-serif; text-align: center"
					   onclick="funcProcessNumGuests()"
					   onmouseover="this.style.color='purple';this.style.backgroundColor='white';this.style.cursor='pointer'"
					   onmouseout="this.style.color='black';this.style.backgroundColor='transparent'">
						Compute
				   </div>	
				</div>
				
						</td>
						
						<td  valign="top" height="100px"
							 style="padding-top:20px;
										font: bold 18px sans-serif;
										color: red;
										width:300px">
								
						<!------- LABEL that displays Guest Price if computed --->
						<span id="lblGuestPrice" name="lblGuestPrice">
						<?php if ($missing || $errors)			//display value if already entered
						{echo htmlentities("Price: P " . $hGuestPrice,ENT_COMPAT,'UTF-8');} ?>									
						</span>
						
						<!------- HIDDEN INPUT used to pass the Guest Price to the POST array --->
						<input type="hidden" name="hGuestPrice" id="hGuestPrice"
						<?php if ($missing || $errors)			//set value if already entered
						{echo 'value="'.htmlentities($hGuestPrice,ENT_COMPAT,'UTF-8').'"';} ?>
							   />
				
						</td>
				
				</tr>
				
				</table>
       
				<script>
				
				//#############################  JAVASCRIPT #################################
				//##################### this block of code computes the Guest Price and
				//##################### sets the freebies
		
				var gGuestPrice = 0;			//initialize variables
				var gNumGuests = 0;
				var gVideoPrice = 0;
				var g2LayerCakePrice = 0;
				var gChocoFountainPrice = 0;
				
				var gTotalPrice = 0;
		  
					       function funcProcessNumGuests()		
					       {
					       var txtNumGuests = document.getElementById("txtNumGuests");	
					       var number = txtNumGuests.value;				
					       var str1 = "Price: P ";
						   
						   gNumGuests = number;
						   
						   
						   if (number >=100)
						   {
						   gGuestPrice = number*300;
					       document.getElementById("lblGuestPrice").innerHTML= str1.concat((gGuestPrice));
						   document.getElementById("hGuestPrice").value = gGuestPrice;
						   }
						   else
						   {
								document.getElementById("lblGuestPrice").innerHTML = '';
								alert('Guests must be at least 100');
						   }

					       //document.getElementById("here").innerHTML= (number*300);
					       }
					       
					       function getFreebies()
					       {
								var txtNumGuests = document.getElementById("txtNumGuests");
								var number = txtNumGuests.value;
								
								
								//video Coverage freebie
								if (number >= 100)
								{
									//document.write('FREE');
									gVideoPrice = 0;
									document.getElementById("VideoPrice").innerHTML= 'FREE';
									document.getElementById("VideoPrice").style.color = 'blue';
									//document.getElementById("VideoPrice").style.textDecoration = 'blink';									
								}
								else
								{
									//document.write('P 3,500');
									gVideoPrice = 3500;
									document.getElementById("VideoPrice").innerHTML= 'P 3,500';
									document.getElementById("VideoPrice").style.textDecoration = 'none';
								}
								
								//2 Layer Cake freebie
								if (number >= 150)
								{
									//document.write('FREE');
									g2LayerCakePrice = 0;
									document.getElementById("2LayerCakePrice").innerHTML= 'FREE';
									document.getElementById("2LayerCakePrice").style.color = 'blue';
									//document.getElementById("2LayerCakePrice").style.textDecoration = 'blink';
									
									document.getElementById("h2LayerCakePrice").value= 0;
									
								}
								else
								{
									//document.write('P 3,500');
									g2LayerCakePrice = 2800;
									document.getElementById("2LayerCakePrice").innerHTML= 'P 2,800';
									document.getElementById("2LayerCakePrice").style.textDecoration = 'none';
									
									document.getElementById("h2LayerCakePrice").value= 2800;
								}							


								//Bubble Machine Freebie
								
								
								if (number >=200)
								{
								    //gBubbleMachine = 0;
									document.getElementById("TDfreeBubble").innerHTML= '<td><input type="checkbox" name="extras[]" value="freeBubble" id="freeBubble" onclick="highlightSelectedE()" <?php if ($_POST && in_array("freeBubble",$_POST["extras"])) {echo "checked";}?> > Bubble Machine</td><td> Package Freebie</td><td align="right" class="rytcol" id="freeBubblePrice">FREE</td>';	
								    document.getElementById("freeBubblePrice").style.color = 'blue';
									//document.getElementById("freeBubblePrice").style.textDecoration = 'blink';
								}
								else
								{
								    //gBubbleMachine = 0;
									document.getElementById("TDfreeBubble").innerHTML='';	
								}						
								
								//Cupcake and Choco Fountain Freebie
								
								
								if (number >=300)
								{
								    
									document.getElementById("TDfreeCupcake").innerHTML= '<td><input type="checkbox" name="cakes[]" value="freeCupcake" id="freeCupcake" onclick="highlightSelectedC()" <?php if ($_POST && in_array("freeCupcake",$_POST["cakes"])) {echo "checked";}?>> 50 pcs. Cupcake </td><td> Package Freebie</td><td align="right" class="rytcol" id="freeCupcakePrice">FREE</td>';	
								    document.getElementById("freeCupcakePrice").style.color = 'blue';
									//document.getElementById("freeCupcakePrice").style.textDecoration = 'blink';
									
									
									gChocoFountainPrice = 0;
									document.getElementById("ChocoFountainPrice").innerHTML= 'FREE';
								    document.getElementById("ChocoFountainPrice").style.color = 'blue';
									//document.getElementById("ChocoFountainPrice").style.textDecoration = 'blink';
									
									
									document.getElementById("hChocoFountainPrice").value = 0;
								}
								else
								{
								    gChocoFountainPrice = 1500;
									
									document.getElementById("TDfreeCupcake").innerHTML='';
									
									document.getElementById("ChocoFountainPrice").innerHTML= 'P 1,500';
									document.getElementById("ChocoFountainPrice").style.textDecoration = 'none';
									
									document.getElementById("hChocoFountainPrice").value = 1500;
								}						
								
								
					       }
					       
				</script>				 
       				       
				

				<!--############################## end of NUM of GUESTS and FREEBIE COMPUTATION ##############################-->

			<div id="item39" class="fb-item">
			  <div class="fb-sectionbreak">
			    <hr>
			  </div>
			</div>
				
				<!-- ########################## EVENT TYPE DROPDOWN #################### -->
				
				<div style="FILTER: " id="item40" class="fb-item fb-100-item-column">
				  <div class="fb-header">
				    <h2 style="DISPLAY: inline; COLOR: #b058b0">
				      Event Type
					</h2>
				   <div style="width:1000px; margin-top:10px;">
					* Event type determines the design and theme of the event.  	
				  </div>
 
				  </div>

				</div>
				
						<?php		// if event type not set
						if ($missing && in_array('event',$missing))
						{ ?>
						<span class="warning">Please supply the type of event</span>
							
						<?php } ?>
						
						
				<br /><br/>
					
				 <!-- ########################## EVENT TYPE RADIO group ################# -->
				
				<table cellpadding="2px" cellspacing="5px">
						
						<tr ><td>
						
						<?php	//each PHP code inside the radio input checks if that radio button is selected,
								//if it is, set 'checked' attribute (HTML) to retain value
						?>
						
						<input type="radio" name="event" value="Kiddie" id="Kiddie"
								<?php if ($_POST && $_POST['event'] == 'Kiddie')
								         {echo 'checked';} ?>  />
						<label for="Kiddie">Kiddie Party</label>
				
						</td></tr>
				
						<tr><td>
						
						<input type="radio" name="event" value="Christening" id="Christening"
							   	<?php if ($_POST && $_POST['event'] == 'Christening')
								         {echo 'checked';} ?>  />
						<label for="Christening" >Christening</label>

						</td></tr>
						
						<tr><td>						

						<input type="radio" name="event" value="Debut" id="Debut"
								<?php if ($_POST && $_POST['event'] == 'Debut')
								         {echo 'checked';} ?>  />
						<label for="Debut" >Debut</label>

						</td></tr>
						
						<tr><td>						

						<input type="radio" name="event" value="Wedding" id="Wedding"
								<?php if ($_POST && $_POST['event'] == 'Wedding')
								         {echo 'checked';} ?>  />							   
						<label for="Wedding" >Wedding</label>

						</td></tr>

						<tr><td>
						
						<input type="radio" name="event" value="Anniversary" id="Anniversary"
								<?php if ($_POST && $_POST['event'] == 'Anniversary')
								         {echo 'checked';} ?>  />							   
						<label for="Anniversary" >Anniversary</label>

						</td></tr>
						
						<tr valign="middle"><td>						
		
						<input type="radio" name="event" value="Other" id="Other"
   
								<?php 			//'Other' selected
								if ($_POST && ($_POST['event'] == 'Other' || $otherError))
								{echo 'checked';} ?>		>					   
								Others, pls. specify:
						<input type="text" name="other_event" id="other_event" class="fb-item fb-33-item-column">

						</td></tr>
						
				</table>
						
			  </div>
			  
			  <!-- ########################## end of EVENT TYPE RADIO group ################# -->
			
			
			<div id="item39" class="fb-item">
			  <div class="fb-sectionbreak">
			    <hr>
			  </div>
			</div>
			
			<div style="FILTER: " id="item40" class="fb-item fb-100-item-column">
			  <div class="fb-header">
			    <h2 style="DISPLAY: inline; COLOR: #b058b0">
			      Schedule
			    </h2>
			  </div>
			</div>			
			  
				<!------------------------------ location ---------------------------->
			  <div style="FILTER: " id="item5" class="fb-item fb-100-item-column">
				<div class="fb-grouplabel">
				  <label style="DISPLAY: inline" id="item5_label_0">Location</label>
				</div>
				<div class="fb-input-box">
				  <input id="item5_text_1" maxlength="100" name="location" placeholder="" autocomplete="off"
				  data-hint="" required type="text" <?php 
				//display value if this field is filled, but there are other missing fields or there are errors
				if ($missing || $errors)
				{echo 'value="'.htmlentities($location,ENT_COMPAT,'UTF-8').'"';}
					?>/>
				</div>
			  </div>
			  <!-------------------------------------------------------------------->

			  <!----------------------- event DATE--------------------------->
			  
			<div style="FILTER: " id="item20" class="fb-item fb-20-item-column">
			  <div class="fb-header">
			    <h2 style="FONT-STYLE: normal; DISPLAY: inline; FONT-FAMILY: ; FONT-SIZE: 15px; FONT-WEIGHT: bold">
			      Date
			    </h2>
			  </div>
			  
			  	<div style="float:left; font-weight:bold; color:red; width:150px;">
					<?php if (isset($errors['pastdate'])) 		//warn if user entered a date from past
							{ echo 'Cannot set a date from past'; }
						 else if (isset($errors['toosoondate']))  //warn if user entered a date 1 week from now
							{ echo 'Cannot set a date 1 week from now'; }
						 else if (isset($errors['duplicatedate'])) 	 //warn if user entered a reserved date
							{ echo 'Sorry. That date has been reserved by other customers. <br/> Please pick another date.'; }
					
					?>
				</div>
			</div>
			
			<div style="FILTER: ; HEIGHT: 20px" id="item47" class="fb-item fb-15-item-column">
			  <div class="fb-spacer">
			    <div id="item47_div_0">
			    </div>
			  </div>
			</div>			

			<div style="FILTER: " id="item45" class="fb-item fb-50-item-column">
			  <div class="fb-header">
			    <h2 style="DISPLAY: inline; FONT-SIZE: 15px">
			      Time
			    </h2>
			  </div>
		
				<div style="float:left; font-weight:bold; color:red; width:500px;">
					<?php if (isset($errors['time'])) 		//warn if user entered a time outside the range
							{ echo 'Time not accepted. Choose a time from 8:00 AM to 10:00 PM. '; }
					?>
				</div>
			</div>
			

			  <div style="FILTER: " id="item21" class="fb-item fb-20-item-column">
				<div class="fb-grouplabel">
				  <label style="DISPLAY: inline" id="item21_label_0">mm/dd/yy</label>
				</div>
				<div class="fb-input-date">
				  <input id="item21_date_1" class="datepicker" name="date" data-hint=""
				  required type="date"  <?php 
				//display value if this field is filled, but there are other missing fields or there are errors
				if ($missing || $errors)
				{echo 'value="'.htmlentities($date,ENT_COMPAT,'UTF-8').'"';}
					?> />
				</div>
				
			  </div>
			  
			<div style="FILTER: ; HEIGHT: 50px" id="item46" class="fb-item fb-15-item-column">
			    <div class="fb-spacer">
			      <div id="item46_div_0">
			      </div>
			    </div>
			</div>
					  
			  <!-------------------------------------------------------------------->
			  
			  <!-- ###########################  event TIME ###########################--->
			  	
			  <!--------------------------- hour ----------------------------------->
			  		  
			  
			  <div style="FILTER: " id="item17" class="fb-item fb-20-item-column">
				<div class="fb-grouplabel">
				  <label style="DISPLAY: inline" id="item17_label_0">Hour</label>
				</div>
				<div class="fb-input-number">
				  <input id="item17_number_1" name="hour1" min="1" max="12" autocomplete="off" required
				  data-hint="" step="1" type="number" <?php 
					//display value if this field is filled, but there are other missing fields or there are errors
					if ($missing || $errors)
						{echo 'value="'.htmlentities($hour1,ENT_COMPAT,'UTF-8').'"';}
					?>/>
				</div>
			  </div>
			  <!--------------------------- minute ----------------------------------->
			  <div id="item18" class="fb-item fb-20-item-column">
				<div class="fb-grouplabel">
				  <label style="DISPLAY: inline" id="item18_label_0">Minute</label>
				</div>
				<div class="fb-input-number">
				  <input id="item18_number_1" name="min1" min="0" max="59" autocomplete="off" required
				  data-hint="" step="1" type="number" <?php 
					//display value if this field is filled, but there are other missing fields or there are errors
					if ($missing || $errors)
						{echo 'value="'.htmlentities($min1,ENT_COMPAT,'UTF-8').'"';}
					?>/>
				</div>
			  </div>
			  <!--------------------------- AM/PM ----------------------------------->
				<div style="PADDING-LEFT: 10px" id="item22" class="fb-item fb-25-item-column">
					<div class="fb-grouplabel">
					  <label style="DISPLAY: inline" id="item22_label_0">AM / PM</label>
					</div>
					<div class="fb-dropdown">
					  <select id="item22_select_1" name="ampm" data-hint="" required>
					  
						<?php // determine selected option from the drop-down menu
							  // attach 'selected' for the option selected
						?>
					  
						<option id="item22_0_option" value="" <?php
								if ($_POST == false || $_POST['ampm'] == '')
								{echo 'selected ';} ?> >
						  Choose one
						</option>
						<option id="item22_1_option" value="AM" <?php
								if ($_POST == true && $_POST['ampm'] == 'AM')
								{echo 'selected';} ?>>
						  AM
						</option>
						<option id="item22_2_option" value="PM" <?php
								if ($_POST == true && $_POST['ampm'] == 'PM')
								{
								echo 'selected';
								} ?>>
						  PM
						</option>
					  </select>
					</div>
				</div>
			    <!-- ##################### end of event TIME #############################---->
				  
			<div id="item43" class="fb-item">
			  <div class="fb-sectionbreak">
			    <hr>
			  </div>
			</div>
			
			<!--############################################################################-->
			<!--########################### CONTENTS/ADDITIONS table #####################-->
			
			<?php	//each of the checkbox has PHP code that gets the status of the checkbox
				// if it is checked, set 'checked' attribute (HTML) to retain value
			?>
			 
			<table width=650px border=2 cellpadding=5px cellspacing=2px>
				
				<!------------- coverage ----------->
				
				<tr style="font: bold 20px sans-serif; color: purple;">
					<th colspan=3>Contents / Additions
					</th>
					<th width="70px">Price
					</th>
				</tr>

				<tr align="left" valign="middle">
					
					<td class="head" width="80px" rowspan="2">Coverage
					</td>
										
					<td><input type="checkbox" name="coverage[]" value="Video" id="Video" onclick="highlightSelectedCo()"
							<?php if ($_POST && in_array('Video',$_POST['coverage'])) {echo 'checked';}?>
					> Video</td>
					<td align="left">* 2 copies of edited DVD
					</td>
					<td align="right" class="rytcol" id="videoPrice">
						<div id="VideoPrice">
						<?php if($freeVideo)		//check boolean
						{ echo 'FREE';}
						else
						{ echo 'P 3,500';}	?>									
						</div>
					</td>
					
				</tr>
				
				<tr align="left" valign="middle">
					<td><input type="checkbox" name="coverage[]" value="Photo" id="Photo" onclick="highlightSelectedCo()"
								<?php if ($_POST && in_array('Photo',$_POST['coverage'])) {echo 'checked';}?>					
					> Photo</td>
					<td align="left">* Unlimited shots saved to CD/DVD <br/>
						* 100 pcs selected pictures with Album <br/>
						* 2x3 Tarpaulin and 11x14 Signature Frame <br/>
					</td>
					<td align="right" class="rytcol" id="PhotoPrice">P 7,000
					</td>
				</tr>

				<!------------- end of coverage ----------->
				
				
				<script>
				
				//#############################  JAVASCRIPT #################################
				//##################### this block of code highlights the item price that is checked
				//##################### highlight color used is RED		
				
				function highlightSelectedCo()
				{
						
					 var coverage = document.getElementsByName('coverage[]');
	 
					 for (var i=0, len=coverage.length; i<len; ++i)
					 {
						
						var chkBoxValue = String(coverage[i].value);
						var tdID = chkBoxValue.concat('Price');						
						
						if (coverage[i].checked)
						{
							document.getElementById(tdID).style.color = 'red' ;
							document.getElementById(tdID).style.textDecoration = 'none';
						}
						else
						{
							document.getElementById(tdID).style.color = 'blue' ;
							document.getElementById(tdID).style.textDecoration = 'none';
						}
					 }
				}

				</script>					
				
				<!--------------- extras ------------------->
				
				<tr valign="middle">
				
					<td class="head" width="80px" rowspan="6">Extras
					</td>
					
					<td colspan="2">
						<input type="checkbox" name="extras[]" value="PhotoBooth" id="PhotoBooth" onclick="highlightSelectedE()"
								<?php if ($_POST && in_array('PhotoBooth',$_POST['extras'])) {echo 'checked';}?>						
						> Photo Booth
					</td>
					
					<td align="right" class="rytcol" id="PhotoBoothPrice">P 7,500
					</td>						
			        	
                </tr>
				
				<tr align="left" valign="middle">
					<td colspan="2">
						<input type="checkbox" name="extras[]" value="ProjectorRental" id="ProjectorRental" onclick="highlightSelectedE()"
								<?php if ($_POST && in_array('ProjectorRental',$_POST['extras'])) {echo 'checked';}?>							
						> Projector Rental
					</td>
					
					<td align="right" class="rytcol" id="ProjectorRentalPrice">P 1,500
					</td>	
					
				</tr>
				
				<tr align="left" valign="middle">
					<td colspan="2">
						<input type="checkbox" name="extras[]" value="SoundSystem" id="SoundSystem" onclick="highlightSelectedE()"
								<?php if ($_POST && in_array('SoundSystem',$_POST['extras'])) {echo 'checked';}?>						
						> Sound System
					</td>
					
					<td align="right" class="rytcol" id="SoundSystemPrice">P 3,000
					</td>	
					
				</tr>
				
				<tr align="left" valign="middle">
					<td colspan="2">
						<input type="checkbox" name="extras[]" value="Floral" id="Floral" onclick="highlightSelectedE()"
								<?php if ($_POST && in_array('Floral',$_POST['extras'])) {echo 'checked';}?>
						> Floral Arrangements
					</td>
					
					<td align="right" class="rytcol" id="FloralPrice">P 10,000
					</td>	
					
				</tr>
				
				<tr align="left" valign="middle">
					<td>
						<input type="checkbox" name="extras[]" value="Balloons" id="Balloons" onclick="highlightSelectedE()"
								<?php if ($_POST && in_array('Balloons',$_POST['extras'])) {echo 'checked';}?>
						> Balloon Arrangements
					</td>
					
					<td>* 1 pair Pillar <br />
						* Cake Arc <br />
						* Balloonderitas <br /> 
						* Balloons Center Table  <br />
					</td>
					
					<td align="right" class="rytcol" id="BalloonsPrice">P 10,000
					</td>	
					
				</tr>
				
				<!--<<<<<<<<<<<<<<<<<<<<< Bubble Machine Freebie >>>>>>>>>>>>>>>>>>>>>-->
				<!-- This checkbox only appears based on certain conditions on Guest Num/Price -->
				
				<tr align="left" valign="middle" id="TDfreeBubble">
						<?php if($freeBubbleMachine) //check boolean
						{ echo '<td><input type="checkbox" name="extras[]" value="freeBubble" id="freeBubble" onclick="highlightSelectedE()"> Bubble Machine</td><td> Package Freebie</td><td align="right" class="rytcol" id="freeBubblePrice">FREE</td>';}
						else
						{ echo '';}	 ?>					
				</tr>
				
				<!--<<<<<<<<<<<<<<<<<<<<< end of Bubble Machine Freebie >>>>>>>>>>>>>>>>>>>>>-->
				
				<!---------- end of extras -------------------->
				
				<script>
				
				//#############################  JAVASCRIPT #################################
				//##################### this block of code highlights the item price that is checked
				//##################### highlight color used is RED					
				
				function highlightSelectedE()
				{
						
					 var extras = document.getElementsByName('extras[]');
	 
					 for (var i=0, len=extras.length; i<len; ++i)
					 {
						
						var chkBoxValue = String(extras[i].value);
						var tdID = chkBoxValue.concat('Price');						
						
						if (extras[i].checked)
						{
							document.getElementById(tdID).style.color = 'red' ;
							document.getElementById(tdID).style.textDecoration = 'none';
						}
						else
						{
							document.getElementById(tdID).style.color = 'blue' ;
							document.getElementById(tdID).style.textDecoration = 'none';
						}
					 }
				}

				</script>				
				
				<!----------- cakes ---------------->
				
				<tr valign="middle">
				
					<td class="head" width="80px" rowspan="4">Cakes
					</td>
					
					<td>
						<input type="checkbox" name="cakes[]" value="2LayerCake" id="2LayerCake" onclick="highlightSelectedC()"
								<?php if ($_POST && in_array('2LayerCake',$_POST['cakes'])) {echo 'checked';}?>
						> 2 Layer Cake
					</td>

					<td>
					</td>				
					
					<td align="right" class="rytcol" id="2LayerCakePrice">
						<?php if($free2LayerCake) //check boolean
						{ echo 'FREE';}
						else
						{ echo 'P 2,800';}	?>						
					</td>
					
					<input type="hidden" name="h2LayerCakePrice" id="h2LayerCakePrice" >
 	
                </tr>
					
				<tr>
						
					<td valign="middle">
						<input type="checkbox" name="cakes[]" value="3LayerCake" id="3LayerCake" onclick="highlightSelectedC()"
								<?php if ($_POST && in_array('3LayerCake',$_POST['cakes'])) {echo 'checked';}?>						
						> 3 Layer Cake
					</td>
					
					<td valign="middle">
						Foundant Cake
					</td>

					<td align="right" class="rytcol" id="3LayerCakePrice">P 5,000
					</td>	
										
				</tr>
				
				<!-- #################### cupcake FREEBIE ############### -->	
				<!-- This checkbox only appears based on certain conditions on Guest Num/Price -->				
						
				<tr align="left" valign="middle" id="TDfreeCupcake">
						<?php if($free50Cupcakes) //check boolean
						{ echo '<td><input type="checkbox" name="cakes[]" value="freeCupcake" id="freeCupcake" onclick="highlightSelectedC()"> 50 pcs. Cupcake </td><td> Package Freebie</td><td align="right" class="rytcol" id="freeCupcakePrice">FREE</td>';}
						else
						{ echo '';}	?>						
				</tr>
				
				<!-- #################### end of cupcake FREEBIE ############### -->						
				
				<tr align="left" valign="middle">
					
					<td>
						<input type="checkbox" name="cakes[]" value="cupcake" id="cupcake" onclick="highlightSelectedC()"> Cup Cakes (P 40 ea.)
						<br/><br/>
						minimum of 50 pieces
					</td>
					
					<td>

						<div id="item27" class="fb-item fb-100-item-column" style="margin-top:20px;">
								Click Compute after entering a value here
						 <div class="fb-input-number"">
						   <input id="cupcakes" name="cupcakes" step="1" max="99999"
						   min="50" autocomplete="off" type="number" onclick="highlightSelectedC()"
								<?php if ($missing || $errors)
								{echo 'value="'.htmlentities($cupcakes,ENT_COMPAT,'UTF-8').'"';} ?>
						   />
 
						 </div>
						</div>
					       
					       <div id="item27" class="fb-item fb-33-item-column" style="margin-top:20px;">	
								
								<!---------- COMPUTE CUPCAKES button -->
								<!---- executes the JavaScript function computeCupcakes() -->
								<div style="width:100px;height:20px; border: 2px solid purple; font: bold 14px sans-serif; text-align: center"
									 onclick="computeCupcakes()"
									 onmouseover="this.style.color='purple';this.style.backgroundColor='white';this.style.cursor='pointer'"
									 onmouseout="this.style.color='black';this.style.backgroundColor='transparent'">
									  Compute
								 </div>	

					       </div>
       
					       <script>
						   
						//#############################  JAVASCRIPT #################################
						//##################### this block of code computes the Cupcake equivalent price
						//#################### based on the Num of Cupcakes input
					       
					       var cupcakeEquiv=0;
					       
					       function computeCupcakes()
					       {
					       var numC = document.getElementById("cupcakes").value;
					       var strC = "P ";

						   if (Number(numC) < 50)
						   {
								document.getElementById("cupcakes").focus;
								alert('Cupcakes must be at least 50 pcs');
								
								document.getElementById("cupcakePrice").innerHTML= "P 0";
								
								document.getElementById("hCupcakePrice").value = "0";	
								
						   }
						   else
						   {
					       cupcakeEquiv = numC * 40;

					       //x=x+300;
					       document.getElementById("cupcakePrice").innerHTML= strC.concat((cupcakeEquiv));
						   
						   document.getElementById("hCupcakePrice").value = cupcakeEquiv;
						   
					       //document.getElementById("here").innerHTML= (number*300);	
										
						   }

					       }
					       </script>	
						
						
					</td>
					
					<td align="right" class="rytcol" id="cupcakePrice">
						<div id="cupcakePrice">
								<?php if ($missing || $errors)
								{
										if (isset($hCupcakePrice))
										{
										echo htmlentities("P " . $hCupcakePrice,ENT_COMPAT,'UTF-8');
										}
										} ?>									
						</div>
						
						<input type="hidden" name="hCupcakePrice" id="hCupcakePrice"
								<?php if ($missing || $errors)
								{
										if (isset($hCupcakePrice))
										{
										echo 'value="'.htmlentities($hCupcakePrice,ENT_COMPAT,'UTF-8').'"';}}
								//else { echo 'value="0"';   }  ?>
									   />						
					</td>	
					
				</tr>
				
				<!----------- end of cakes ------------>
				
				<script>
				
				//#############################  JAVASCRIPT #################################
				//##################### this block of code highlights the item price that is checked
				//##################### highlight color used is RED					
				
				function highlightSelectedC()
				{
						
					 var cakes = document.getElementsByName('cakes[]');
	 
					 for (var i=0, len=cakes.length; i<len; ++i)
					 {
						
						var chkBoxValue = String(cakes[i].value);
						var tdID = chkBoxValue.concat('Price');						
						
						if (cakes[i].checked)
						{
							document.getElementById(tdID).style.color = 'red' ;
							document.getElementById(tdID).style.textDecoration = 'none';
						}
						else
						{
							document.getElementById(tdID).style.color = 'blue' ;
							document.getElementById(tdID).style.textDecoration = 'none';
						}
					 }
				}

				</script>					
				
				<!----------- treats ------------------>

				<tr valign="middle">
				
					<td class="head" width="80px" rowspan="5">Treats
					</td>
					
					<td>
						<input type="checkbox" name="treats[]" value="ChocoFountain" id="ChocoFountain" onclick="highlightSelectedT()"
								<?php if ($_POST && in_array('ChocoFountain',$_POST['treats'])) {echo 'checked';}?>
						> Chocolate Fountain Rental
					</td>
					
					<td>* 3 ft.
					</td>
					
					<td align="right" class="rytcol" id="ChocoFountainPrice">
						<?php if($freeChocoFountain)	//check boolean
						{ echo 'FREE';}
						else
						{ echo 'P 1,500';}		?>	
					</td>
					
					<input type="hidden" name="hChocoFountainPrice" id="hChocoFountainPrice">
	
			        	
                </tr>
				
				<tr align="left" valign="middle">
					<td>
						<input type="checkbox" name="treats[]" value="PopCorn" id="PopCorn" onclick="highlightSelectedT()"
								<?php if ($_POST && in_array('PopCorn',$_POST['treats'])) {echo 'checked';}?>
						> Pop Corn Cart
					</td>
					
					<td>* good for 150 persons
					</td>
					
					<td align="right" class="rytcol" id="PopCornPrice">P 2,500
					</td>	
					
				</tr>				
				
				<tr align="left" valign="middle">
					<td>
						<input type="checkbox" name="treats[]" value="HotDog" id="HotDog" onclick="highlightSelectedT()"
								<?php if ($_POST && in_array('HotDog',$_POST['treats'])) {echo 'checked';}?>	
						> Hot Dog Stand
					</td>
					
					<td>* good for 100 persons
					</td>
					
					<td align="right" class="rytcol" id="HotDogPrice">P 2,500
					</td>	
					
				</tr>
				
				<tr align="left" valign="middle">
					<td>
						<input type="checkbox" name="treats[]" value="IceCrumble" id="IceCrumble" onclick="highlightSelectedT()"
								<?php if ($_POST && in_array('IceCrumble',$_POST['treats'])) {echo 'checked';}?>
						> Ice Crumble Cart
					</td>
					
					<td>* good for 150 persons
					</td>
					
					<td align="right" class="rytcol" id="IceCrumblePrice">P 2,500
					</td>	
					
				</tr>
				
				<tr align="left" valign="middle">
					<td>
						<input type="checkbox" name="treats[]" value="CottonCandy" id="CottonCandy" onclick="highlightSelectedT()"
								<?php if ($_POST && in_array('CottonCandy',$_POST['treats'])) {echo 'checked';}?>						
						> Cotton Candy 
					</td>
					
					<td>* good for 150 persons
					</td>
					
					<td align="right" class="rytcol" id="CottonCandyPrice">P 2,000
					</td>	
					
				</tr>					
				<!-------------- end of treats ------------>

				<script>
				
				//#############################  JAVASCRIPT #################################
				//##################### this block of code highlights the item price that is checked
				//##################### highlight color used is RED					
				
				function highlightSelectedT()
				{
						
					 var treats = document.getElementsByName('treats[]');
	 
					 for (var i=0, len=treats.length; i<len; ++i)
					 {
						
						var chkBoxValue = String(treats[i].value);
						var tdID = chkBoxValue.concat('Price');						
						
						if (treats[i].checked)
						{
							document.getElementById(tdID).style.color = 'red' ;
							document.getElementById(tdID).style.textDecoration = 'none';
						}
						else
						{
							document.getElementById(tdID).style.color = 'blue' ;
							document.getElementById(tdID).style.textDecoration = 'none';
						}
					 }
				}

				</script>				
				
				<!----------- personnel ------------------>
				
				
				<tr valign="middle">
				
					<td class="head" width="80px" rowspan="6">Personnel
					</td>
					
					<td>
						<input type="checkbox" name="personnel[]" value="Mascot" id="Mascot" onclick="highlightSelected()"
								<?php if ($_POST && in_array('Mascot',$_POST['personnel'])) {echo 'checked';}?>	
						> Mascot
					</td>
					
					
					<td>
						Choose from the available characters: <br />
						
						<!------------ MASCOT Selection DropDown --------------->
						<select name="mascotWho" id="mascotWho" >
								<option value="" <?php if ($_POST == false || $_POST['mascotWho'] == ''){echo 'selected ';} ?>>Select One</option>
								<option value="HelloKitty" <?php if ($_POST == true && $_POST['mascotWho'] == 'HelloKitty'){echo 'selected ';} ?>>Hello Kitty</option>
								<option value="MinnieMouse" <?php if ($_POST == true && $_POST['mascotWho'] == 'MinnieMouse'){echo 'selected ';} ?>>Minnie Mouse</option>
								<option value="MickeyMouse" <?php if ($_POST == true && $_POST['mascotWho'] == 'MickeyMouse'){echo 'selected ';} ?>>Mickey Mouse</option>
								<option value="Ben10" <?php if ($_POST == true && $_POST['mascotWho'] == 'Ben10'){echo 'selected ';} ?>>Ben 10</option>
								<option value="Dora" <?php if ($_POST == true && $_POST['mascotWho'] == 'Dora'){echo 'selected ';} ?>>Dora the Explorer</option>								
						</select>

					</td>

					<td align="right" class="rytcol" id="MascotPrice">P 3,500
					</td>						
			        	
                </tr>
				
				<tr align="left" valign="middle">


					<td colspan="2">
						<input type="checkbox" name="personnel[]" value="Magicians" id="Magicians" onclick="highlightSelected()"
								<?php if ($_POST && in_array('Magicians',$_POST['personnel'])) {echo 'checked';}?>	
						> Magicians
					</td>
					
					
					<td align="right" class="rytcol" id="MagiciansPrice">P 10,000
					</td>	
					
				</tr>				
				
				<tr align="left" valign="middle">


					<td colspan="2">
						<input type="checkbox" name="personnel[]" value="EmceeSinger" id="EmceeSinger" onclick="highlightSelected()"
								<?php if ($_POST && in_array('EmceeSinger',$_POST['personnel'])) {echo 'checked';}?>	
						> Emcee / Singer
					</td>
					
					
					<td align="right" class="rytcol" id="EmceeSingerPrice">P 4,000
					</td>		
					
				</tr>
				
				<tr align="left" valign="middle">


					<td>
						<input type="checkbox" name="personnel[]" value="ClownsIce" id="ClownsIce" onclick="highlightSelected()"
								<?php if ($_POST && in_array('ClownsIce',$_POST['personnel'])) {echo 'checked';}?>	
						> 3 Clowns and Ice Cream
					</td>
					
					<td>* 10 gallons
					</td>
					
					
					<td align="right" class="rytcol" id="ClownsIcePrice">P 3,500
					</td>	
					
				</tr>
				
				<tr align="left" valign="middle">

					
					<td colspan="2">
						<input type="checkbox" name="personnel[]" value="ClownsHost" id="ClownsHost" onclick="highlightSelected()"
								<?php if ($_POST && in_array('ClownsHost',$_POST['personnel'])) {echo 'checked';}?>	
						> 3 Clowns / Hosting
					</td>
					
					
					<td align="right" class="rytcol" id="ClownsHostPrice">P 4,000
					</td>	
					
				</tr>
				
				<tr align="left" valign="middle">

					<td colspan="2">
						<input type="checkbox" name="personnel[]" value="FacePainting" id="FacePainting" onclick="highlightSelected()"
								<?php if ($_POST && in_array('FacePainting',$_POST['personnel'])) {echo 'checked';}?>	
						> Face Painting
					</td>
					
					
					<td align="right" class="rytcol" id="FacePaintingPrice">P 2,500
					</td>	
					
				</tr>	
				<!-------------- end of personnel ------------>

				<script>
				
				//#############################  JAVASCRIPT #################################
				//##################### this block of code highlights the item price that is checked
				//##################### highlight color used is RED					
				
				function highlightSelected()
				{
						
					 var personnel = document.getElementsByName('personnel[]');
	 
					 for (var i=0, len=personnel.length; i<len; ++i)
					 {
						
						var chkBoxValue = String(personnel[i].value);
						var tdID = chkBoxValue.concat('Price');						
						
						if (personnel[i].checked)
						{
							document.getElementById(tdID).style.color = 'red' ;
							document.getElementById(tdID).style.textDecoration = 'none';
						}
						else
						{
							document.getElementById(tdID).style.color = 'blue' ;
							document.getElementById(tdID).style.textDecoration = 'none';
						}
					 }
				}

				</script>
				
				<!------------------------ TOTAL BUTTON and TOTAL CELL ------------------------>
				
				<tr style="border: none">
				
				<tr>
						
						<td colspan=2 align="center" height="100px" valign="middle">
								
								<!--------------TOTAL Button = executes the JavaScript function getTotal() -->
								<div style="width:100px;height:20px; border: 2px solid purple; font: bold 20px monospace; text-align: center;"
									 onclick="getTotal()"
									 onmouseover="this.style.color='purple';this.style.backgroundColor='white';this.style.cursor='pointer'"
									 onmouseout="this.style.color='black';this.style.backgroundColor='transparent'">
									  Total
								 </div>			
								
								 
						</td>
						
						<td colspan=2 bgcolor="#800040" align="right" style="" height="100px" valign="middle">	
								  <div id="totalPrice" style="font:bold 20px sans-serif;color:white;" >
										<?php if ($missing || $errors)		 
										{echo htmlentities("P " . $hTotalPrice,ENT_COMPAT,'UTF-8');}
										?>			
								  </div>								

								  <input type="hidden" name="hTotalPrice" id="hTotalPrice"
										<?php if ($missing || $errors)
										{echo 'value="'.htmlentities($hTotalPrice,ENT_COMPAT,'UTF-8').'"';}
										?>										 
										 />


						</td>
				</tr>
				
				<!------------------------------------------------------------------->
				
			</table>


			<!--########################### end of CONTENTS/ADDITIONS table #####################-->
			<!--############################################################################-->			
			  
			</div>
		</div>
		

		
<!-- ########################## JAVASCRIPT TOTAL ######################-->

	      <script>
		  
				//#############################  JAVASCRIPT #################################
				//##################### this block of code computes the total price of everything
				//##################### composed of the Total Guest Price and all additions
	      
	      var cupcakeEquiv;
	      
	      function getTotal()
	      {
				var total=0;
				var str1 = "P ";
				
				var numGuests = Number(document.getElementById("txtNumGuests").value);
				
				var numGuestsPrice =  numGuests * 300;
				
				var numC = document.getElementById("cupcakes").value;
				
				var chkCupcake = document.getElementById("cupcake");
				
				if (numGuests >= 100)	//only continue of guests > 100 (minimum)
				{
				
						getFreebies();
	
						if ((chkCupcake.checked == true) && (Number(numC) < 50))	//if cupcakes is checked but no value is entered or value is < 50 (minimum)
						{
							 document.getElementById("cupcakes").focus;
							 alert('Cupcakes must be at least 50 pcs');
							 
							 document.getElementById("cupcakePrice").innerHTML= "P 0";
							 
							 document.getElementById("hCupcakePrice").value = "0";	
							 
						}
						else
						{			// if no missing, proceed with computation of Total
						//total = total + gGuestPrice;
						total = total + numGuestsPrice;
						total = total + Number(getCoverage());
						total = total + Number(getExtras());
						total = total + Number(getCakes());
						total = total + Number(getTreats());
						total = total + Number(getPersonnel());
						
						
						gTotalPrice = total;
				
						document.getElementById("totalPrice").innerHTML=str1.concat(total);							
		
		
						document.getElementById("hTotalPrice").value = total;		
										
						}
				}
				else		//if num of guests < 50, prompt user
				{
				alert('Please enter the number of guests above');
				document.getElementById('txtNumGuests').focus;
				document.getElementById("totalPrice").innerHTML='';		
				}
				
	      }
	      
	      
	      function getCoverage() 		//get Total of Coverage items
		  {
		    //var coverage;
		    //var coveragePrice;
		    //how to check what is the selected radio input
		    //document.getElementById("coverage2").innerHTML=getCheckedRadioId('coverage');
		    //coverage = getCheckedRadioId('coverage');
		
				//var coverage = document.getElementsByName('coverage[]');
				var totalCoverage = 0;
				var price = 0;
				
				var photo = document.getElementById('Photo');
				
				
				if (photo.checked)
				{	
						totalCoverage = 7000;
				}
				else
				{
						totalCoverage = 0;
				}
				
				return totalCoverage;
		   }
						
		/*function getCheckedRadioId(name) {
		    var elements = document.getElementsByName(name);
		
		    for (var i=0, len=elements.length; i<len; ++i)
			if (elements[i].checked) return elements[i].value;
		}*/
		
		
	      function getExtras() 		//get Total of Extras items
	      
	      {
		
				var extras = document.getElementsByName('extras[]');
				var totalExtras = 0;
				var price = 0;
		
				for (var i=0, len=extras.length; i<len; ++i)
				if (extras[i].checked)
				{
					switch (extras[i].value)
					{
						case "PhotoBooth": price = 7500; break;
						case "ProjectorRental": price = 1500; break;
						case "SoundSystem": price = 3000; break;
						case "Floral": price = 10000; break;
						case "Balloons": price = 10000; break;
						case "freeBubble": price = 0; break;
					}
					totalExtras = totalExtras + price;
				}
				
				return totalExtras;	
				
	       }
	       
	       
	       function getCakes()  		//get Total of Cake items
	       {
				
				var totalCakes = 0;
				
				if (document.getElementById('2LayerCake').checked )
				{
					//totalCakes = totalCakes + 2800;
					totalCakes = totalCakes + g2LayerCakePrice ;
				}
				
				if (document.getElementById('3LayerCake').checked )
				{
					totalCakes = totalCakes + 5000;
				}				
				
				if (document.getElementById('cupcake').checked )
				{
					//var cupcakeTotal = document.getElementById('cupcakeTotal');
					//totalCakes = totalCakes + Number(cupcakeTotal.value);
					totalCakes = totalCakes + cupcakeEquiv;
				}
				
				return totalCakes;
		
	       }
	       
	      function getTreats()  		//get Total of Treats items
	      {
		
				var treats = document.getElementsByName('treats[]');
				var totalTreats = 0;
				var price = 0;
		
				for (var i=0, len=treats.length; i<len; ++i)
				if (treats[i].checked)
				{
					switch (treats[i].value)
					{
						//case "ChocoFountain": price = 1500; break;
						case "ChocoFountain": price = gChocoFountainPrice ; break;
						case "PopCorn": price = 2500; break;
						case "HotDog": price = 2500; break;
						case "IceCrumble": price = 2500; break;
						case "CottonCandy": price = 2000; break;
					}
					totalTreats = totalTreats + price;
				}
				
				return totalTreats;	
			
	       }
	       
	       
	      function getPersonnel()   		//get Total of Personnel items
	      {
		
				var personnel = document.getElementsByName('personnel[]');
				var totalPersonnel = 0;
				var price = 0;
		
				for (var i=0, len=personnel.length; i<len; ++i)
				if (personnel[i].checked)
				{
						
					switch (personnel[i].value)
					{
						case "Mascot": price = 3500; break;
						case "Magicians": price = 10000; break;
						case "EmceeSinger": price = 4000; break;
						case "ClownsIce": price = 3500; break;
						case "ClownsHost": price = 4000; break;
						case "FacePainting": price = 2500; break;
					}
					totalPersonnel = totalPersonnel + price;
				}
			
				return totalPersonnel;	
	       }
		
	      </script>
			      
			      
<!-- ########################## end of JAVASCRIPT TOTAL ######################-->

		<br/>
		<br/>
		<br/>
		
<center>

		  <div class="fb-grouplabel" style="margin-left:20px">
		    <label style="DISPLAY: inline" id="item30_label_0">Message/Notes</label>
		  </div>
		  
		  <div style="margin-left:20px">
		    <textarea name="message" id="message" cols=20 rows=10
				placeholder="Tell us anything about the event e.g. describe the decorations, cake (if any), or anything else. ^^"
				data-hint="" maxlength="10000" placeholder="" ><?php if ($missing || $errors)
				{echo htmlentities($message,ENT_COMPAT,'UTF-8');}?></textarea>
		  </div>

</center>


<br/>
<br/>
				  <!-- PREVIEW button= executes JavaScript function funcPreview() -->
				  <div style="width:100px;
								height:25px;
								border: 2px solid purple; font: bold 14px sans-serif;
								text-align: center;
								margin:20px auto;
								vertical-align: middle;
								padding-top:5px;
								color: white;
								border-radius: 5px;
								background-color: purple;"
					   onclick="funcPreview()"
					   onmouseover="this.style.color='black';this.style.backgroundColor='transparent';this.style.cursor='pointer'"
					   onmouseout="this.style.color='white';this.style.backgroundColor='purple'">
						Preview
				   </div>	
		  
		  <!-- SUBMIT BUTTON for FORM -->
		  <div style="HEIGHT: 35px" id="fb-submit-button-div" class="fb-item-alignment-center">
			<input id="fb-submit-button" class="fb-button-special" type="submit" value="Submit" name="send"
			/>
		  </div>
		  

<script>

//#############################  JAVASCRIPT #################################
//##################### this block of code shows the current data and selections of the user
//##################### without submitting the form yet






function funcPreview()
{


var txtNumGuests2 = document.getElementById("txtNumGuests");	
var number2 = txtNumGuests2.value;				
var str2 = "Price: P ";

gNumGuests = number2;
gGuestPrice = number2 * 300;

getTotal();



		
var str1 = "";

var photo = document.getElementById('Photo');
var video = document.getElementById('Video');

if (photo.checked)
{
		str1 = str1.concat("\nPhoto Coverage:P 7000 "); 
}
if (video.checked)
{
		str1 = str1.concat("\nVideo Coverage: FREE "); 
}

//extras

var extras = document.getElementsByName('extras[]');

for (var i=0, len=extras.length; i<len; ++i)
if (extras[i].checked)
{
	switch (extras[i].value)
	{
		case "PhotoBooth": str1 = str1.concat("\nPhoto Booth:P 7500 "); break;
		case "ProjectorRental": str1 = str1.concat("\nProjector Rental:P 1500 "); break;
		case "SoundSystem": str1 = str1.concat("\nSound System:P 3000 "); break;
		case "Floral": str1 = str1.concat("\nFloral Arrangements:P 10000 "); break;
		case "Balloons": str1 = str1.concat("\nBalloon Arrangements:P 10000 "); break;
		case "freeBubble": str1 = str1.concat("\nBubble Machine: FREE "); break;
	}
}


//cakes


if (document.getElementById('2LayerCake').checked )
{
	//totalCakes = totalCakes + 2800;
	//totalCakes = totalCakes + g2LayerCakePrice ;
	
		if (Number(g2LayerCakePrice) == 0)
		{
				str1 = str1.concat("\n2 Layer Cake: FREE");
		}
		else
		{
				str1 = str1.concat("\n2 Layer Cake:P " + g2LayerCakePrice );		
		}
	
	
}

if (document.getElementById('3LayerCake').checked )
{
	//totalCakes = totalCakes + 5000;
	str1 = str1.concat("\n2 Layer Cake:P 5000");	
}				

if (document.getElementById('cupcake').checked )
{
	//var cupcakeTotal = document.getElementById('cupcakeTotal');
	//totalCakes = totalCakes + Number(cupcakeTotal.value);
	//totalCakes = totalCakes + cupcakeEquiv;
	str1 = str1.concat("\nCupcakes:P " + cupcakeEquiv );	
}


var freeCupcakeElement = document.getElementById('freeCupcake');

if (freeCupcakeElement != null)
{
		if (document.getElementById('freeCupcake').checked )
		{
			//var cupcakeTotal = document.getElementById('cupcakeTotal');
			//totalCakes = totalCakes + Number(cupcakeTotal.value);
			//totalCakes = totalCakes + cupcakeEquiv;
			str1 = str1.concat("\nFree Cupcakes: FREE " );	
		}
}


//treats

var treats = document.getElementsByName('treats[]');

for (var i=0, len=treats.length; i<len; ++i)
if (treats[i].checked)
{
	switch (treats[i].value)
	{
		//case "ChocoFountain": price = 1500; break;
		//case "ChocoFountain": str1 = str1.concat("\nPhoto Booth: 7500 "); break;
		
		case "ChocoFountain":
		
				if (Number(gChocoFountainPrice) == 0)
				{
						str1 = str1.concat("\nChocolate Fountain: FREE" );
				}
				else
				{
						str1 = str1.concat("\nChocolate Fountain:P " + gChocoFountainPrice);		
				}
		
		break;

		//case "ChocoFountain": str1 = str1.concat("\nChocolate Fountain: " + gChocoFountainPrice); break;  
		case "PopCorn": str1 = str1.concat("\nPop Corn Cart:P 2500 "); break;
		case "HotDog": str1 = str1.concat("\nHotdog Stand:P 2500 "); break;
		case "IceCrumble": str1 = str1.concat("\nIce Crumble Cart:P 2500 "); break;
		case "CottonCandy": str1 = str1.concat("\nCotton Candy:P 2000 "); break;
	}
}


//personnel

var personnel = document.getElementsByName('personnel[]');

var mascotWho = document.getElementById('mascotWho');



for (var i=0, len=personnel.length; i<len; ++i)
if (personnel[i].checked)
{
		
	switch (personnel[i].value)
	{
		case "Mascot": str1 = str1.concat("\nMascot: (" + mascotWho.value  + ") P 3500 "); break;
		case "Magicians": str1 = str1.concat("\nMagicians:P 10000 "); break;
		case "EmceeSinger": str1 = str1.concat("\nEmcee/Singer:P 4000 "); break;
		case "ClownsIce": str1 = str1.concat("\n3 Clowns and Ice Cream:P 3500 "); break;
		case "ClownsHost": str1 = str1.concat("\n3 Clowns / Hosting:P 4000 "); break;
		case "FacePainting": str1 = str1.concat("\nFace Painting:P 2500 "); break;
	}
}

/*
*/			

//display POP-UP box for Preview

alert('Total guest number: ' +  gNumGuests + "\n" + 'Total guest price:P ' + gGuestPrice + "\n" +
	   "\n" + "-------------Additions--------------" + str1 + "\n" +  "\n" + "-------------TOTAL--------------" +  "\n" +
	  'Total amount:P ' + gTotalPrice);

}

</script>
  
		  

</form>
	

<?php  

}
else			
{
				//###################### THIS PART ONWARDS WOULD ONLY BE EXECUTED/DISPLAYED IF 
				//######################  THE RESERVATION FORM IS SUBMITTED WITHOUT ERRORS ######### 

//echo "<pre>";		//used for testing
//print_r($_POST);
//echo "</pre>";

$twoLayerCakePrice = $_POST['h2LayerCakePrice'];	
$chocoFountainPrice = $_POST['hChocoFountainPrice'];

//make array for items, names, and prices

$items = array(0 => 'Photo',
			   1 => 'Video',
			   2 => 'PhotoBooth',
			   3 => 'ProjectorRental',
			   4 => 'SoundSystem',
			   5 => 'Floral',
			   6 => 'Balloons',
			   7 => 'freeBubble',
			   8 => '2LayerCake',
			   9 => '3LayerCake',
			   10 => 'freeCupcake',
			   11 => 'empty',
			   11 => 'ChocoFountain',
			   12 => 'PopCorn',
			   13 => 'HotDog',
			   14 => 'IceCrumble',
			   15 => 'CottonCandy',
			   16 => 'Mascot',
			   17 => 'Magicians',
			   18 => 'EmceeSinger',
			   19 => 'ClownsIce',
			   20 => 'ClownsHost',
			   21 => 'FacePainting' );

$names = array(0 => 'Photo Coverage',
			   1 => 'Free Video Coverage',
			   2 => 'Photo Booth',
			   3 => 'Projector Rental',
			   4 => 'Sound System',
			   5 => 'Floral Arrangements',
			   6 => 'Balloon Arrangements',
			   7 => 'Free Bubble Machine',
			   8 => '2 Layer Cake',
			   9 => '3 Layer Cake',
			   10 => 'Free Cupcake - 50 pcs',
			   11 => 'empty',
			   11 => 'Chocolate Fountain Rental',
			   12 => 'Pop Corn Cart',
			   13 => 'Hotdog Stand',
			   14 => 'Ice Crumble Cart',
			   15 => 'Cotton Candy',
			   16 => 'Mascot',
			   17 => 'Magicians',
			   18 => 'Emcee/Singer',
			   19 => '3 Clowns and Ice Cream',
			   20 => '3 Clowns/Hosting',
			   21 => 'Face Painting' );

$prices = array(0 => 7000,
			   1 => 0,
			   2 => 7500,
			   3 => 1500,
			   4 => 3000,
			   5 => 10000,
			   6 => 10000,
			   7 => 0,
			   8 => $twoLayerCakePrice,
			   9 => 5000,
			   10 => 0,
			   11 => 0,
			   11 => $chocoFountainPrice,
			   12 => 2500,
			   13 => 2500,
			   14 => 2500,
			   15 => 2000,
			   16 => 3500,
			   17 => 10000,
			   18 => 4000,
			   19 => 3500,
			   20 => 4000,
			   21 => 2500 );

?>

<div style="border:5px purple ridge;
				margin: 20px auto;
				padding: 20px;
				text-align: center;
				font: 16px sans-serif;
				line-height: 25px;
				">
		
<table border=2 cellpadding=5px cellspacing=5px align=center width=600px>
		
<tr><th colspan=2>Details</th> </tr>

<?php

//############# DISPLAY DETAILS OF RESERVATION ##########################
//############## PHP code here includes computation of additions and the total price
//############## separate from the computation in JavaScript
//############### computation and details here will be the data to be saved in the database


echo "<tr> <td> Number of Guests </td> <td align='right'> " . $_POST['txtNumGuests'] . "</td></tr>" ;

		$_SESSION['txtNumGuests'] = $_POST['txtNumGuests'];

echo "<tr> <td>Initial Price for Num. of Guests </td> <td align='right'>P " . $_POST['hGuestPrice'] . "</td></tr>" ;

		$_SESSION['hGuestPrice'] = $_POST['hGuestPrice'];


if ($_POST['event'] == 'Other')
{echo "<tr> <td>Event </td> <td align='right'> " . $_POST['other_event'] . "</td></tr>" ;
 $_SESSION['event'] =  $_POST['other_event'];
}
else
{echo "<tr> <td>Event </td> <td align='right'> " . $_POST['event'] . "</td></tr>" ;
 $_SESSION['event'] =  $_POST['event'];
}

echo "<tr> <td>Location </td> <td align='right'> " . $_POST['location'] . "</td></tr>" ;
		$_SESSION['location'] = $_POST['location'];
		
		
$newDate2 = $_POST['date'];        // get date from user input
$format2 = 'M-d-Y';    
$newDate2 = new DateTime($newDate2);   // create a DateTime object using user-input date           	

echo "<tr> <td>Event Date </td> <td align='right'> " . $newDate2->format($format2) . "</td></tr>" ;
		$_SESSION['date'] = $_POST['date'];
		
		
echo "<tr> <td>Event Time </td> <td align='right'>" . $_POST['eventtime'] . "</td></tr>";
		$_SESSION['eventtime'] = $_POST['eventtime'];
		
		$_SESSION['eventtime24'] = $_POST['eventtime24'];
		

echo "<tr><th colspan='2'>Additions</th></tr>" ;

//coverage

$strCoverage = '';

foreach ($_POST['coverage'] as $key => $value) {
		
		$keyF = array_search($value, $items);
		
		echo "<tr> <td>";
		echo $names[$keyF] . "</td> <td align='right'> ";
		
		$strCoverage .= $names[$keyF] . "," ;
		
		if (($prices[$keyF] == 0) || (empty($prices[$keyF])))
		{
			echo "FREE";	
		}
		else
		{
		echo "P " . $prices[$keyF];		
		}

		echo  "</td></tr>";
		
	}
	
	$_SESSION['coverage'] =  $strCoverage;
	
//extras

$strExtras = '';
		
foreach ($_POST['extras'] as $key => $value) {

		$keyF = array_search($value, $items);
		
		echo "<tr> <td>";
		echo $names[$keyF] . "</td> <td align='right'> ";
		
		$strExtras .= $names[$keyF] . "," ;
		
		if (($prices[$keyF] == 0) || (empty($prices[$keyF])))
		{
			echo "FREE";	
		}
		else{
		echo "P " . $prices[$keyF];		
		}
		
		echo "</td></tr>";
	}
	
	$_SESSION['extras'] = $strExtras;
	

//cakes

$strCakes = '';
			
foreach ($_POST['cakes'] as $key => $value) {
		
		if ($value == 'cupcake')
		{
				if (isset($_POST['cupcakes']) && (($_POST['cupcakes']) >= 50) )
				{	
				echo "<tr> <td> Cupcakes ( " . $_POST['cupcakes'] . " pcs. ) </td> <td align='right'>P  " . ($_POST['cupcakes'] * 40 . "</td></tr>");
				$strCakes .= "Cupcakes " . $_POST['cupcakes'] . " pcs. ,"  ;
				}	
		}
		else
		{
				$keyF = array_search($value, $items);
				
				echo "<tr> <td>";
				echo $names[$keyF] . "</td> <td align='right'> ";
				
				$strCakes .= $names[$keyF] . "," ;
				
				if (($prices[$keyF] == 0) || (empty($prices[$keyF])))
				{
					echo "FREE";	
				}
				else{
				echo "P " . $prices[$keyF];		
				}
				echo "</td></tr>";			
		}
	}
	
	$_SESSION['cakes'] = $strCakes;
	

//treats
$strTreats = '';
		
foreach ($_POST['treats'] as $key => $value) {

		$keyF = array_search($value, $items);
		
		echo "<tr> <td>";
		echo $names[$keyF] . "</td> <td align='right'> ";
		
		$strTreats .= $names[$keyF] . "," ;
		
		if (($prices[$keyF] == 0) || (empty($prices[$keyF])))
		{
			echo "FREE";	
		}
		else{
		echo "P " . $prices[$keyF];		
		}

		echo "</td></tr>";	
	}
	
	$_SESSION['treats'] = $strTreats;
	
//personnel

$strPersonnel = '';
			
foreach ($_POST['personnel'] as $key => $value) {

		$keyF = array_search($value, $items);
		
		echo "<tr> <td>";
		echo $names[$keyF] . "</td> <td align='right'> ";
		
		$strPersonnel .= $names[$keyF] . "," ;
		
		if (($prices[$keyF] == 0) || (empty($prices[$keyF])))
		{
			echo "FREE";	
		}
		else
		{
		echo "P " . $prices[$keyF];		
		}

		echo "</td></tr>";
		
		if (($value == 'Mascot') && (!empty($_POST['mascotWho'])) )
		{
		echo "<tr> <td>Mascot Character </td> <td align='right'> " . $_POST['mascotWho'] ;
		
		$strPersonnel .= "Mascot Character: " . $_POST['mascotWho']  . ","  ;
		
		echo "</td></tr>";	
		}
	}
	
	$_SESSION['personnel'] = $strPersonnel;

echo "<tr> <th colspan=2>Message </th></tr><tr> <td colspan=2 align='center'> " . $_POST['message'] . "</td></tr>";
		$_SESSION['message'] = $_POST['message'];

echo "<tr> <th>Total Price <td align='right'><span style='color:red;font-weight:bold;' >P " . $_POST['hTotalPrice'] . "</span></td></tr>";
		$_SESSION['hTotalPrice'] = $_POST['hTotalPrice'];

?>
	
</table>	
	
</div>

<!-------------------- AGREEMENTS, PAYMENT OPTIONS, METHODS, etc --------------->

<div style="border:5px purple ridge; margin: 20px auto; padding: 20px; font: 16px sans-serif; line-height: 25px;">

<b>Reservation Agreement</b> <br />
* Payment must be completed before the date of event. <br />
* The following conditions will be charged extra:<br />
<ul>
	<li>Any missing or broken utensils</li>
	<li>Excess amount of foods</li>
	<li>Excess hours beyond the event hours limit (4-5 hours maximum)</li>	
</ul>
</div>
	
<div style="border:5px purple ridge; margin: 20px auto; padding: 20px; font: 16px sans-serif; line-height: 25px;">

<b>Payment Options </b> <br />
* Full Payment <br />
* Installment - Downpayment of P 5000 (non-refundable) as Reservation Fee <br />
</div>

<div style="border:5px purple ridge; margin: 20px auto; padding: 20px; font: 16px sans-serif; line-height: 25px;">
<b>Payment Method </b> <br />
* Please send payment to this bank account <br />
<img src="./images/bdo.jpg" width="150px" style="margin:10px; border: 2px black solid; float: left;"> <br />
<b>Account Number: <span style="color:blue">702-002-1015</span> </b>  <br />
<b>Account Name: <span style="color:blue">Henry Catigan</span></b>  <br />
<br />
* Please inform us of completed payment through a text message <br />
<b>Cellphone Numbers
<br/> Globe: <span style="color:blue">0906-482-7349</span><br />
Smart: <span style="color:blue">0907-609-8086</span><br /></b>

</div>

<div style="border:5px purple ridge; margin: 20px auto; padding: 20px; font: 16px sans-serif; line-height: 25px;">
		<ul>
		<li>By clicking Finish, it is assumed that you agree to the Reservation Agreement described above. </li>
		<li> the reservation will be completed and immediately sent to us.<br/>  </li>
		<li>You can click Cancel to abort the transaction. All data from the reservation form will be lost.<br/>	</li>	
		<li>We will process the reservation accordingly after initial payment is received. <br />  </li>
		<li>Note the payment options and method described above. <br /> </li>
		<li>Please read the <b>Reservation Agreement</b> above before completing this transaction. <br /> </li>
		</ul>

<center>
		
<form id="finishR" method="post" action="">

		  <!----- FINISH button ------------>
		  <div style="HEIGHT: 35px" id="fb-submit-button-div" class="fb-item-alignment-center">
			<input id="fb-submit-button" class="fb-button-special" type="submit" value="Finish" name="finish"
			/>
		  </div>
		  
</form>
  
</center>
	
		<!----- CANCEL button ------------>
		<!---- executes JavaScript function Cancel() -->
		
		<div style="width:100px;height:25px; border: 2px solid purple; font: bold 14px sans-serif; text-align: center; margin:20px auto; color: white; background-color: red; padding-top:5px; border-radius: 5px 5px;"
			 onmouseover="this.style.color='black';this.style.backgroundColor='hotpink';this.style.cursor='pointer'"
			 onmouseout="this.style.color='white';this.style.backgroundColor='red'"
			 onclick="Cancel()">
				Cancel
		 </div>			
		
</div>

<script>

function Cancel()		//cancel clicked, alert user that changes will be lost
{

	var r=confirm("You will lose any changes made in the reservation. Do you want to continue?");
	if (r==true)
	  {
	  self.location="./reservation.php";
	  }
	else
	  {
	  x="You pressed Cancel!";
	  }
	  
}  

</script>
	
				
<?php }
?>


</div>


<!----- RESERVATION AGREEMENT overlay-------------------->
<!------ this block is normally hidden and only viewed when Reservation Agreement button is clicked --->

<div id="overlay">
     <div>
          <p>
				<center><b>Reservation Agreement</b></center> <br /> <br />
				* Payment must be completed before the date of event. <br />
				* The following conditions will be charged extra:<br />
				<ul>
					<li>Any missing or broken utensils</li>
					<li>Excess amount of foods</li>
					<li>Excess hours beyond the event hours limit (4-5 hours maximum)</li>	
				</ul>			
				
		  <br/>
		  <br/>
		  <a href='#' onclick='overlay()'
			onmouseover="this.style.color='blue';this.style.cursor='pointer'">Hide</a>
		  
		  </p>
     </div>
</div>
 
<script>
 
function overlay() {
	el = document.getElementById("overlay");
	el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";
}
 
</script>
 
 <!---------------- end of overlay -------------------------->
 
 
</div>
	
</body>

</html>