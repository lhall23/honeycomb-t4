<?php
/* 
 * register.php
 * -Lee Hall Sat 08 Sep 2012 06:05:55 PM EDT
 *
 * Allow the user to create a new account, and verify that account
 *
 */
// Yes, this makes a connection when we don't neccesarily need it. It's better
// than repeating the include inside multiple branches, though
require_once('include/conf.php');

$MAIL_SUBJECT="[Honeycomb] Registration";
$MAIL_TEXT="Please click the following link to finish the registration" .
  " process. $URL_BASE$_SERVER[PHP_SELF]?verify=";

//Is there a user trying to register?
if (array_key_exists('register', $_POST)){

  if (!array_key_exists('user_name', $_POST) || 
      !array_key_exists('password', $_POST) || 
      !array_key_exists('email', $_POST) ){
    die("User, password or email not set. How did you get here?");
  }

    //Make sure that no one's doing anything tricksey with the username,
    //since it gets used as a filename for the moment
    if(!ctype_alnum($_POST['user_name'])){
        $msg="Please only use letters and numbers in the username.";
    header("Location: $_SERVER[PHP_SELF]?msg=$msg");
        die("Illegal characters in username.");
    }

  //Get user info from database
  $token=md5(mt_rand() . $_POST['user_name']);
  $sql="INSERT INTO users(user_name,password,email,auth_hash) VALUES 
    ($1, $2, $3, $4);";
  $params=array($_POST['user_name'], md5($_POST['password']), 
    $_POST['email'], $token);
  $results=pg_query_params($conn, $sql, $params);
  if (!$results || pg_affected_rows($results) != 1) {
    //There has to be a more elegant way to do this.
    $error=pg_last_error();
    if (strpos($error, 'user_name_key') !== False){
      $msg="That username is already in use.";  
    } else if (strpos($error, 'email_key') !== False) {
      $msg="That email address is already in use.";
    } else {
      die("Unknown error.");
    }
    header("Location: $_SERVER[PHP_SELF]?msg=$msg");
  } else {
    mail($_POST['email'], $MAIL_SUBJECT, $MAIL_TEXT . $token);
    die("Account created succesfully." . 
      "Please follow the directions in your email.");
  }
}

//Is someone trying to get their account verified?
if (array_key_exists('verify', $_GET)){
  $sql="SELECT user_id,user_name FROM users WHERE auth_hash=$1;";
  $params=array($_GET['verify']);
  $results=pg_query_params($conn, $sql, $params);
  if (pg_num_rows($results) != 1){
    $msg="Account not found.";
    header("Location: $_SERVER[PHP_SELF]?msg=$msg");  
    die($msg);
  }
  $row=pg_fetch_array($results);

  // We can still fail after this, but we've finished authentication, so it's
  // safe to authorize the user here.
  $_SESSION['user_id']=$row['user_id'];
  $_SESSION['user_name']=$row['user_name'];

  if (!mkdir("$FILE_STORE/$_SESSION[user_name]")) {
    die("Unable to create user file system.");
  } 
  $sql="UPDATE users SET auth_hash=NULL WHERE user_id=$1;";
  $params=array($row['user_id']);
  $results=pg_query_params($conn, $sql, $params);
  if (!$results || pg_affected_rows($results) != 1){
    die("Database error verifying user.");
  }
  header("Location: profilePage.php");
  die("User validated.");
}
?>

<HTML> 
<HEAD>
<TITLE>Register</TITLE>
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
            <td>Username: </td>
            <td><input type="text" name="user_name"></td>
          </tr>
          <tr>
            <td>Password: </td>
            <td><input type="password" name="password"></td>
          </tr>
          <tr>
            <td>Email: </td>
            <td><input type="text" name="email"></td>
          </tr>
          <tr>
            <td><input type="hidden" name="register"></td>
            <td><input type="submit" value="Register"></td>
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

