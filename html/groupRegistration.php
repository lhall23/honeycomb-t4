<?php
/* 
 *
 * Allow the user to create a new group
 *
 */
// Yes, this makes a connection when we don't neccesarily need it. It's better
// than repeating the include inside multiple branches, though
require_once('include/conf.php');
require_once('include/session.php');


//Is there a user trying to register?
if (array_key_exists('register', $_POST)){

if (array_key_exists('group_name', $_POST))
{

    if(!ctype_alnum($_POST['group_name'])){
        $msg="Please only use letters and numbers in the Group Name.";
    header("Location: $_SERVER[PHP_SELF]?msg=$msg");
        die("Illegal characters in group name.");
    }

  //Get Group info from database
  $params=array($_POST['group_name'],$_SESSION['user_id']);
  $sql="INSERT INTO groups(group_name,owner_id) VALUES 
    ($1,$2) RETURNING group_id;";
  $results=pg_query_params($conn, $sql, $params);
  
  $row= pg_fetch_array($results);
  $sql="INSERT INTO group_members(group_id,user_id) VALUES 
    ($1, $2);";
  $params=array($row['group_id'], $_SESSION['user_id']);
  $results=pg_query_params($conn, $sql, $params);
  if (!$results || pg_affected_rows($results) != 1) {
    //There has to be a more elegant way to do this.
    $error=pg_last_error();
    if (strpos($error, 'group_name_key') !== False)
	{
      $msg="That group name is already in use.";  
    } 
	else {
      die("Unknown error.");
    	 }
		 
    header("Location: $_SERVER[PHP_SELF]?msg=$msg");
  } else{
	  
	header("Location: groupProfilePage.php?group_id=$row[group_id] ");
    die("Group created succesfully.");
  }
}
  else 
  { 
  die("Group name not set. How did you get here?");
  }
}
?>

<HTML> 
<HEAD>
<TITLE>groupRegister</TITLE>
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
          method="post" id="register">
        <table title="Content" id="content" border="0">
          <tr>
            <td>Group Name: </td>
            <td><input type="text" name="group_name"></td>
          </tr>
      
          <tr>
            <td><input type="hidden" name="register"></td>
            <td><input type="submit" value="Create"></td>
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

