<?php

//session timeout logs out the user automatically after a period of inactivity
require_once('./includes/user_session_timeout.inc.php');


// set the max upload size per file in bytes  
$max = 2 * 1048576;			//  2 MB	limit in 000webhost


if (isset($_SESSION['user_ID']))
{
    $userID = $_SESSION['user_ID']   ;       
}
else
{$userID = 0;}

//check if user comes from an admin page, require log-in if outside admin
//require_once('./includes/check_admin.inc.php');

// prepare database connection
require_once('./includes/my_connection.inc.php');
$conn = dbConnect('read');

// initialize flags 
$OK = false; 
$done = false;
$empty = false;


//get from GET ARRAY

if (isset($_GET['reserve_ID']) && !$_POST)
{
    $reserve_ID = $_GET['reserve_ID'];
    
    //prepare SQL string
    
    $sql = "SELECT reserve_ID,
                    message_ID,
                    DATE_FORMAT(date_added, '%b-%d-%Y %h:%i %p ') AS date_added_f,
                    message,
                    sender
            FROM messages
            WHERE reserve_ID = $reserve_ID  
            ORDER BY date_added_f";
    
    $resultDB = $conn->query($sql) or die(mysql_error());   
    
    //$result = $conn->query($sql);
    
    
    $numRows  = $resultDB->num_rows;
    
    
    //echo $sql;
    //echo $_SESSION['user_ID'];
    //$message = $conn->errorInfo();
    //  echo $message;
    
    // or die(mysql_error());       
}


if (isset($_POST['PostMsg']))
{
    
	if (isset($_POST['attachments'])==false)
	{
		$_POST['attachments'] = array();
	}
    
    
    
    if (isset($_GET['reserve_ID']) )
    {
    $reserve_ID = $_GET['reserve_ID'];
    }

    // if there are no errors in date, proceed with update
    //if(($datePast == false) && ($date1week == false))
    //{
   //prepare update query, time is not included
    //$sql = "UPDATE messages
    //        SET message = ?, date = ? 
     //       WHERE reserve_ID = ?";
     
     
     
     if (!empty($_POST['message']))
     {
        
        $Manila = new DateTimeZone('Asia/Manila');
        $now = new DateTime('now',$Manila);
        
        //$curDate = $now->format('D,M d Y h:i a');
        
        $curDateMysql = $now->format('Y-m-d H:i:s');
     
     
        $msg = htmlentities($_POST['message'],ENT_QUOTES);
        
        $senderName = $_SESSION['username'];
     
        $sql = "INSERT INTO messages
			(reserve_ID,date_added,message,sender)
			VALUES (" . "$reserve_ID, '$curDateMysql', '$msg','$senderName')";
            
        if (!$conn->query($sql)) 				//query is not executed by the connection
        { $messageNotPosted = $conn->error; 		//get error
        } 		
        else
        { $done = true;}          //else reservation is successfully saved


	  $sql = "SELECT message_ID
			 FROM messages
			 WHERE reserve_ID = $reserve_ID
			 AND date_added = '$curDateMysql' ";
	  
	  $result = $conn->query($sql) or die(mysqli_error());
	  $row = $result->fetch_assoc();
	  
	  $id = $row['message_ID'];
      
      $result->free_result();
    
    
        foreach ($_POST['attachments'] as $fileA) {
        
                $sql = "INSERT INTO attachments
                        (reserve_ID,message_ID,date_uploaded,filename)
                        VALUES (" . "$reserve_ID,$id,  NOW(), '$fileA')";
            
            
                  if (!$conn->query($sql)) 				//query is not executed by the connection
                  { //$messageNotSaved = $conn->error; 		//get error
                  } 		
                  else
                  { //$fileUploaded = true;}          //else reservation is successfully saved
                    }
            }
    }
    else
    {
      $empty = true;     
    }
        
    //$stmt->prepare($sql);		//prepare SQL
    //bind paramaters to query
    //$stmt->bind_param('ssi', $_POST['location'], $_POST['date'], $_POST['reserve_ID']);

    //$done = $stmt->execute();   //execute query
    
    //}
    

}

//multiple uploads are allowed, no limit is imposed on total size of all selected uploaded files
//the limit above applies to each individual file
// you can customize error messages and other options at the class file (Upload_class.php) , please also see that code

if (isset($_POST['uploadA']))
{

    
	$dest = './admin/uploads/attach/' ;			// destination of uploaded files
	
		require_once('./admin/upload_class.php');
          try { 
            $upload = new c1_Upload($dest); 
			
			//change max size of file allowed through $max var on top
			$upload->setMaxSize($max);
			
            
            $upload->setPermittedTypes(array('image/gif', 
									'image/jpeg', 
									'image/jpg',
									'image/pjpeg', 
									'image/png',
									'image/tiff',
									'application/pdf',
                                    'application/x-pdf',
									 'text/plain', 
									 'text/rtf',
									'application/msword',
									'application/vnd.ms-powerpoint',
									'application/vnd.ms-excel',
									'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
									'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
									'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
									'application/vnd.openxmlformats-officedocument.presentationml.template',
									'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
									'application/vnd.openxmlformats-officedocument.presentationml.presentation',
									'application/vnd.openxmlformats-officedocument.presentationml.slide',
									'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
									'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
									'application/vnd.ms-excel.addin.macroEnabled.12',
									'application/vnd.ms-excel.sheet.binary.macroEnabled.12'
										)); 
            

			
			$upload->move();				//add duplicate file if exist and rename
			
            $result = $upload->getMessages(); 
          } catch (Exception $e) { 
            echo $e->getMessage(); 
          }	
	
}

