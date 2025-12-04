<?php
	session_start();
	session_unset();
	session_destroy();
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Log Out</title>
	</head>
	<div class="main-body">
		<H2> You are now logged out </H2> 
		<p> Would like to log in again? <a href="login.php">Log in</a>  </p>
	</div>
</html>