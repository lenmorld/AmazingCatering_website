<?php

//prepare database connection
require_once('my_connection.inc.php');
$conn = dbConnect('read');

// create key for encryption
$key = 'takeThisWith@PinchOfSalt';
$sql = 'SELECT username, user_ID, name, email, contactno 
        FROM users
		WHERE username = ? AND pwd = AES_ENCRYPT(?, ?)';
		
// initialize and prepare statement
$stmt = $conn->stmt_init();
$stmt->prepare($sql);

// bind the input parameters
$stmt->bind_param('sss', $username, $password, $key);
$stmt->execute();

// store the result
$stmt->store_result();

$stmt->bind_result($username, $user_ID, $name, $email, $contactno );   


//If you don't bind the result to variables, use  $row = $stmt->fetch(), and access each variable as 
//$row['column_name'].

// if a match is found, num_rows is 1, which is treated as true
if ($stmt->num_rows) {
    
  //$row = $stmt->fetch();
 // $_SESSION['user_ID'] = $row['user_ID'];
 

 while ($stmt->fetch()) { 
  $_SESSION['user_ID'] = $user_ID;
  $_SESSION['name'] = $name;
  $_SESSION['email'] = $email;
  $_SESSION['contactno'] = $contactno;
  }
  
  
  $_SESSION['authenticated'] = 'camille';
  // get the time the session started
  $_SESSION['start'] = time();
  session_regenerate_id();
  
  
  header("Location: $redirect");
  exit;
} else {
  // if no match, prepare error message
  $error = 'Invalid username or password';
}


