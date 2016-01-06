<?php 

//convert $_POST contents to variables ##################################
// $_POST array contains the user input after the form is submitted

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

/*
 //############# email validation ###############################

// validate the user's email 

if (!empty($email)) { 		// if e-mail field is not empty
  $validemail = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);  //check e-mail if in valid format
  if ($validemail) { 		// if e-mail is a valid e-mail
	$headers .= "\r\nReply-To: $validemail"; 
  } else { 
	$errors['email'] = true; 	// set e-mail error true if it is not a valid e-mail
  } 
}

//#####################################################################
*/	
	
	
// initialize boolean variables ###########################################	
		  
$mailSent = false;
$reservationSaved= false;

// go ahead only if missing is false (fields required are completed) 
//and errors is false (there are no errors)
if (!$missing && !$errors) 
{
	  // initialize the $message variable 
	  $message = ''; 
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
	  } 
	  
}
//############################################################################### 
	  
