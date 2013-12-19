#!/usr/bin/php
<?php

error_reporting(E_ALL);

if(isset($_SERVER['REMOTE_ADDR'])) {
	die('You cannot run this indirectly');
}

require_once "config.php";

$arguments=unserialize(trim(isset($argv[2])?$argv[2]:$argv[1])); 	// TODO: fix this

function getargument($key) {
	global $arguments;
	return getenv($key) !== FALSE ? getenv($key) : $arguments[$key];
}

$action 	= getargument('ACTION');
$svnlogin 	= getargument('SVN_LOGIN');
$svnpass 	= getargument('SVN_PASSWORD');
$svnworkdir	= getargument('SVN_WORKDIR');


$workingcopy	= realpath($WORKDIR_PREFIX.'/'.$svnworkdir.'/'.$WORKDIR_POSTFIX);	// '/'-s just in case
if(DEBUG&DEBUG_LASERDO)
	echo "\n\n".$workingcopy."\n";

if(($_stat = stat($workingcopy)) === FALSE)
	die('[error] Cannot access to file. Is it exists?');

if(DEBUG&DEBUG_LASERDO) {
	print_r($_stat);
}

posix_setgid($_stat['gid']);
posix_setuid($_stat['uid']);
if(DEBUG&DEBUG_LASERDO) {
	echo 'UID: 		'.posix_getuid()."<br>\n";
	echo 'GID: 		'.posix_getgid()."<br>\n";
	echo 'SVN-login: 	'.$svnlogin."<br>\n";
	echo 'SVN-password: 	'.$svnpass."<br>\n";
}

svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_USERNAME, $svnlogin);
svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_PASSWORD, $svnpass);

switch($action) {
	case SVN_UP:
		$svnrevision	= getargument('SVN_REVISION');
		if(DEBUG&DEBUG_LASERDO)
			print "svn_update($workingcopy, $svnrevision)\n";
		if(($rev=svn_update($workingcopy, $svnrevision)) === FALSE)
			die('[error] Cannot make "svn up" :(');

		echo $rev;
		break;
	case SVN_INFO:
		$ret=svn_status($workingcopy, SVN_NON_RECURSIVE|SVN_ALL);
		if($ret === FALSE) 
			die('[error] svn_status() returned "FALSE"');
		echo serialize($ret);
		break;
	default:
		echo '[error] Unknown action';
}

?>
