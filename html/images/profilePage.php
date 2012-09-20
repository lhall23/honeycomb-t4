<HTML>
<HEAD>
    <TITLE>User Profile</TITLE>
</HEAD>
<BODY>
<?php
    require_once('file:///Macintosh HD/Users/robinmays/honeycomb-t4/html/include/session.php');
    require_once('file:///Macintosh HD/Users/robinmays/honeycomb-t4/html/include/conf.php');
    // Where the file is going to be placed 
    $target_path = "$FILE_STORE/$_SESSION[user_name]/";
?>

<?php
if (array_key_exists('uploadedfile', $_FILES)){
    /* Add the original filename to our target path.  
        Result is "uploads/filename.extension" */
    $target_path = $target_path . basename( $_FILES['uploadedfile']['name']); 

    if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
        echo "The file ".  basename( $_FILES['uploadedfile']['name']). 
        " has been uploaded";
    } else{
        trigger_error("Failed to save file as $target_path");
        echo "There was an error uploading the file, please try again!";
    }
}

if (array_key_exists('delete', $_POST)){
    foreach ($_POST['filelist'] as $myfile){
        if (strstr('/', $myfile)){
            die("Please don't be rude. That's not a filename I gave you.");
        }
        if (! unlink("$FILE_STORE/$_SESSION[user_name]/$myfile")) {
            die("Unable to delete $myfile.");
        }
    }
}
?>

<?php
    print "Welcome $_SESSION[user_name]";
?>

<link  href="file:///Macintosh HD/Users/robinmays/honeycomb-t4/html/include/yui/2.8.2r1/build/fonts/fonts-min.css" rel="stylesheet" type="text/css">
<link  href="file:///Macintosh HD/Users/robinmays/honeycomb-t4/html/include/yui/2.8.2r1/build/treeview/assets/skins/sam/treeview.css" rel="stylesheet" type="text/css">
<script src="file:///Macintosh HD/Users/robinmays/honeycomb-t4/html/include/yui/2.8.2r1/build/yahoo-dom-event/yahoo-dom-event.js" type="text/javascript"></script>
<script src="file:///Macintosh HD/Users/robinmays/honeycomb-t4/html/include/yui/2.8.2r1/build/treeview/treeview-min.js" type="text/javascript"></script>
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
        <tr>
          <td width="1195">
          <img src="../../../../Documents/Unnamed Site 2/Honeycomb Logo 2.jpg" width="1008" height="168"></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr height="200">
    <td width="176" bgcolor="white">
      <table id="navigation" title="Navigation" border="0">
 <img src="file:///Macintosh HD/Users/robinmays/honeycomb-t4/html/images/Side Bar Pics.jpg" width="432" height="432">
      </table>
    <img src="../../../../Documents/Unnamed Site 2/Side Bar Pics.jpg" width="216" height="864">    </td>
    <td width="989" bgcolor="white">
      <form enctype="multipart/form-data" 
                  action="<?php echo "file:///Macintosh HD/Users/robinmays/honeycomb-t4/html/$_SERVER[PHP_SELF]";?>" method="POST">
        <table title="FileList" id="FileList" border="0">
        <?php 
        $file_dir = opendir("$FILE_STORE/$_SESSION[user_name]");
        if (!$file_dir) die("Can't see directory.");
        $id = 0;
        while($myfile = readdir($file_dir)){
            printf ('<tr><td><input type="checkbox" value="%s" name="filelist[]"/>%s</td></tr>', $myfile, $myfile);
        } 
        ?> 
          <tr><td><input type="submit" name='delete' value="Delete Files" /></td></tr>
        </table>
      </form>
      <form enctype="multipart/form-data" 
                  action="<?php echo "file:///Macintosh HD/Users/robinmays/honeycomb-t4/html/$_SERVER[PHP_SELF]";?>" method="POST">
        <table title="Content" id="content" border="0">
          <tr>
            <td>
              <input type="hidden" name="MAX_FILE_SIZE" value="100000" />
                Choose a file to upload: 
            </td>
            <td>
              <input name="uploadedfile" type="file" />
            </td>
          </tr>
          <tr>
            <td><input type="submit" value="Upload File" /></td>
          </tr>
          <tr>
            <td>
              <a href="<?php echo "file:///Macintosh HD/Users/robinmays/honeycomb-t4/html/$FILE_URL/$_SESSION[user_name]"; ?>">
                User directory</a>
            </td>
          </tr> 
          <tr> 
            <td>
              <a href="<?php echo "file:///Macintosh HD/Users/robinmays/honeycomb-t4/html/$URL_BASE/login.php?logout"; ?>">Logout</a> 
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