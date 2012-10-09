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

function gen_filename($seed){
    // bind some params to more readable variables and get a starting filename
    $cur_date=date("r");
    $target_name=sha1($seed . $cur_date . mt_rand());
    $target_name=substr($target_name,0,1) . "/$target_name";
    return $target_name;
}

if (array_key_exists('uploadedfile', $_FILES)){

    $check_quota_sql="SELECT SUM(size) FROM files WHERE user_id=$1;";
    $store_file_sql="INSERT INTO files(user_id,file_name,location,size)" .
        "VALUES ($1, $2, $3, $4);";

    // Check the current amount of space in use. This aggregate must always
    // return exactly 1 row, unless there is a database or schema failure. 
    $params=array($_SESSION['user_id']);
    $disk_usage_res=pg_query_params($check_quota_sql, $params);
    assert('$disk_usage_res /*Unknown database error*/');
    $disk_usage_row=pg_fetch_array($disk_usage_res);
	$disk_usage = $disk_usage_row[0];
    pg_free_result($disk_usage_res);

    //Check if we're going to go over the quota
    $file_size=$_FILES['uploadedfile']['size'];
    if ($disk_usage + $file_size > $_SESSION['user_quota']){
        $msg="This would exceed your quota of $_SESSION[user_quota] bytes.";
        header("Location: $_SERVER[PHP_SELF]?msg=$msg");
        die($msg);
    }

    // Generate a filename. Try until we get one that's not in use. The
    // likelyhood that this fails to terminate is miniscule.
    // A race condition is possible, but even more unlikely than collisions,
    // and as the user does not control all of the input data to the hash,
    // manufactured ones should be practically impossible.

    $src_file_name=basename($_FILES['uploadedfile']['name']);
    $target_file_name=gen_filename($src_file_name);
    while(file_exists("$FILE_STORE/$target_file_name")){
        $file_name=gen_filename($target_file_name);
    }

    if(!move_uploaded_file($_FILES['uploadedfile']['tmp_name'], 
            "$FILE_STORE/$target_file_name")) {
        $msg="Failed to save $src_file_name as $FILE_STORE/$target_file_name";
        trigger_error($msg);
        header("Location: $_SERVER[PHP_SELF]?msg=Error uploading file.");
        die($msg);
    }

    $params=array($_SESSION['user_id'], $src_file_name, $target_file_name,
        $file_size); 
    $file_res=pg_query_params($conn, $store_file_sql, $params);
    if (!$file_res || pg_affected_rows($file_res) != 1){
        $msg="Failed to insert file into database. Deleting file.";
        // Nuke the file, since we can't track it. No need to check returns --
        // if this fails, there's not anything we can do about it.
        unlink("$FILE_STORE/$file_name");
        trigger_error($msg);
        header("Location: $_SERVER[PHP_SELF]?msg=Error uploading file.");
        die($msg);
    }
}

if (array_key_exists('add', $_POST)){
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
	trigger_error("Trace: adding file to group");
 }
	
	
// Delete from the group below!!!

if (array_key_exists('delete', $_POST)){
    $delete_sql="DELETE FROM group_members WHERE group_id = $1 and user_id = $2";
    pg_prepare("del_file", $delete_sql);

   
    foreach ($_POST['userlist'] as $myfile){
        $params=array($_GET['group_id'],$myfile);
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
	trigger_error("Trace: removing member from group");
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


 $query = "SELECT * FROM users"; 
        $result = pg_query($conn, $query); 
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
      
  
	<title>
    Group Files
    </title>
      
      
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
	  
			
			?>"
         <tr><td><input type="submit" name='delete' value="Delete Files" /></td></tr>
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
