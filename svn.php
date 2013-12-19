<?php

//error_reporting(E_ALL);
//ini_set('display_errors','On'); 

//defence from hack attempt
defined("__run__") or die('Error');

require_once "DB.php";
require_once "permissions.php"; 
require_once "laserrun.php";

function _svn_args_mysql_query($svnlist) {

	// Join Condition ARray
	$_jc_ar=array();
	foreach($svnlist as $project_id => $sides) {
		// Join Condition
		$_jc = '`projects`.`project_id`="'.mysql_real_escape_string($project_id).'"';
		$_sidescond_ar = array();
		foreach($sides as $side) {
			switch($side) {
				case SVN_SIDE_DEVELOP:
					$_sidescond_ar[] = '`projects`.`dev_svnup_id` = `svnups`.`svnup_id`';
					break;
				case SVN_SIDE_PRODUCTION:
					$_sidescond_ar[] = '`projects`.`svnup_id` = `svnups`.`svnup_id`';
					break;
			}
		}
		if(isset($_sidescond_ar[0]))
		// Dirty hack in case of only two sides:
			if(!isset($_sidescond_ar[1]))
				$_jc .= ' AND (('.implode(') OR (',$_sidescond_ar).'))';
		$_jc_ar[] = $_jc;
	}

	$jc = '(('.implode(') OR (', $_jc_ar).'))';


	$query = "
			SELECT 
				`projects`.`svnup_id`		as `PROJECT_SVNID_PROD`,
				`projects`.`dev_svnup_id`	as `PROJECT_SVNID_DEV`,
				`svn_reps`.`svn` 		as `SVN_REPOSITORY`,
				`svn_reps`.`login` 		as `SVN_LOGIN`,
				`svn_reps`.`password` 		as `SVN_PASSWORD`,
				`svnups`.`workingdir` 		as `SVN_WORKDIR`,
				`svnups`.`svnup_id`		as `SVN_ID`,
				`svnups`.`ssh_id`		as `SSH_ID`,
				`ssh`.`sshhost`			as `SSH_HOST`,
				`ssh`.`sshlogin`		as `SSH_LOGIN`,
				`ssh`.`sshkey`			as `SSH_KEY`,
				`ssh`.`laserdocommand`		as `SSH_COMMAND`,
				`projects`.`project_id` 	as `project_id`
			FROM 
					`projects`
				JOIN
					`svn_reps` USING(`svn_rep_id`)
				JOIN
					`svnups` ON ( `svnups`.`svnup_id`=`projects`.`svnup_id` OR `svnups`.`svnup_id`=`projects`.`dev_svnup_id`) AND ".$jc."
				LEFT JOIN 
					`ssh` ON `ssh`.`ssh_id`=`svnups`.`ssh_id`

		"; // Sorry for this. TODO: optimize this...

	$sqlresult = mysql_query($query);
//	print $query;
	if(!$sqlresult) {
		if(DEBUG&DEBUG_SVN) print("Cannot make query (" .mysql_error().'): '.$query);
		die();
	}

	return $sqlresult;
}

function svn_getinfo($svnlist) {
	$svninfo = array();

	$res 	 = _svn_args_mysql_query($svnlist);
	while($row = mysql_fetch_array($res)) {
		$_svninfo=@unserialize(laserrun($row, SVN_INFO));
		if(!isset($_svninfo[0])) {
			echo 'Cannot fetch SVN information for "'.$row['SVN_WORKDIR'].'"'."<br>\n";
			continue;
		}
		$_svninfo=reset($_svninfo);
		switch($row['SVN_ID']) {
			case $row['PROJECT_SVNID_PROD']:
				$svninfo[$row['project_id']][SVN_SIDE_PRODUCTION]	= $_svninfo;
				break;
			case $row['PROJECT_SVNID_DEV']:
				$svninfo[$row['project_id']][SVN_SIDE_DEVELOP]		= $_svninfo;
				break;
		}
	}

	return $svninfo;
}

function svn_up($svnlist, $rev = SVN_REVISION_HEAD) {
	$revs = array();


	$res  = _svn_args_mysql_query($svnlist);
	while ($row = mysql_fetch_array($res)) {
		$row['SVN_REVISION'] = $rev;
		$rev=laserrun($row, SVN_UP);
		if(!is_numeric($rev)) {
			die ('Can not update. Try to see DEBUG_LASERDO in configuration File ');// TODO: notify that cannot update
		}
		$revs[$row['project_id']] = $rev;
	}
	return $revs;
}
/*
function svn_r_to($project_ids, $rev) {
	$revs = array();
	$svninfo = array();
	
	$res = _svn_args_mysql_query($project_ids);
	while ($row = mysql_fetch_array($res)) {
		$_svninfo=@unserialize(laserrun($row, SVN_ROLLBACK));
		if (!isset($_svninfo[0])) {
			echo 'Cannot  fetch SVN information for  "'.$row['SVN_WORKDIR'].'".'."<br>\n";
			continue;
		}
		$_svninfo=reset($_svninfo);
		$svninfo[$row['project_id']]=$_svninfo;
		$rev=lasserrun($row, SVN_BACKUP);
		if(!is_numeric($rev)) {
			die('Can not backup');
		}
		$revs[$row['project_id']] = $rev;
	}

	return array ($revs, $svninfo); //TODO: return an array	
}
*/
?>
