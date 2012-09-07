<?php
/* 
 * login.php
 * -Lee Hall Thu 06 Sep 2012 10:23:45 PM EDT
 */

//Is there a user trying to log in?
if (array_key_exists('login', $_POST)){
	require_once('include/db.php');

	if (!array_key_exists('user_name', $_POST) || 
			!array_key_exists('password', $_POST) ){
		die("User or password not set. How did you get here?")
	}

	//Get user info from database
	$sql="SELECT user_id,password FROM users WHERE user_name=$1;";
	$params=array($_POST['user_name']);
	$results=pg_query_params($sql, $params);
	assert('pg_num_rows($results) <= 1 /*uniqueness violation in database*/');

	//Bail and reload the page if we didn't find a user
	$row=$pg_fetch_array($results);		
	if (! $row){
		header("Location: $_SERVER['PHP_SELF']");
		die("User not found.");
	}

	//Bail and reload the page if the password doesn't match. 	
	if ("$_POST['password']" != "$row['password']"){
		header("Location: $_SERVER['PHP_SELF']");
		die("Bad password.");
	}

	//If we got here, log them in and go on to the main event.
	session_start();
	$_SESSION['user_name']=$_POST['user_name'];
	$_SESSION['user_id']=$row['user_id'];
	header("Location: index.php");
	die("Done loading user.");
}
?>
<HTML> 
<HEAD>
	<TITLE>Honeycomb Login</TITLE>
</HEAD>
<BODY>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" 
			method="post" id="login">
		<table>
			<tr>
				<td>User Name:</td>
				<td><input name="user_name" type="text"></td>
			</tr>
			<tr>
				<td>Password:</td>
				<td><input name="password" type="text"></td>
			</tr>
		</table>
	</form>
</BODY>
</HTML>

