<?php

if (isset($_POST['send']))
{
	echo $_POST['username'];
}

?>

<form action="" method="post">

<p>Enter username</p>

<label for="username">Username:</label>
<input type="text" id="username" name="username" value="<?php if (isset($_POST['username'])) { echo $_POST['username'];} ?>"/>
<input type="submit" id="send" name="send" value="SEND NAME" />

</form>
