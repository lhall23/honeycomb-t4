<?php
/* 
 * index.php
 * -Lee Hall Thu 20 Sep 2012 01:03:13 PM EDT
 */
//Check if the user is logged in.
require_once("include/session.php");

//If they are, send them to the main page
header("Location: profilePage.php");

?>
