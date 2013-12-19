<?php

error_reporting(E_ALL);
ini_set('display_errors','On'); 

//defence from hack attempt
defined("__run__") or die('Error');


$projsvnup = $_POST['projsvnup'];    

include "DB.php";

// $svnrequest= mysql_query ("SELECT * FROM `svn_reps`, `svnups` where `svn_reps`.`svn_rep_id` and `svnups`.`svnup_id` = \"$param\"") 

$svnrequest= mysql_query ("SELECT 
		`svn_reps`.`svn` 	as `SVN_REPOSITORY`,
		`svn_reps`.`login` 	as `SVN_LOGIN`,
		`svn_reps`.`password` 	as `SVN_PASSWORD`,
		`svnups`.`workingdir` 	as `SVN_WORKDIR`
				FROM `projects`
				LEFT JOIN `svn_reps` ON `svn_reps`.`svn_rep_id` = `projects`.`svn_rep_id`
				LEFT JOIN `svnups` ON `svnups`.`svnup_id` = `projects`.`svnup_id`
				WHERE `projects`.`project_id` = \"$projsvnup\"")              

	or die ("Can't form your svn update query, sorry");

while ($row = mysql_fetch_array($svnrequest)) {
	$command='sudo '.$SCRIPT_SVNUP." '".serialize($row)."'";
	if($DEBUG)
		$rev=system($command);
	else
		$rev=exec($command);
	if(!is_numeric($rev)) {
		// TODO: notify that cannot update
	}
	echo 'Revision is: '.$rev;
}
function showlastver () {
	print_r ( svn_log ('http://devadm.mephi.ru/',1));
}
die();
?>
