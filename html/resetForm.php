<?php
/* 
 * reset.php
 * -Matthew Powell 
 *
 * Lets users make a new password
 *
 */

require_once('include/conf.php');
//Is someone trying to get their account verified?
if (array_key_exists('verify', $_GET)){
	if (!array_key_exists('Password', $_POST) || 
			!array_key_exists('PasswordC', $_POST))
		if($_POST['Password']==$_POST['PasswordC'])
	{
	$sql="UPDATE users Set password=MD5($2) , Auth_Hash=null WHERE Auth_Hash=$1;";
	
	$params=array($_GET['verfy'],'password');
    $results=pg_query_params($conn, $sql, $params);
	
	}
		else
		{
		die("Invlid or nonmatching password");
		}
    else
	{
	die ("Permision Denied");
	}

}
else
{die("SQL error?");
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
            <td>Password: </td>
            <td><input type="password" name="password"></td>
          </tr>
		   <tr>
            <td>PasswordC: </td>
            <td><input type="password" name="passwordC"></td>
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

