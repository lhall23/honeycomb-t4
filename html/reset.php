<?php
/* 
 * reset.php
 * -Matthew Powell 
 *
 * Lets users make a new password
 *
 */

require_once('include/conf.php');

$MAIL_SUBJECT="[Honeycomb] Password Reset";
$MAIL_TEXT="Please click the following link to Reset your Password" .
  " process. $URL_BASE/resetForm.php?verify=";

//Is there a user trying to reset their password?
if (array_key_exists('reset', $_POST)){

  if (!array_key_exists('email', $_POST) ){//checks to see if an email was sudmited
    die("No email provided");
  }
  $Email=strtolower($_POST['email']);
    $Email=$_POST['email'];
	if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {//if Email is invalid kick out
		die ("This email address is invalid.");
	}

  //Get user info from database
  
  $sql="SELECT COUNT(*) FROM users WHERE email=$1;";
  
  
  $params=array(strtolower($Email));
  $results=pg_query_params($conn, $sql, $params);//looks to see if email is in table
	if (!$results) {
		die("WE messed up!!!");
	}
	else if(1==pg_num_rows($results)) {//email has been found
		$token=md5(mt_rand());
		$sql="UPDATE users Set auth_hash=$2 WHERE email=$1;";
		$params=array($Email,$token);
		$results=pg_query_params($conn, $sql, $params);//set the authintication hash
		mail($Email, $MAIL_SUBJECT, $MAIL_TEXT . $token);//sends email to user with link
		die("Check your inbox for an email that will reset your password" . 
		"Please follow the directions in your email.");
	}
    else
		{
		die("Your Email was not found");
		}
}


?>

<HTML> 
<HEAD>
<TITLE>PasswordReset</TITLE>
</HEAD>
<BODY>
<link href="include/yui/2.8.2r1/build/fonts/fonts-min.css" 
    rel="stylesheet" type="text/css">
<link href="include/yui/2.8.2r1/build/treeview/assets/skins/sam/treeview.css" 
    rel="stylesheet" type="text/css">
<script src="include/yui/2.8.2r1/build/yahoo-dom-event/yahoo-dom-event.js" 
    type="text/javascript"></script>
<script src="include/yui/2.8.2r1/build/treeview/treeview-min.js" 
    type="text/javascript"></script>
<script type="text/xml">
  <!--
  <oa:widgets>
    <oa:widget wid="2444522" binding="#OAWidget" />
  </oa:widgets>
  -->
</script> 
<table cellspacing="1" cellpadding="0" border="0"
    id="shell" height="639" width="1168">
  <tr height="50">
    <td height="83" colspan="2" bgcolor="white">
      <table title="Banner" id="banner" border="0">
        <tr>
          <td width="1195">
            <img src="images/Honeycomb Logo 2.jpg" 
                width="1157" height="137" alt="Honeycomb Logo 2" />
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr height="200">
    <td width="218">
      <img src="images/Side Bar Pics.jpg" alt="sidebanner" 
          width="216" height="864" />
    </td>
    <td>
      <form action="<?php echo $_SERVER['PHP_SELF']; ?>" 
          method="post" id="reset">
        <table title="Content" id="content" border="0">
          <tr>
            <td>Email: </td>
            <td><input type="text" name="email"></td>
          </tr>
          <tr>
            <td><input type="hidden" name="reset"></td>
            <td><input type="submit" value="Reset"></td>
          </tr>
          <tr>
            <td></td>
            <td>
<?php
    if (array_key_exists('msg', $_GET)){
        echo "$_GET[msg]";
    }
?>
            </td>
          </tr>
        </table>
      </form>
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

