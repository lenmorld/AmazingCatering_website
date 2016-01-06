<?php

//prepare database connection
require_once('my_connection.inc.php');
$conn = dbConnect('read');

// create key for encryption
$key = 'takeThisWith@PinchOfSalt';
$sql = 'SELECT username FROM admin
		WHERE username = ? AND pwd = AES_ENCRYPT(?, ?)';
		
// initialize and prepare statement
$stmt = $conn->stmt_init();
$stmt->prepare($sql);

// bind the input parameters
$stmt->bind_param('sss', $username, $password, $key);
$stmt->execute();

// store the result
$stmt->store_result();

// if a match is found, num_rows is 1, which is treated as true
if ($stmt->num_rows) {
  $_SESSION['authenticated'] = 'admin';
  // get the time the session started
  $_SESSION['start'] = time();
  session_regenerate_id();
  header("Location: $redirect");
  exit;
} else {
  // if no match, prepare error message
  $error = 'Invalid username or password';
}