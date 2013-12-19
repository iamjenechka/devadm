<?php
function haveright ($userlogin, $projectid, $righttypeid) {
	$result = mysql_query("
		select *
			from users
			where login = \"$userlogin\"
		") or die ("Can't form a query");
	$f = mysql_fetch_array($result);
	if($f['is_admin'])
		return true;
	$result = mysql_query("
		select count(*)
			from user_rights
				right join users on (user_rights.user_id = users.user_id)
			where 
				users.login = \"$userlogin\" and
				user_rights.project_id = \"$projectid\" and
				user_rights.right_type_id = \"$righttypeid\"
		") or die ("Can't form a query");
	$f = mysql_fetch_array($result);
	return ($f[0] > 0);
}

function haveright_projects($userlogin, $righttypeid) {
	$project_ids = array();

	$result = mysql_query("
		select *
			from users
			where login = \"$userlogin\"
		") or die ("Can't form a query");
	$f = mysql_fetch_array($result);

	if($f['is_admin'])
		$query = 'select `project_id` from `projects`';
	else
		$query = "
			select `user_rights`.`project_id` as `project_id`
				from user_rights
					right join users on (user_rights.user_id = users.user_id)
				where 
					users.login = \"$userlogin\" and
					user_rights.right_type_id = \"$righttypeid\"
		";

	$result = mysql_query($query) or die ("Can't form a query");
	while($row = mysql_fetch_assoc($result))
		$project_ids[] = $row['project_id'];

	return $project_ids;
}


?>
