<?php
/* 
 * config.php
 * -Lee Hall Thu 06 Sep 2012 10:10:03 PM EDT
 *
 * This file opens the database connection and provides some useful global
 * variables to the project
 */

$conn_str="user=apache dbname=honeycomb";
$conn= pg_connect($conn_str);
if (!$conn) 
    die("Unable to connect to database.");

$URL_BASE="https://$_SERVER[SERVER_NAME]";
$FILE_STORE="/var/www/honeycomb/file_store";
$FILE_URL=$URL_BASE . "/file_store";

?>
