<?php
/* 
 * login.php
 * -Lee Hall Thu 06 Sep 2012 10:23:45 PM EDT
 */

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

