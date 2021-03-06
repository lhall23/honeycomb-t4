<?php
    //Display a user profile, list the files, and do most of the heavy lifting
?>
<HTML>
<HEAD>
    <TITLE>User Profile</TITLE>
</HEAD>
<BODY>
<?php
    require_once('include/session.php');
    require_once('include/conf.php');
    // Where the file is going to be placed 
    $target_path = "$FILE_STORE/$_SESSION[user_name]/";

function gen_filename($seed){
    // bind some params to more readable variables and get a starting filename
    $cur_date=date("r");
    $target_name=sha1($seed . $cur_date . mt_rand());
    $target_name=substr($target_name,0,1) . "/$target_name";
    return $target_name;
}

if (array_key_exists('uploadedfile', $_FILES)){

    $store_file_sql="INSERT INTO files(user_id,file_name,location,size)" .
        "VALUES ($1, $2, $3, $4);";

    //Check if we're going to go over the quota
    $file_size=$_FILES['uploadedfile']['size'];
    if ($_SESSION['user_free_space'] < $file_size){
        $msg="This would exceed your quota.";
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

    //File has been uploaded successfully. Update the quota.
    $_SESSION['user_free_space'] -= $file_size;
}

if (array_key_exists('delete', $_POST)){
    $fetch_sql="SELECT user_id,file_name,location,size 
                    FROM files WHERE file_id=$1";
    pg_prepare("get_file", $fetch_sql);
    $delete_sql="DELETE FROM files WHERE file_id=$1";
    pg_prepare("del_file", $delete_sql);

    // This would be problematic for bulk deletes, since we're iterating over
    // what could be one query, but as the user has to independently select 
    // each file to delete, it doesn't seem like sanitizing the array input is
    // worth the time it would take.
    foreach ($_POST['filelist'] as $myfile){
        $params=array($myfile);
        $query_res=pg_execute($conn, "get_file", $params);

        if (!$query_res || pg_num_rows($query_res)!=1){
            $msg="Can't find file with id $myfile.";
            trigger_error($msg);
            die($msg);
        }
        $row=pg_fetch_assoc($query_res);
        if ($row['user_id'] != $_SESSION['user_id']){
            $msg="$_SESSION[user_name] doesn't own file with id $myfile.";
            trigger_error($msg);
            die($msg);
        }
        $file_loc=$row['location'];
        $file_size=$row['size'];
        if (! unlink("$FILE_STORE/$file_loc")) {
            $msg="Unable to delete from $file_loc.";
            trigger_error($msg);
            die($msg);
        }
        pg_free_result($query_res);
        
        $query_res=pg_execute($conn, "del_file", $params);
        if (!$query_res || pg_affected_rows($query_res) != 1) {
            $msg="Unable to remove $myfile from from database.";
            trigger_error($msg);
            die($msg);
        }
        pg_free_result($query_res);
		$_SESSION['user_free_space'] += $file_size;
    }
}
?>

<?php
    print "Welcome $_SESSION[user_name].<br>";
	print "You have $_SESSION[user_free_space] bytes available.<br>";
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
                  action="<?php echo "$_SERVER[PHP_SELF]";?>" method="POST">
        <h3>My Groups</h3>
        <table title="GroupList" id="GroupList" border="0">
        <?php 



$query = "SELECT group_name, group_id FROM groups INNER JOIN group_members 
USING(group_id) WHERE user_id=$1"; 
        $params = array($_SESSION['user_id']);
        $result = pg_query_params($conn, $query, $params); 
        if (!$result) { 
            $msg="Failed to get file listing.";
            trigger_error($msg); 
            die($msg); 
        } 

        while($myrow = pg_fetch_assoc($result)) {
            echo '<tr><td>';
            printf('<a href="groupProfilePage.php?group_id=%s">%s</a>', 
                $myrow['group_id'], $myrow['group_name']); 
            echo '</td></tr>';
        }?>
        </table>

        <h3>My Files</h3>
        <table title="FilesList" id="FilesList" border="0">
        <?php 

        $query = "SELECT * FROM files WHERE user_id=$1"; 
        $params = array($_SESSION['user_id']);
        $result = pg_query_params($conn, $query, $params); 
        if (!$result) { 
            $msg="Failed to get file listing.";
            trigger_error($msg); 
            die($msg); 
        } 

        while($myrow = pg_fetch_assoc($result)) {
            echo '<tr><td><input type="checkbox" ';
            printf('value="%s" name="filelist[]"/>' . 
					'<a href="getFile.php?file_id=%s">%s</a>', 
                $myrow['file_id'], $myrow['file_id'], $myrow['file_name']); 
            echo '</td></tr>';
        } 
        ?> 
          <tr><td><input type="submit" name='delete' value="Delete Files" /></td></tr>
        </table>
      </form>
      <form enctype="multipart/form-data" 
          action="<?php echo "$_SERVER[PHP_SELF]";?>" method="POST">
        <table title="Content" id="content" border="0">
          <tr>
            <td><input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
              Choose a file to upload: </td>
            <td><input name="uploadedfile" type="file" /></td>
          </tr>
          <tr>
            <td><input type="submit" value="Upload File" /></td>
          </tr>
          
          <tr>
          	<td><a href="<?php echo "groupRegistration.php"; ?>">Create a Group</a></td>
           </tr>
            <tr>
            <td><a href="<?php echo "login.php?logout"; ?>">Logout</a></td>
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
