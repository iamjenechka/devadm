<?php

define('DEBUG_ALL',		0xff);
define('DEBUG_INDEX',		0x01);
define('DEBUG_SVN',		0x02);
define('DEBUG_LASERRUN',	0x04);
define('DEBUG_LASERDO',		0x08);

define('DEBUG',			NULL);
define('CHARSET',		'en_US.UTF8');

$WORKDIR_PREFIX		= '/var/www/';
$WORKDIR_POSTFIX	= '/root/';

$SCRIPT_SVNUP		= './laserdo.php';


$HOST 			= 'localhost';
$USER 			= 'devadm';
$PASS 			= 'ksjd*SD(*ncx78)%^@';
$DB 			= 'devadm';

define('SVN_UP',		0x01);
define('SVN_INFO',		0x02);
define('TIMEZONE',		'Europe/Moscow');
define('SVN_SIDE_PRODUCTION',	0x01);
define('SVN_SIDE_DEVELOP',	0x02);

if(DEBUG) {
	error_reporting(E_ALL);
	ini_set('display_errors', 'On');
}
setlocale(LC_ALL,	CHARSET);
?>
