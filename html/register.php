<?php
/* 
 * register.php
 * -Lee Hall Sat 08 Sep 2012 06:05:55 PM EDT
 */
$MAIL_SUBJECT="[Honeycomb] Registration";
$MAIL_TEXT="Please click the following link to finish the registration" .
	" process. $_SERVER[PHP_SELF]?verify=";

// Yes, this makes a connection when we don't neccesarily need it. It's better
// than repeating the include inside multiple branches, though
require_once('include/db.php');

//Is there a user trying to register?
if (array_key_exists('register', $_POST)){

	if (!array_key_exists('user_name', $_POST) || 
			!array_key_exists('password', $_POST) || 
			!array_key_exists('email', $_POST) ){
		die("User, password or email not set. How did you get here?");
	}

	//Get user info from database
	$token=md5(mt_rand() . $_POST['user_name']);
	$sql="INSERT INTO users(user_name,password,email,auth_hash) VALUES 
		($1, $2, $3, $4);";
	$params=array($_POST['user_name'], $_POST['password'], 
		$_POST['email'], $token);
	$results=pg_query_params($conn, $sql, $params);
	if (pg_affected_rows($results) != 1) {
		die("Something failed -- probably a uniqueness violation.");
	} else {
		mail($_POST['email'], $MAIL_SUBJECT, $MAIL_TEXT . $token);
		die("Account created succesfully." . 
			"Please follow the directions in your email.");
	}
}

//Is someone trying to get their account verified?
if (array_key_exists('verify', $_GET)){
	$sql="SELECT user_id FROM users WHERE auth_hash=$1;";
	$params=array($_GET['verify']);
	$results=pg_query_params($conn, $sql, $params);
	if (pg_num_rows($results) != 1){
		die("Could not find that account.");	
	}
	$row=pg_fetch_array($results);
	$SESSION['user_id']=$row['user_id'];
	$sql="UPDATE users SET auth_hash=NULL WHERE user_id=$1;";
	$params=array($row['user_id']);
	$results=pg_query_params($conn, $sql, $params);
	if (pg_affected_rows($results) != 1){
		die("Error verifying user.");
	}
	header("Location: login.php");
	die("User validated.");
}
?>

<HTML> 
<HEAD>
<TITLE>Register</TITLE>
</HEAD>
<BODY>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" 
			method="post" id="register">
		<table>
			<tr>
				<td>Username: </td>
				<td><input type="text" name="user_name"></td>
			</tr>
			<tr>
				<td>Password: </td>
				<td><input type="text" name="password"></td>
			</tr>
			<tr>
				<td>Email: </td>
				<td><input type="text" name="email"></td>
			</tr>
			<tr>
				<td><input type="hidden" name="register"></td>
				<td><input type="submit" value="Register"></td>
			</tr>
		</table>
	</form>
</BODY>
</HTML>

