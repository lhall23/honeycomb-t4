<?php
/* 
 * index.php
 * -Lee Hall Thu 06 Sep 2012 11:07:04 PM EDT
 */
require_once('include/session.php');
require_once('include/conf.php');
?>
<HTML> 
<HEAD>
<TITLE> Honeycomb application </TITLE>
</HEAD>
<BODY>
Welcome.<br>
<a href="<?php echo "http://$URL_BASE/login.php?logout"; ?>">Logout</a>
</BODY>
</HTML>

