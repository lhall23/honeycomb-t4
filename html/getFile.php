<?php
/* 
 * getFile.php
 * -Lee Hall Sun 07 Oct 2012 03:17:39 PM EDT
 * Retrieves a file from the file store
 */

require_once("include/conf.php");
require_once("include/session.php");

if (!array_key_exists($_GET, 'file_id'){
    die("No file specified");
}

$sql="SELECT DISTINCT file_id,location
        FROM files 
        JOIN group_files USING (file_id) 
        WHERE file_id=$1 AND 
            (files.user_id=$2 OR
            group_id IN 
                (SELECT group_id 
                    FROM group_members 
                    WHERE user_id=$2));";
$params=array($_GET['file_id'], $_SESSION['user_id']);
$file_res=pg_query_params($conn, $sql, $params);
if (!$file_res || (pg_num_rows($file_res) > 1)){
    die("Unrecoverable database error.");
}

if (pg_num_rows($file_res)== 0) {
    die("This file does not exist," .
        " or you do not have permission to access it.");
}
$row=pg_fetch_array($file_res);
$file=$row['location'];
$file_name=$row['file_name'];
       
//File sending adapted from PHP example at 
// http://php.net/manual/en/function.readfile.php 
if (file_exists($file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header("Content-Disposition: attachment; filename=$file_name");
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: ' . filesize($file));
    ob_clean();
    flush();
    readfile($file);
    exit;
}

?>