// redirect page on success or if $_GET['reserve_ID'] not defined 
if ($done  || !isset($_GET['reserve_ID'])) { 
  //header('Location: ' . './memberpage.php'); 	//redirect to list of reservations
  
  // "message.php?reserve_ID=<?php echo $row['reserve_ID'];
  header('Location: ' . "./message.php?reserve_ID=$reserve_ID"); 	//redirect to list of reservations
  
  exit; 
}

else
{
     $reserve_ID = $_GET['reserve_ID'];
    
    //prepare SQL string
    
    $sql = "SELECT reserve_ID,
                    message_ID,
                    DATE_FORMAT(date_added, '%b-%d-%Y %h:%i %p ') AS date_added_f,
                    message,
                    sender
            FROM messages
            WHERE reserve_ID = $reserve_ID  
            ORDER BY date_added_f";
    
    $resultDB = $conn->query($sql) or die(mysql_error());   
    
    //$result = $conn->query($sql);
    
    
    $numRows  = $resultDB->num_rows;
}
// display error message if query fails 
//if (isset($stmt) && !$OK && !$done ) { 
//  $error = $stmt->error; 
//}


?>




<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

                                                                                     
<title>Member Page Main</title>

<!--link href="admin.css" rel="stylesheet" type="text/css"-->

 <style type="text/css">
 
 body {
     
     background-color: thistle;
 }
 
 h1 {color:black;
        font: bold 20px sans-serif;}
        
  h2 {color:black;
        font: bold 15px sans-serif;}
        
label {
        font: bold 14px sans-serif;
        }
        
a
{
   color: #8a008a;
   font: bold 12px;
   text-decoration: underline;
}
a:visited
{
   color: #8a008a;
   font-weight: bold 12px;
}
a:active
{
   color: #8a008a;
   font-weight: bold 12px;
}
a:hover
{
   color: blue;
   text-decoration: none;
   font-weight: bold 12px;
}

img {
opacity:0.7;
}

img:hover
    {
        opacity:1;
    }
    
span.a2 {
    font: 18px "Trebuchet MS", Helvetica, sans-serif;
    color: #8a008a;
}

span.a1 {
    font: 18px "Trebuchet MS", Helvetica, sans-serif;
    color: #800040;
}

 </style>
 


</head>

<body>
    
<!--------------------------- USERNAME and LOGOUT ------------------------------->


<div style="border: 5px purple groove;
		margin:20px;
                background-color:pink;
                height:100px;">
    
    <div style="color:purple;
                    font: bold 15px  'Lucida Sans Unicode', 'Lucida Grande', sans-serif;
                    width:75%;
                    float:left;
                    padding:10px;">
    
    <?php
    
        echo "You are logged in, <u>" . $_SESSION['username'] . "</u>";            //display username
        echo "<br>";
        echo "<br>";
        include('./includes/user_logout.inc.php');        // log out button
              
    ?>
    
    </div>


    <div>
    
        <div style="float:left;padding:10px;">
        <a href="./memberpage.php">
        <img src="./images/cooltext774339533.png" onmouseover="this.src='./images/cooltext774339533MouseOver.png';" onmouseout="this.src='./images/cooltext774339533.png';" />
        </a>
        </div>
        
        <!--div style="float:left;padding:10px;">
        <a href="./home.php">
        <img src="./images/cooltext774344919.png" onmouseover="this.src='./images/cooltext774344919MouseOver.png';" onmouseout="this.src='./images/cooltext774344919.png';" />
        </a>
        </div-->
    
    </div>


</div>


<!-------------------------------------------------------------------------------->


<div style="width:700px; border: 2px pink outset; margin: 20px auto; padding: 20px; font-family: sans-serif;">

<h1>Member's Page</h1> 


<p>

<span class="a2">
<?php
    echo "Welcome  <u>" . $_SESSION['username'] . "</u>";
    //echo "wa" . $userID . "numrows:" . $numRows;
?>
</span>

</p>

<br />


<h1>Messages</h1>

<?php

if (isset($messageNotPosted))
{
    echo $messageNotPosted;
    
}





if (!$numRows)
{ ?>
    <p style="color:blue; font: bold 15px sans-serif"> There are no messages yet </p>
<?php }



