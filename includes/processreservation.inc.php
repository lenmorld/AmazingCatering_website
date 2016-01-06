<?php 


/*
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
	
//echo "$event , $name , $contactno, $location, $date, $time";
//print_r($missing);

*/
	
// initialize boolean variables ###########################################	
		  
$mailSent = false;
$reservationSaved= false;
$messageSaved = false;

// go ahead only if missing is false (fields required are completed) 
//and errors is false (there are no errors)
if (!$missing && !$errors) 
{
	  
//convert $_SESSION contents to variables ##################################
// $_SESSION array contains the reservation details after the form is submitted

foreach ($_SESSION as $key => $value) { 
	  // assign to temporary variable and strip whitespace if not an array 
	  $temp = is_array($value) ? $value : trim($value); 
	  // if empty and required, add to $missing array 
	  if (empty($temp)) {
		//$missing[] = $key; 
		${$key} = '';        //errata update
							//prevents 'undefined var' warning
	  } else { 
		// otherwise, assign to a variable of the same name as $key 
		${$key} = $temp; 
	  }
}

	  /*
	  // loop through the $expected array 
	  foreach($expected as $item) { 
		// assign the value of the current item to $val 
		if (isset(${$item}) && !empty(${$item})) { 
		  $val = ${$item};
		  //echo $val;
		} else { 
		  // if it has no value, assign 'Not selected' 
		  $val = 'Not selected'; 
		} 
		// if an array, expand as comma-separated string 
		if (is_array($val)) { 
		  $val = implode(', ', $val); 
		} 
		// replace underscores and hyphens in the label with spaces 
		$item = str_replace(array('_', '-'), ' ', $item); 
		// add label and value to the message body 
		$message .= ucfirst($item).": $val\r\n\r\n"; 
	  } */

	  // initialize the $message variable 
	  $eMessage = '';
	  
	/*  
	  $message .= "Username: " . $_SESSION['username'] . "\r\n\r\n";
	  $message .= "Customer Name: " . $_SESSION['name'] . "\r\n\r\n";
	  $message .= "Number of Guests: " . $_SESSION['txtNumGuests'] . "\r\n\r\n";
	  $message .= "Total Price of Guests: " . $_SESSION['hGuestPrice'] . "\r\n\r\n";
	  $message .= "Event: " . $_SESSION['event'] . "\r\n\r\n";
	  $message .= "Location: " . $_SESSION['location'] . "\r\n\r\n";
	  $message .= "Date: " . $_SESSION['date'] . "\r\n\r\n";
	  $message .= "Time: " . $_SESSION['eventtime'] . "\r\n\r\n";
	  $message .= "-------------Additions---------------" . "\r\n\r\n";
	  $message .= "Coverage: " . $_SESSION['coverage'] . "\r\n\r\n";
	  $message .= "Extras: " . $_SESSION['extras'] . "\r\n\r\n";
	  $message .= "Cakes: " . $_SESSION['cakes'] . "\r\n\r\n";
	  $message .= "Treats: " . $_SESSION['treats'] . "\r\n\r\n";
	  $message .= "Personnel: " . $_SESSION['personnel'] . "\r\n\r\n";
	  $message .= "Message: " . $_SESSION['message'] . "\r\n\r\n";
	  $message .= "Total Price: " . $_SESSION['hTotalPrice'] . "\r\n\r\n";
	*/
	
	  $eMessage .= "Username: " . $username . "\r\n\r\n";
	  $eMessage .= "Customer Name: " . $name . "\r\n\r\n";
	  $eMessage .= "Email: " . $email . "\r\n\r\n";
	  $eMessage .= "Contact No: " . $contactno . "\r\n\r\n";
	  $eMessage .= "Number of Guests: " . $txtNumGuests . "\r\n\r\n";
	  $eMessage .= "Total Price of Guests: " . $hGuestPrice . "\r\n\r\n";
	  $eMessage .= "Event: " . $event . "\r\n\r\n";
	  $eMessage .= "Location: " . $location . "\r\n\r\n";
	  $eMessage .= "Date: " . $date . "\r\n\r\n";
	  $eMessage .= "Time: " . $eventtime . "\r\n\r\n";
	  $eMessage .= "-------------Additions---------------" . "\r\n\r\n";
	  $eMessage .= "Coverage: " . $coverage . "\r\n\r\n";
	  $eMessage .= "Extras: " . $extras . "\r\n\r\n";
	  $eMessage .= "Cakes: " . $cakes . "\r\n\r\n";
	  $eMessage .= "Treats: " . $treats . "\r\n\r\n";
	  $eMessage .= "Personnel: " . $personnel . "\r\n\r\n";
	  $eMessage .= "Message: " . $message . "\r\n\r\n";
	  $eMessage .= "Total Price: " . $hTotalPrice . "\r\n\r\n";
	
	  // limit e-mail line length to 70 characters 
	  $eMessage = wordwrap($eMessage, 70);

	//save to database ###########################################################
	
	//convert date to MySQL format
	$format = 'Y/m/d';

	$newDate = new DateTime($date);		// create new DateTime object
	$date = $newDate->format($format);	// format date

	//form the SQL query

	  $zero = 0;


// ----------------------before messaging---------------------
//	$sql = "INSERT INTO reservations		
//		(user_ID,event,location,date,time,date_reserved,numGuests,guestPrice,coverage,extras,cakes,treats,personnel,message,totalPrice,paid) 
//		VALUES (" . "$userID, '$event', '$location', '$date', '$eventtime24'". ",NOW(), $txtNumGuests, $hGuestPrice, '$coverage', '$extras', '$cakes', '$treats', '$personnel', '$message', $hTotalPrice, $zero )";


    $message = htmlentities($message, ENT_QUOTES);

	$sql = "INSERT INTO reservations		
		(user_ID,event,location,date,time,date_reserved,numGuests,guestPrice,coverage,extras,cakes,treats,personnel,message,totalPrice,paid) 
		VALUES (" . "$userID, '$event', '$location', '$date', '$eventtime24'". ",NOW(), $txtNumGuests, $hGuestPrice, '$coverage', '$extras', '$cakes', '$treats', '$personnel', '$message', $hTotalPrice, $zero )";


	  if (!$conn->query($sql)) 				//query is not executed by the connection
	  { $errors['database'] = $conn->error; 		//get error
	  } 		
	  else
	  { $reservationSaved = true;}          //else reservation is successfully saved
	  
	  
	  $sql = "SELECT reserve_ID
			 FROM reservations
			 WHERE user_ID = $userID
			 AND date= '$date' ";
	  
	  $result = $conn->query($sql) or die(mysqli_error());
	  $row = $result->fetch_assoc();
	  
	  $id = $row['reserve_ID'];
	  
    $sql = "INSERT INTO messages
			(reserve_ID,date_added,message,sender)
			VALUES (" . "$id, NOW(), '$message','$username')";


	  if (!$conn->query($sql)) 				//query is not executed by the connection
	  { $messageNotSaved = $conn->error; 		//get error
	  } 		
	  else
	  { $messageSaved = true;}          //else reservation is successfully saved


//	  echo $sql;
		

	//###############################################################################

	// send to mail #####################################################
			// mail engine only works online, don't send mail if testing in localhost
	  if ($online)
	  {
	  
		  $headers .= "\r\nReply-To: $email";
	  
		  $mailSent = mail($to,$subject,$eMessage,$headers); 
		  
		  if ($mailSent == false)
		  {
			$errors['mailfail'] = true;
		  }
	  }
}
//############################################################################### 
	  
