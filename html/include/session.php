<?php
/* 
 * session.php
 * -Lee Hall Thu 06 Sep 2012 10:13:49 PM EDT
 *
 * Check that a session exists. 
 * If not, bounce them to the login page and die.
 */

session_start();
if (! array_key_exists('user_id', $_SESSION) ||
        !isset($_SESSION['user_id'])){
    header( "location: login.php");
    die("User not logged in");
}

?>
