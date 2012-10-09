<?php
/* 
 * include/secure.php
 * -Lee Hall Tue 09 Oct 2012 01:23:17 AM EDT
 * This should be included in the header of any page that sends a password
 */
if (!array_key_exists('HTTPS', $_SERVER) || $_SERVER['HTTPS'] != "on") { 
    $url = "https://". $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']; 
    header("Location: $url"); 
    die("Forwarding to a secure page"); 
}  

?>
