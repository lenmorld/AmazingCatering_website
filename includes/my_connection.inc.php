<?php 
function dbConnect($usertype, $connectionType = 'mysqli') { 

// check if ran locally (testing) or online ################################ 

$mystring = $_SERVER['HTTP_HOST'];          // $_SERVER['HTTP_HOST'] holds the hostname,
                                                // if tested locally, it is ->  http://localhost/ 
                                                // if online, it is ->  http://amazingcoveragecatering.net46.net/  
                                                
$posLocal = strpos($mystring, 'localhost');       // returns true if 'localhost' is find in hostname        
$posOnline =  strpos($mystring, 'net46');        // returns true if 'net46' is find in hostname

if ($posLocal !== false) {
    //string 'localhost is find in address- use localhost'
    $host = 'localhost';
} 
else if ($posOnline !== false) {
    //string 'hostzi' - online host is find in address- use online host'
    $host = 'mysql2.000webhost.com';
}

  //$host = 'localhost'; 
  //$host = 'mysql11.000webhost.com';
  
// ##################################################################

  $db = 'a8227946_amazing'; 			//database name
  
										//only 1 user is allowed per database at 000webhost
										//so both modes uses the 'a5106316_awrite' user
										
  if ($usertype  == 'read') { 					
    $user = 'a8227946_awrite'; 			//database username
    $pwd = 'programming25'; 			//database password
  } elseif ($usertype == 'write') { 	
    $user = 'a8227946_awrite'; 			//database username
    $pwd = 'programming25';             //database password
  } else { 
    exit('Unrecognized connection type'); 
  } 
  
  // Connection code goes here 
  if ($connectionType == 'mysqli') 
  { 
   $conn = new mysqli($host, $user, $pwd, $db);			//establish connection using the credentials given
	if ($conn->connect_error)						//if database error, exit script
		{die('Cannot open database' . $host . $posOnline . $posLocal);}			
	return $conn;					//return connection object if successful
  } 
 else { 
   try { 
     return new PDO("mysql:host=$host;dbname=$db", $user, $pwd); 	//establish connection using the credentials given
   } catch (PDOException $e) { 					//catch database error
     echo 'Cannot connect to database'; 
     exit; 
   } 
 }  
 
} 