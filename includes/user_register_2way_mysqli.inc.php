<?php
require_once('./classes/c25/CheckPassword.php');  // call code that handles checking of password

$errors = array();		//prepare errors array

$usernameMinChars = 6;		//minimum characters of username required
if (strlen($username) < $usernameMinChars) {		//if username length is lower than required
  $errors[] = "Username must be at least $usernameMinChars characters.";
}

if (preg_match('/\s/', $username)) {				//if space/s detected
  $errors[] = 'Username should not contain spaces.';
}

$checkPwd = new c25_CheckPassword($password, 6);	//create password object
//$checkPwd->requireMixedCase();
$checkPwd->requireNumbers(2);			//require at least 2 numbers
//$checkPwd->requireSymbols();
$passwordOK = $checkPwd->check();		//check validity of password

if (!$passwordOK) {					//if password not OK
  $errors = array_merge($errors, $checkPwd->getErrors());
}
if ($password != $retyped) {		//if passwords don't match
  $errors[] = "Your passwords don't match.";              
}


// name validation ####################################

if (empty($fname))
{
  $errors[] = "Please enter your full name";      
}

if (strlen($fname) < 5)
{
  $errors[] = "Name must be at least 5 characters";      
}


// contact no validation ####################################    

if (!empty($contactno))
{
$subject = $contactno;
preg_match_all('/[0-9]/', $subject, $matches);
$count = count($matches[0]);
                              
$lettersFound = preg_match_all('/[A-Za-z]/', $subject, $matches2 );  

  if (($count < 7) || ($lettersFound) )
    {
       $errors[] = "Please enter a valid contact number";  
    }
                          
}
else
{
  $errors[] = "Please enter your contact no";      
}


 //############# email validation ###############################

// validate the user's email 

if (!empty($email)) {         // if e-mail field is not empty
  $validemail = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);  //check e-mail if in valid format
  if ($validemail) {         // if e-mail is a valid e-mail
    //$headers .= "\r\nReply-To: $validemail"; 
  } else { 
    $errors[] = "Please enter a valid e-mail address";     // set e-mail error true if it is not a valid e-mail
  } 
}

//#####################################################################

//execute only if there are no errors
if (!$errors) {			
  // include the connection file
  require_once('my_connection.inc.php');
  $conn = dbConnect('write');
  // create a key
  $key = 'takeThisWith@PinchOfSalt';
  
  // prepare SQL statement
  $sql = 'INSERT INTO users
              (username, pwd, name, contactno, email)			
          VALUES (?, AES_ENCRYPT(?, ?), ?, ?, ? )';
		  
	////table name is 'users_2way', encrypt password before inserting into database
		  
  $stmt = $conn->stmt_init();				//initialize statement
  $stmt = $conn->prepare($sql);				//prepare SQL
  
  
  // bind parameters and insert the details into the database
  $stmt->bind_param('ssssss', $username, $password, $key ,$fname, $contactno, $email);
  $stmt->execute();				
  
  if ($stmt->affected_rows == 1) {		//username successfully registered
	$success = "$username has been registered. You may log in later using this account.";
  } elseif ($stmt->errno == 1062) {		//duplicate username
	$errors[] = "$username is already in use. Please choose another username.";
  } else {					// there are other problems with the database
	$errors[] = 'Sorry, there was a problem with the database.';
  }
}