while($row = $resultDB->fetch_assoc())
{ ?>


<div style="margin: 20px; border: 5px #ff0080 ridge; padding: 5px; ">
<span style='color:blue;font-weight:bold;'> <?php echo $row['sender']; ?>
</span>
<span style='color:green;font: italic 14px sans-serif;'> <?php echo $row['date_added_f']; ?>
</span>
<br/>

<?php //$row = $result->fetch_assoc(); 
 echo $row['message']; ?>

<br />

<?php
    //get files for this message (if any)
    
    
    $rID = $row['reserve_ID'];
    $mID = $row['message_ID'];
    
    $sql = "SELECT filename
            FROM attachments
            WHERE message_ID = $mID
            AND reserve_ID = $rID";
            
	  $resultDB2 = $conn->query($sql) or die(mysqli_error());
	  //$row2 = $resultDB2->fetch_assoc();
      $numRows2  = $resultDB2->num_rows;
      
      
      if ($numRows2)
      {
        
        echo "<div style='font:12px sans-serif;
                    color: purple;
                    margin:20px;
                    padding: 10px;
                    border: 3px purple solid;'>
                    Attached files:";
        while($row2 = $resultDB2->fetch_assoc())
        {
          //echo $row2['filename'];
          echo "<br />";
          $file3 =  $row2['filename'];
          echo '<a target="_blank" href="./admin/uploads/attach/' . $file3 . '">' . $file3  . '</a>';
        }
        echo "</div>";
      }

      $resultDB2->free_result();
      
    
    /*
        $sql = "SELECT reserve_ID, message_ID, date_added, message
            FROM messages
            WHERE reserve_ID = $reserve_ID  
            ORDER BY date_added";
            
	  $sql = "SELECT message_ID
			 FROM messages
			 WHERE reserve_ID = $reserve_ID
			 AND date_added = '$curDateMysql' ";
	  
	  $result = $conn->query($sql) or die(mysqli_error());
	  $row = $result->fetch_assoc();
	  
	  $id = $row['message_ID'];    
    
    
    foreach ($_POST['attachments'] as $fileA) {
    
            $sql = "INSERT INTO attachments
                    (reserve_ID,message_ID,date_uploaded,filename)
                    VALUES (" . "$reserve_ID,$id,  NOW(), '$fileA')";
        
        
              if (!$conn->query($sql)) 				//query is not executed by the connection
              { //$messageNotSaved = $conn->error; 		//get error
              } 		
              else
              { //$fileUploaded = true;}          //else reservation is successfully saved
        }
       */        

?>

<br />

</div>
 
<?php }
$resultDB->free_result();

?>

    <div style="border: 5px red inset; padding: 10px;">
    
    
        <p style='color:purple;font: bold 16px sans-serif'>Post new message</p>
        
        <p style='color:purple;font: bold 12px sans-serif'>You can attach files here.
        <a onclick="uploadG()" style="cursor: pointer; font-size: 12px;">Attach Guidelines</a>
        </p>
                   

        <script>
         
        function uploadG() {
          
          alert('*Permitted file types are images, text file, and documents.' + 
                '\n*If you have files to attach, please attach them before typing a message.' +
                '\n*Click browse to select files.' +
                '\n*You can select multiple files in the file window (eg. by using Ctrl + Right Click).' +
                '\n*Maximum upload size is 2MB (total size of all files to upload)' +
                '\n*After selecting the files, click Upload.' +
                '\n*After upload, check the file checkbox to attach it to the message.' +
                '\n*You can also preview the file.' +
                '\n*Proceed with your message and click "Post Message".');
            //el = document.getElementById("overlay");
            //el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";
        }
         
        </script>
        
        
        <form action="" method="post" enctype="multipart/form-data" id="uploadImage">
            
            <label for="image">Upload file:</label>
            <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max; ?>">
            
            <input type="file" name="files[]"  multiple>
            
            <input type="submit" name="uploadA"  value="Upload">
            
        
        </form>
    
    
        <form method="post" action="" name="postmsg">
            
            <div style="margin:20px; color:darkgreen; font: bold 14px sans-serif;">
            
            <?php
            
            
            
                
                if (isset($_POST['uploadA']) && isset($result)) { 
                  
                  echo '<ul>'; 
                  foreach ($result as $message) {
                    echo "<li>$message</li>"; 
                  } 
                echo '</ul>';
                  
                
                if (is_array($upload->getFilenames()))
                {
                foreach ($upload->getFilenames() as $file) { ?>
                <input type="checkbox" name="attachments[]" value="<?php echo  $file ;?>" id="<?php echo  $file ;?>"><?php echo  $file ;?>
                <a target="_blank" href="./admin/uploads/attach/<?php echo  $file ;?> ">PREVIEW FILE</a><br/>
                <?php
            
                
                     }
                }
                 
                
                }
                
            ?>
                
            
            </div>
            
            
            
            <?php if ($empty)
            { ?>
            <p style="color:red; font: bold 15px sans-serif"> The message field is empty </p>
            <?php } ?>
            <div style="margin-left:20px; width: 700px;">
              <textarea name="message" id="message" cols=75 rows=10 
                  data-hint="" maxlength="10000" placeholder="" style="resize:none;" ></textarea>
            </div>
            
            <br />
            <center>
            
            <input type="submit" name="PostMsg" value="Post Message">
                
            </center>
        </form>
    
    </div>

</div>

</body>

</html>