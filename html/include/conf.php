<?php
/* 
 * config.php
 * -Lee Hall Thu 06 Sep 2012 10:10:03 PM EDT
 */

$conn_str="user=apache dbname=honeycomb";
$conn= pg_connect($conn_str);
if (!$conn) die("Unable to connect to database.");

$URL_BASE="http://$_SERVER[SERVER_NAME]";
$FILE_STORE="/var/www/honeycomb/file_store";
$FILE_URL=$URL_BASE . "/file_store";

?>
