<?php
  //  check if user came from a non-admin page and tries to access this one ################################ 

$mystring = $_SERVER['HTTP_REFERER'];         // HTTP_REFERE contains the previous page before this page              
                                                
$posAdmin = strpos($mystring, 'admin');       // returns true if 'admin' is find in hostname        


if ($posAdmin === false) {
    //string 'localhost is find in address- offline'
      // empty the $_SESSION array
      $_SESSION = array();
      // invalidate the session cookie
      if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-86400, '/');
      }
      // end session
      session_destroy();   
 
      //redirect to home page
      header('Location: '. './login.php');
}

//echo $mystring;


//####################################################################################
