<?php


//session timeout logs out the user automatically after a period of inactivity
require_once('../includes/session_timeout.inc.php');

//check if user comes from an admin page, require log-in if outside admin
require_once('../includes/check_admin.inc.php');


// set the max upload size per file in bytes  
$max = 2097152;			// 2,097,152 is 2 MB	limit in 000webhost

//multiple uploads are allowed, no limit is imposed on total size of all selected uploaded files
//the limit above applies to each individual file
// you can customize error messages and other options at the class file (Upload_class.php) , please also see that code

if (isset($_POST['uploadP']))
{

if (isset($_POST['folderI']))
    {      
      $folder = $_POST['folderI'];
    }
	else
	{
	  $folder = 'other';
	}
    
	$dest = './uploads/photos/' . $folder . '/' ;			// destination of uploaded files
	
	
		require_once('./upload_class.php');
          try { 
            $upload = new c1_Upload($dest); 
			
			//change max size of file allowed through $max var on top
			$upload->setMaxSize($max);
			
            
            $upload->setPermittedTypes(array('image/gif', 		
                                        'image/jpeg', 
										'image/jpg',
                                        'image/pjpeg', 
                                        'image/png'								
										));
  
			
			$upload->move();				//add duplicate file if exist and rename
			
            $result = $upload->getMessages(); 
          } catch (Exception $e) { 
            echo $e->getMessage(); 
          }	
	
}


?>

<!DOCTYPE html>
<html lang="en-US" style="height: 100%">
<head>
<title>Upload Page - Admin

</title>
<!--link rel="stylesheet" type="text/css" href="style.css" /-->
<link href="admin.css" rel="stylesheet" type="text/css">
</head>

<body style="background-color:lightblue">
  
  
  


<!--------------------------- USERNAME and LOGOUT ------------------------------->

<div style="border: 5px purple ridge;
		display:block;
		color:#ffceff;
		font: bold 15px  'Lucida Sans Unicode', 'Lucida Grande', sans-serif;
		background-color:#8080c0;
		text-align:center;
		padding:10px;
		margin:10px auto;
		height:170px;">

  
<p style="font-weight: bold">UPLOAD PAGE - ADMIN</p>

<?php

    echo "You are logged in, <u>" . $_SESSION['username'] . "</u>";            //display username
    
    include('../includes/logout_db.inc.php');        // log out button
        
?>


<div style="padding:10px; text-align: center; width: 300px; margin:0px auto;">
<a href="./adminpage.php">
<img src="../images/cooltext785843436.png" onmouseover="this.src='../images/cooltext785843436MouseOver.png';" onmouseout="this.src='../images/cooltext785843436.png';" />
</a>
</div>




</div>

<!----------------------- end of USERNAME and LOGOUT -------------------------------->


<h3>Upload here<br/>You can select multiple items by using [Ctrl] + [left click] </h3>
  
  

<div style="margin:20px; color:darkgreen; font-weight: bold;">
  
  <?php	
		
		if (isset($result)) { 
          echo '<ul>'; 
          foreach ($result as $message) {
            echo "<li>$message</li>"; 
          } 
        echo '</ul>'; 
        }
		
?>
  
</div>

<!------------------------------------------------>  
  
  
<div style="border:5px blue inset; padding: 20px;">

<div style="margin:20px">
  
  
</div>

<div style="margin:20px"><h1>Images</h1></div>

<form action="" method="post" enctype="multipart/form-data" id="uploadImage">

	<label for="image">Upload file:</label>
	<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max; ?>">
	
	<input type="file" name="images[]"  multiple accept="image/*">

	<input type="submit" name="uploadP"  value="Upload">
      
      <br />
      <br />
       <br />
      <br />     
      
      
      <input type="radio" name="folderI" value="wedding"
	  <?php if (isset($_POST['folderI']) && ($_POST['folderI'] == 'wedding')) {echo 'checked';}?>
	  >Wedding<br />
      <input type="radio" name="folderI" value="kiddie"
	  <?php if (isset($_POST['folderI']) && ($_POST['folderI'] == 'kiddie')) {echo 'checked';}?>			 
	  >Kiddie<br />
      <input type="radio" name="folderI" value="debut"
	  <?php if (isset($_POST['folderI']) && ($_POST['folderI'] == 'debut')) {echo 'checked';}?>			 
	  >Debut<br />
      <input type="radio" name="folderI" value="other"
	  <?php if ((isset($_POST['folderI']) && ($_POST['folderI'] == 'other')) || (!$_POST)  ) {echo 'checked';}?>			 
	  >Other<br />

</form>

</div>
<br/>

</body>
</html>