<?php

require_once "config.php";

if (!mysql_connect ($HOST, $USER, $PASS)) die ('Error:'.mysql_error ());
  
 mysql_select_db($DB) or die ('Error: '.mysql_error());
 mysql_set_charset('utf8');
?>
