<?php

include "template.php";
include "config.php";

if (!mysql_connect ($HOST, $USER, $PASS)) die ('Error:'.mysql_error ());
mysql_select_db($DB) or die ('Error: '.mysql_error());


if (!isset($_POST['login']) && !isset($_POST['password']))
            
            {
                   
                   $DIRECTORY=explode("login.php", $_SERVER['REQUEST_URI']);
                   $DIRECTORY=reset($DIRECTORY);
                   $tpl->set_value('path', $DIRECTORY);
                   $tpl->get_tpl('./template/login.tpl');                    
                   $tpl->tpl_parse();
                   echo $tpl->html; 
            }
            
    else
    {
       
    }

?>
