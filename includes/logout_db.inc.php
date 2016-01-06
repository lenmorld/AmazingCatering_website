<?php
// run this script only if the logout button has been clicked
if (isset($_POST['logout'])) {
  // empty the $_SESSION array
  $_SESSION = array();
  // invalidate the session cookie
  if (isset($_COOKIE[session_name()])) {
	setcookie(session_name(), '', time()-86400, '/');
  }
  // end session
  session_destroy();
  
  //redirect to home page
  header('Location: '. '../index.php');
  
 
  exit;
}
?>

<!----------- logout button ---------------->
<div style="clear:both; margin-top:10px;">
<form id="logoutForm" method="post" action="">
  <input name="logout" type="submit" id="logout" value="Log out">
</form>
</div> 