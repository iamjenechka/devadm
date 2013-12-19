<?php

//error_reporting(E_ALL);
//ini_set('display_errors','On'); 

// defence from hack attempt
defined("__run__") or die('Error');

require_once "DB.php";
require_once "permissions.php";

function _dbcopy_args_mysql_query($project_ids){
	$sqlwhereclosure = '`project_id` = "'.implode('"  OR  `project_id` = "', $project_ids).'"';
	$query = "
			SELECT 
				`dbase`.`db_id`			AS `DB_ID`,
				`dbase`.`db_host`		AS `DB_HOST`,
				`dbase`.`db_name`		AS `DB_NAME`,
				`dbase`.`db_login`		AS `DB_LOGIN`,
				`dbase`.`db_password`		AS `DB_PASSWORD`,
				`ssh_w`.`ssh_id`		AS `SSH_ID`,
				`ssh_w`.`sshhost`		AS `SSH_HOST`,
				`ssh_w`.`sshlogin`		AS `SSH_LOGIN`,
				`ssh_w`.`sshkey`		AS `SSH_KEY`,
				`ssh_w`.`laserdopath`		AS `SSH_LASERDO`,
				`ssh_d`.`ssh_id`		AS `DEVSSH_ID`,
				`ssh_d`.`sshhost`		AS `DEVSSH_HOST`,
				`ssh_d`.`sshlogin`		AS `DEVSSH_LOGIN`,
				`ssh_d`.`sshkey`		AS `DEVSSH_KEY`,
				`ssh_d`.`laserdopath`		AS `DEVSSH_LASERDO`,
				`dev_dbase`.`db_id`		AS `DEVDB_ID`,
				`dev_dbase`.`db_host`		AS `DEVDB_HOST`,
				`dev_dbase`.`db_name`		AS `DEVDB_NAME`,
				`dev_dbase`.`db_login`		AS `DEVDB_LOGIN`,
				`dev_dbase`.`db_password`	AS `DEVDB_PASSWORD`
			FROM
				`projects` LEFT JOIN
				`dbases` AS `dbase` ON  `dbase`.`db_id` = `projects`.`db_id` LEFT JOIN
				`dbases` AS `dev_dbase` ON  `dev_dbase`.`db_id` = `projects`.`dev_db_id` LEFT JOIN
				`ssh` `ssh_w` ON `ssh_w`.`ssh_id` = `dbase`.`ssh_id` LEFT JOIN
				`ssh` `ssh_d` ON `ssh_d`.`ssh_id` = `dev_dbase`.`ssh_id`
			WHERE ".$sqlwhereclosure;
	
	$sqlresult = mysql_query($query) or die ('cant form a query <'.$query.'>: '.mysql_error());

	return $sqlresult;
}


function dbcopy($dbproj_ids){
	// croocked nailed style
	$dbs = _dbcopy_args_mysql_query($dbproj_ids);
	while ($row = mysql_fetch_array($dbs)) {
		// source
		$command='';
		if(!is_null($row['SSH_ID'])) 
			$command .= 'ssh -l "'.addslashes($row['SSH_LOGIN']).'" -i "'.addslashes($row['SSH_KEY']).'" "'.addslashes($row['SSH_HOST']).'" ';
		$command .= 'mysqldump -h "'.addslashes($row['DB_HOST']).'" -u"'.addslashes($row['DB_LOGIN']).'" -p"'.addslashes($row['DB_PASSWORD']).'" "'.addslashes($row['DB_NAME']).'"';

		// destination
		$command .= ' | ';
		if(!is_null($row['SSH_ID'])) 
			$command .= 'ssh -l "'.addslashes($row['DEVSSH_LOGIN']).'" -i "'.addslashes($row['DEVSSH_KEY']).'" "'.addslashes($row['DEVSSH_HOST']).'" ';
		$command .= 'mysql -h "'.addslashes($row['DEVDB_HOST']).'" -u"'.addslashes($row['DEVDB_LOGIN']).'" -p"'.addslashes($row['DEVDB_PASSWORD']).'" "'.addslashes($row['DEVDB_NAME']).'"';


		exec($command);
		// TODO: exceptions
		die($command);
	}
}




		


?>
