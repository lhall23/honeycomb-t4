<?php
    //Display a user profile, list the files, and do most of the heavy lifting
?>
<HTML>
<HEAD>
    <TITLE>Group Profile</TITLE>
</HEAD>
<BODY>
<?php
    require_once('include/session.php');
    require_once('include/conf.php');
    // Where the file is going to be placed 

if (array_key_exists('add', $_POST) &&
        array_key_exists('filelist', $_POST)){
    $insert_sql="INSERT INTO GROUP_FILES(group_id,file_id) VALUES($1,$2)";
    pg_prepare("ins_file", $insert_sql);

   
    foreach ($_POST['filelist'] as $myfile){
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
        array_key_exists('filelist', $_POST)){
    $delete_sql="DELETE FROM group_files WHERE group_id = $1 and file_id = $2";
    pg_prepare("del_file", $delete_sql);

   
    foreach ($_POST['filelist'] as $myfile){
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
 }
?>

<?php
    if (array_key_exists("msg", $_GET)){
        // Does this actually sanitixe enough, or can we still end up with XSS
        // attacks here?
        echo htmlentities($_GET['msg']);
        echo "<br>";
    }
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


 $query = "SELECT DISTINCT file_name, file_id FROM files 
            LEFT JOIN group_files USING (file_id) 
            WHERE user_id=$1 AND (group_id != $2 OR group_id IS NULL)"; 
        $params = array($_SESSION['user_id'], $_GET['group_id']);
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
          <tr><td><input type="submit" name='add' value="Add Files to Group" /></td></tr>
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
			 
	  
        $query = "SELECT * FROM group_files JOIN files USING(file_id) WHERE group_id = $1;";
		$params = array($_GET['group_id']);
        $result = pg_query_params($conn, $query,$params); 
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
         <tr>
            <td><a href= "profilePage.php">User Profile</a></td>
          </tr>
         <tr>
            <td><a href= "groupAdmin.php?group_id=<?php echo $_GET['group_id']?>">Group Admin</a></td>
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
