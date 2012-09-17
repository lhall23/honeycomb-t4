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
		die("User or password not set. How did you get here?");
	}

	// Get user info from database. Only retrieve users who have authenticated
	// their accounts.
	$sql="SELECT user_id,password FROM users 
		WHERE user_name=$1 AND auth_hash IS NULL;";
	$params=array($_POST['user_name']);
	$results=pg_query_params($conn, $sql, $params);
	assert('pg_num_rows($results) <= 1 /*uniqueness violation in database*/');

	//Bail and reload the page if we didn't find a user
	$row=pg_fetch_array($results);		
	if (! $row){
		//header("Location: $_SERVER[PHP_SELF]");
		die("User not found.");
	}

	//Does the password match?
	if ($_POST['password'] == $row['password']){
		session_start();
		$_SESSION['user_name']=$_POST['user_name'];
		$_SESSION['user_id']=$row['user_id'];
		header("Location: index.php");
		die("Done loading user.");
	} else {
		header("Location: $_SERVER[PHP_SELF]");
		echo "\"$_POST[password]\" != \"$row[password]\".\n";
		die("Bad password.");
	}
}
?>
<HTML> 
<HEAD>
	<TITLE>Honeycomb Login</TITLE>
</HEAD>
<BODY>

<link href="YUI/2.8.2r1/build/fonts/fonts-min.css" rel="stylesheet" type="text/css">
<link href="YUI/2.8.2r1/build/treeview/assets/skins/sam/treeview.css" rel="stylesheet" type="text/css">
<script src="YUI/2.8.2r1/build/yahoo-dom-event/yahoo-dom-event.js" type="text/javascript"></script>
<script src="YUI/2.8.2r1/build/treeview/treeview-min.js" type="text/javascript"></script>
<script type="text/xml">
<!--
<oa:widgets>
  <oa:widget wid="2444522" binding="#OAWidget" />
</oa:widgets>
-->
</script>
<table cellspacing="1" cellpadding="0" border="0"
 bgcolor="black" id="shell" height="639" width="1168">
   <tr height="50">
      <td height="83" colspan="2" bgcolor="white">
         <table title="Banner" id="banner" border="0">
            <tr><td width="1195"><a href="http://www.flickr.com/photos/87155180@N06/7980116517/" title="TheHoneycombBanner1 by devgurl36!, on Flickr"><img src="http://farm9.staticflickr.com/8170/7980116517_004c52431c_b.jpg" width="1157" height="137" alt="TheHoneycombBanner1"></a></td></tr>
         </table>
      </td>
   </tr>
   <tr height="200">
      <td width="260" bgcolor="white">
         <table id="navigation" title="Navigation" border="0">
         
           <link href="YUI/2.8.2r1/build/fonts/fonts-min.css" rel="stylesheet" type="text/css">
<link href="YUI/2.8.2r1/build/treeview/assets/skins/sam/treeview.css" rel="stylesheet" type="text/css">
<script src="YUI/2.8.2r1/build/yahoo-dom-event/yahoo-dom-event.js" type="text/javascript"></script>
<script src="YUI/2.8.2r1/build/treeview/treeview-min.js" type="text/javascript"></script>
<script type="text/xml">
<!--
<oa:widgets>
  <oa:widget wid="2444522" binding="#OAWidget" />
</oa:widgets>
-->
</script>
<table cellspacing="1" cellpadding="0" border="0"
 bgcolor="black" id="shell" height="639" width="1168">
   <tr height="50">
      <td height="83" colspan="2" bgcolor="white">
         <table title="Banner" id="banner" border="0">
            <tr><td width="1195"><a href="http://www.flickr.com/photos/87155180@N06/7980116517/" title="TheHoneycombBanner1 by devgurl36!, on Flickr"><img src="http://farm9.staticflickr.com/8170/7980116517_004c52431c_b.jpg" width="1157" height="137" alt="TheHoneycombBanner1"></a></td></tr>
         </table>
      </td>
   </tr>
    <tr height="200">
     <td width="260" bgcolor="white">
    
     
       <table id="navigation" title="Navigation" border="0">
         
            <tr><td>Links!</td></tr>
            <tr><td>Links!</td></tr>
            <tr><td>Links!</td></tr>
        </table>
      </td><td width="397" bgcolor="white">

         <table title="Content" id="content" border="0">
            <tr><td>
            
            </td></tr>
         </table>
      </td>
   </tr>
</table>
 
<style>
		.ygtvitem {
			font-family:Verdana, Geneva, sans-serif;
		}
	</style>

       
            <tr><td><form action="<?php echo $_SERVER['PHP_SELF']; ?>" 
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
			<tr>
				<td><input name="login" type="hidden"</td>
				<td><input value="Login" type="submit"></td>
			</tr>
		</table>
	</form></td></tr>
         </table>
      </td>
   </tr>
</table>
 
<style>
		.ygtvitem {
			font-family:Verdana, Geneva, sans-serif;
		}
	</style>

	
</BODY>
</HTML>

