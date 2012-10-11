<?php
    //Display a user profile, list the files, and do most of the heavy lifting
?>
<HTML>
<HEAD>
    <TITLE>Group Admin</TITLE>
</HEAD>
<BODY>
<?php
    require_once('include/session.php');
    require_once('include/conf.php');
    // Where the file is going to be placed 


if (!array_key_exists('group_id', $_GET)){
    die("Please supply a group id.");
}

$get_group_sql="SELECT group_name,owner_id FROM groups WHERE group_id=$1;";
$params=array($_GET['group_id']);
$group_res=pg_query_params($conn,$get_group_sql,$params);
if (!$group_res || pg_num_rows($group_res) > 1){
    die("Unrecoverable database error.");
}

$row=pg_fetch_array($group_res);
if (!$row || $row['owner_id'] != $_SESSION['user_id']){
    die("This group does not exist, " . 
        "or you do not have permission to administer it.");
}

if (array_key_exists('add', $_POST) && array_key_exists('userlist', $_POST)){
    $insert_sql="INSERT INTO GROUP_MEMBERS(group_id,user_id) VALUES($1,$2)";
    pg_prepare("ins_file", $insert_sql);

   
    foreach ($_POST['userlist'] as $myfile){
        $params=array($_GET['group_id'],$myfile);
        $query_res=pg_execute($conn, "ins_file", $params);

        if (!$query_res || pg_affected_rows($query_res)!=1){
            $msg="Database error.";
            trigger_error($msg);
            die($msg);
        }
        $row=pg_fetch_assoc($query_res);
        $file_loc=$row['location'];
        
        pg_free_result($query_res);
        
	}
 }
	
	
// Delete from the group below!!!

if (array_key_exists('delete', $_POST) && 
        array_key_exists('userlist', $_POST)){
    $delete_sql="DELETE FROM group_members WHERE group_id = $1 and user_id = $2";
    pg_prepare("del_file", $delete_sql);

   
    foreach ($_POST['userlist'] as $myuser){
        $params=array($_GET['group_id'],$myuser);
        $query_res=pg_execute($conn, "del_file", $params);

        if (!$query_res || pg_affected_rows($query_res)!=1){
            $msg="Database error.";
            trigger_error($msg);
            die($msg);
        }
        $row=pg_fetch_assoc($query_res);
        $file_loc=$row['location'];
        
        pg_free_result($query_res);
        
	}
 }
?>

<?php
    print "Welcome $_SESSION[user_name]<br>";
    if (array_key_exists("msg", $_GET)){
        // Does this actually sanitixe enough, or can we still end up with XSS
        // attacks here?
        echo htmlentities($_GET['msg']);
    }
    echo "<br>";
?>

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
    bgcolor="black" id="shell" height="639" width="1168">
  <tr height="50">
    <td height="83" colspan="2" bgcolor="white">
        <img src="images/Honeycomb Logo 2.jpg" 
            width="1157" height="137" alt="Honeycomb Logo 2"></td>
  </tr>
  <tr height="200">
    <td width="216" bgcolor="white">
      <table id="navigation" title="Navigation" border="0">
            <tr><td> </td> </tr> 
      </table>
    <img src="images/Side Bar Pics.jpg" width="216" height="864" />    </td>
    <td width="959" bgcolor="white">
      <form enctype="multipart/form-data" 
                  action="<?php 
echo "$_SERVER[PHP_SELF]?group_id=$_GET[group_id]";	
					?>" method="POST">
        <table title="FileList" id="FileList" border="0">
        <?php 


 $query = "SELECT DISTINCT user_name, user_id FROM users 
            LEFT JOIN group_members USING(user_id) 
            WHERE group_id != $1 OR group_id IS NULL"; 
 $params=array($_GET['group_id']);
        $result = pg_query_params($conn, $query, $params); 
        if (!$result) { 
            $msg="Failed to list users.";
            trigger_error($msg); 
            die($msg); 
        } 

        while($myrow = pg_fetch_assoc($result)) {
            echo '<tr><td><input type="checkbox" ';
            printf('value="%s" name="userlist[]"/> %s', 
                $myrow['user_id'], $myrow['user_name']); 
            echo '</td></tr>';
        } 


        ?> 
          <tr><td><input type="submit" name='add' value="Add Members to Group" /></td></tr>
        </table>
      </form>
      
      <form enctype="multipart/form-data" 
          action="<?php echo "$_SERVER[PHP_SELF]?group_id=$_GET[group_id]";?>" method="POST"> <table title="Content" id="content" border="0">
          <tr>
            <td><input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
        <table title="Content" id="content" border="0">
         
         
         
           
			<?php
			 
	  
        $query = "SELECT * FROM group_members JOIN users USING(user_id) WHERE group_id = $1;";
		$params = array($_GET['group_id']);
        $result = pg_query_params($conn, $query,$params); 
        if (!$result) { 
            $msg="Failed to get users in group.";
            trigger_error($msg); 
            die($msg); 
        } 

        while($myrow = pg_fetch_assoc($result)) {
            echo '<tr><td><input type="checkbox" ';
            printf('value="%s" name="userlist[]"/> %s', 
                $myrow['user_id'], $myrow['user_name']); 
            echo '</td></tr>';
        } 
	  
			
			?>
         <tr><td><input type="submit" name='delete' value="Delete Members" /></td></tr>
          <tr>
            <td>
                <a href="groupProfilePage.php?group_id=<?php 
                    echo $_GET['group_id'];
                    ?>">Group Files</a>
            </td>
          </tr>
          <tr>
            <td><a href= "login.php?logout"> Logout</a></td>
          </tr>
           
         
        </table>
      </td><td width="4" bgcolor="white">
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
