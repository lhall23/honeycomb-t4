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
?>

<?php
    print "Welcome $_SESSION[user_name]";
?>


<link  href="include/yui/2.8.2r1/build/fonts/fonts-min.css" rel="stylesheet" type="text/css">
<link  href="include/yui/2.8.2r1/build/treeview/assets/skins/sam/treeview.css" rel="stylesheet" type="text/css">
<script src="include/yui/2.8.2r1/build/yahoo-dom-event/yahoo-dom-event.js" type="text/javascript"></script>
<script src="include/yui/2.8.2r1/build/treeview/treeview-min.js" type="text/javascript"></script>
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
            <a href="images/banner.jpg" 
              title="TheHoneycombBanner1 by devgurl36!, on Flickr">
              <img src="http://farm9.staticflickr.com/8170/7980116517_004c52431c_b.jpg" 
                width="1157" height="137" alt="TheHoneycombBanner1">
            </a>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr height="200">
    <td width="176" bgcolor="white">
      <table id="navigation" title="Navigation" border="0">
        <tr><td>Links!</td></tr>
        <tr><td>Links!</td></tr>
        <tr><td>Links!</td></tr>
      </table>
    </td>
    <td width="989" bgcolor="white">
      <form enctype="multipart/form-data" 
                  action="<?php echo "$_SERVER[PHP_SELF]";?>" method="POST">
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
              <a href="<?php echo "$FILE_URL/$_SESSION[user_name]"; ?>">
                User directory</a>
            </td>
          </tr> 
          <tr> 
            <td>
              <a href="<?php echo "$URL_BASE/login.php?logout"; ?>">Logout</a> 
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
