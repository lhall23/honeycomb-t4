
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




if (array_key_exists('delete', $_POST)){
    $fetch_sql="SELECT location FROM files WHERE file_id=$1";
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
            $msg="Can't find file $myfile.";
            trigger_error($msg);
            die($msg);
        }
        $row=pg_fetch_assoc($query_res);
        $file_loc=$row['location'];
        if (! unlink("$FILE_STORE/$file_loc")) {
            $msg="Unable to delete from $file_loc.";
            trigger_error($msg);
            die($msg);
        }
        pg_free_result($query_res);
        
        $query_res=pg_execute($conn, "del_file", $params);
        if (!$query_res || pg_affected_rows($query_res)) {
            $msg="Unable to remove from $myfile from database.";
            trigger_error($msg);
            die($msg);
        }
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
                  action="<?php echo "$_SERVER[PHP_SELF]";?>" method="POST">
        <table title="FileList" id="FileList" border="0">
        <?php 

        $query = "SELECT * FROM files;"; 
        $params = array($_SESSION['user_id']);
        $result = pg_query_params($conn, $query, $params); 
        if (!$result) { 
            $msg="Failed to get file listing.";
            trigger_error($msg); 
            die($msg); 
        } 

        while($myrow = pg_fetch_assoc($result)) {
            echo '<tr><td><input type="checkbox" ';
            printf('value="%s" name="filelist[]"/><a href="%s/%s">%s</a>', 
                $myrow['file_id'], $FILE_URL, $myrow['location'], 
                $myrow['file_name']); 
            echo '</td></tr>';
        } 
        ?> 
          <tr><td><input type="submit" name='delete' value="Delete Files" /></td></tr>
        </table>
        <table title="Content" id="content" border="0">
          <tr>
            <td><input type="hidden" name="MAX_FILE_SIZE" value="100000" />
          
          <tr>
            <td><a href="<?php echo "$URL_BASE/login.php?logout"; ?>">Logout</a></td>
          </tr>
        </table>
      </form></td><td width="4" bgcolor="white">
      <form enctype="multipart/form-data" 
                  action="<?php echo "$_SERVER[PHP_SELF]";?>" method="POST">
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
