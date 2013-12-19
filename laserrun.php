<?

require_once "config.php";

/*
function _ssh_mysql_getargs ($ssh_id) {
	$query	= "
		SELECT
			`ssh`.`ssh_id`		as `SSH_ID`,
			`ssh`.`sshhost`		as `SSH_HOST`,
			`ssh`.`sshlogin`	as `SSH_LOGIN`,
			`ssh`.`sshkey`		as `SSH_KEY`,
			`ssh`.`laserdocommand`	as `SSH_COMMAND`
		FROM
			`ssh`
		WHERE 
			`ssh`.`ssh_id` = '".mysql_real_escape_string($ssh_id)."'
		";
	
	print $query."<br>\n";

	$sqlresult = mysql_query($query);
	if(!$sqlresult)
		die("Cannot make query: (" .mysql_error().')');

	return $sqlresult;

}
*/
function _tmp_key_file_gen($key) {
	$filepath = "/tmp/.devadm_sshkey-".posix_getpid();
	//$handle = fopen("/tmp/id_ecdsa_".getpid(),"w") or die ("Can't create a temp file");j
	$f=fopen($filepath, 'w')		or die('Cannot open file "'.$filepath.'" for writing');
	chmod($filepath, 0400) 			or die('Cannot chmod() for file "'.$filepath.'"');
	fwrite($f, $key)			or die('Cannot write into file "'.$filepath.'"');
	fclose($f);
/*
	touch($filename) 			or die('Cannot touch file "'.$filename.'"');
	chmod($filename, 0400) 			or die('Cannot chmod() for file "'.$filename.'"');
	print "((".$key.'))';
	file_put_contents($filename, $key) 	or die("Couln't to generate the file in temp directory (".$filename.")");*/
	return $filepath;
}

function laserrun($args, $action) {
	global $SCRIPT_SVNUP;
	// just in case {
	foreach($args as $key => $value)
		putenv("$key=$value");
	// }

	$keypath=NULL;
	$args['ACTION'] = $action;
	if(!is_null($args['SSH_ID'])) {
		/*
		$res =  _ssh_mysql_getargs($args['SSH_ID']);
		$row = mysql_fetch_array($res)
		if(!$row) die('Cannot get SSH-key for SSH-id '.$args['SSH_ID']);*/
		$keypath=_tmp_key_file_gen($args['SSH_KEY']);
		$command="ssh -i '".$keypath."' '".$args['SSH_LOGIN']."'@'".$args['SSH_HOST']."' '".$args['SSH_COMMAND']."' '".serialize($args)."'";
	} else {
		$command='sudo '.$SCRIPT_SVNUP." '".serialize($args)."'";
	}
	if(DEBUG&DEBUG_LASERRUN) print $command."<br>\n";
	if(DEBUG&DEBUG_LASERDO)
		$out=system($command);
	else
		$out=exec($command);
	if(!is_null($keypath)) {
		unlink($keypath);
	}
	return $out;
}

?>
