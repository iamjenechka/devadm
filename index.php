<?php
ob_start();
//error_reporting(E_ALL);
//ini_set('display_errors','On'); 

//require 'config.php';
 
require_once 'template.php';
require_once "config.php"; 
require_once "permissions.php";
 
//флаг авторизации
define("__run__", true);
require_once "svn.php"; 
require_once "dbcopy.php";
require_once "projects.php";

//if (!date_default_timezone_set(DEFAULT_TIMEZONE)){
//	throw new exc('Invalid timezone: "'.DEFAULT_TIMEZONE.'"');
//}
//$now = getdate();
//$time = $now['year'].$now['mon'].$now['mday'];

date_default_timezone_set(TIMEZONE);


session_name('devadm');
session_start();

        
$DIRECTORY=explode("index.php", $_SERVER['REQUEST_URI']);
$DIRECTORY=reset($DIRECTORY);

if (!mysql_connect ($HOST, $USER, $PASS)) die ('Error:'.mysql_error ());
mysql_select_db($DB) or die ('Error: '.mysql_error());


        
      //  error_reporting(E_ALL);
       // ini_set('display_errors','On');     
if(!isset($_SESSION["permission"])) {
        if(isset($_POST['login']) && isset($_POST['password'])) {
                    $login = mysql_real_escape_string($_POST['login']); 
                    $result = mysql_query("select * from users where login = \"$login\" LIMIT 1" ) or die ("Can't form a query");
                    $f = mysql_fetch_array($result); //or die ("Cant fetch, bro");
                    if ($f['md5'] == md5($_POST['password']))
                            $_SESSION['permission'] = $f['is_admin'] ? 0xffffffff : 0x0000ffff ;
                   	
		 	else {
/*                         	$message = '<div class="f-message f-message error">Не правильное имя или пароль, пожалуйста убедитесь в том, 
						что выбрана правильная расскладка клавиатуры или не включена клавиша caps lock </div>';
						
						$tpl->set_value('path', $DIRECTORY);
						$tpl->set_value('messages',$message);
                				$tpl->get_tpl('./template/login.tpl');                    
                				$tpl->tpl_parse();
                				echo $tpl->html;
						die();  */
			die('Wrong login or password');
			}

                    $_SESSION['username'] = $f['fullname'];
                    $_SESSION['login']    = $f['login'];
        } else {
                $message = '';
		$tpl->set_value('path', $DIRECTORY);
		$tpl->set_value('messages',$message);
                $tpl->get_tpl('./template/login.tpl');                    
                $tpl->tpl_parse();
                echo $tpl->html;
                exit;
        }
}


$tablecontent = '';
$message = '';
$project_ids  = haveright_projects($_SESSION['login'], 1);
$svnlist = array();
foreach($project_ids as $project_id) {
	$svnlist[$project_id][] = SVN_SIDE_DEVELOP;
	$svnlist[$project_id][] = SVN_SIDE_PRODUCTION;
}
$svninfo      = svn_getinfo($svnlist);
$tpl->get_tpl('./template/admin.tpl');

if(array_key_exists('do', $_GET)) {
        switch($_GET['do']) {
                case 'logout':
                        session_destroy();
                        break;
                case 'svnup':
		case 'svnrollback':
			$proj = $_POST['projid'];	// TODO: check if parameters are set
			$side = $_GET['svnside'];
			if(in_array($proj, $project_ids))
				svn_up(array($_POST['projid']=>array($side)), $_GET['do']=='svnup'?SVN_REVISION_HEAD:$svninfo[$proj][$side]['revision']-1);
			break;
		case 'dbcopy':
			$proj = $_POST['projid'];
			dbcopy(array($proj));
			break;

		case 'proj_edit':
	                die('This function is under construction');
			$ctpl = new template_class();
			$ctpl->get_tpl('./template/proj_edit.tpl');      
            		$tpl->set_value('ADDITIONAL', $ctpl->html);
		//	$ctpl->tpl_parse();
			unset($ctpl);
			$tpl->tpl_parse();
			echo $tpl->html;
			ob_end_flush();
			exit(0);
			break;
                default:
        }
        header('Location: '.$DIRECTORY);
	exit(0);
}
if(DEBUG&DEBUG_INDEX)
	print_r($svninfo);


$sql = mysql_query("SELECT
			`projects`.`project_id`, `projects`.`name`, `projects`.`svnup_id` ,`projects`.`dev_svnup_id`, `su1`.`url` AS `url`, `su2`.`url` AS `devurl`,
			`dbase1`.`db_host` AS `db_host1`, `dbase1`.`db_name`  AS `dbasebase1`, `dbase1`.`db_password`  AS `dbasepass1`, 
			`dbase1`.`db_login`  AS `dbaselogin1`, `dbase1`.`comment`  AS `dbasecomm1`,
			`dbase2`.`db_host` AS `dbasehost2`, `dbase2`.`db_name`  AS `dbasebase2`, `dbase2`.`db_password`  AS `dbasepass2`, `dbase2`.`db_login`  AS `dbaselogin2`, `dbase2`.`comment`  AS `dbasecomm2`
                                                    FROM `projects` 
                                                    LEFT JOIN `site_urls` AS `su1` ON `projects`.`site_url_id` = `su1`.`site_url_id`
                                                    LEFT JOIN `site_urls` AS `su2` ON `projects`.`dev_site_url_id` = `su2`.`site_url_id`
                                                    LEFT JOIN `dbases` AS `dbase1` ON `projects`.`db_id` = `dbase1`.`db_id`
                                                    LEFT JOIN `dbases` AS `dbase2` ON `projects`.`dev_db_id` = `dbase2`.`db_id`
                                                        
")

or die ("Can't find any projects, 'couse: ". mysql_error());
// $count = mysql_num_fields($sql);
while ($row = mysql_fetch_array($sql, MYSQL_ASSOC))
{
	if(DEBUG&DEBUG_INDEX)
		print_r($row);
        $projsvnup 	= $row['svnup_id']; //TODO : make svn_id perhaps, or svnupdev svnup
       	$projdevsvnup 	= $row['dev_svnup_id'];
	$projid 	= $row['project_id'];
        $projname 	= $row['name'];
        $projurl 	= $row['url'];
        $projdevurl 	= $row['devurl'];
        $projbase 	= $row['dbasebase1'];
        $projdevbase   	= $row['dbasebase2'];
	$tablecontent  .= "<tr class='none'><td>$projname<div>$projurl</div></td>";
	//if(DEBUG&DEBUG_INDEX)
	//print_r($row);
       // echo $projsvnup;

        //updates
        // $svninfo ='' .system("cd '/var/www/devadm.mephi.ru/root'; svn info | awk '{ if(\$3==\"Date:\") { print \$4\" \"\$5; } }'; cd - >/dev/null");
        //$tablecontent = $tablecontent. "<td>Последний update: ".$svninfo."</td>";



	//if(DEBUG&DEBUG_INDEX)
	//	print_r($svninfo[$projid]);
	

//TODO: examination for projects

//	if (!isset($svninfo[$projid][SVN_SIDE_DEVELOP]))  {
//		$message = '<div class="f-message f-message error">Не могу прочитать данные SVN для некоторых репозиториев,
//				проверьте указаны ли все параметры верно</div>';
//		continue;
//	}
	
	$_svninfo = $svninfo[$projid];

        $buttondevsvn  	= '<form method="post" action="index.php?do=svnup&svnside='.SVN_SIDE_DEVELOP.'"><div><button type="submit" name="projid" value="'.$projid.'" class=\'f-bu f-bu-default\'>SVN-up</button></div></form><div id="svninfo"><div class="revno">Ревизия: '.$_svninfo[SVN_SIDE_DEVELOP]['revision'].'</div><div class="svndate">'.date('Y-m-d H-i-s', $_svninfo[SVN_SIDE_DEVELOP]['cmt_date']).'</div></div><div class="svnrollback"><form method="post" action="index.php?do=svnrollback&svnside='.SVN_SIDE_DEVELOP.'"><button type="submit" name="projid" value="'.$projid.'" class="f-bu f-bu-default">Roll it back</button></form></div></td>';
	$buttondbcopy  	= "<form method=\"post\" action=\"index.php?do=dbcopy\"><button type=\"submit\" name=\"projid\" value='".$projid."' class='f-bu f-bu-default'>DB-Copy</button></form>";

	// "SVN-up"/"Roll it back" for production
        if (haveright($_SESSION['login'], $projid, 1))
		$tablecontent .= empty($projsvnup) ?"<td>Отсутствует</td>" :'<td><form method="post" action="index.php?do=svnup&svnside='.SVN_SIDE_PRODUCTION.'"><div><button type="submit" name="projid" value="'.$projid.'" class=\'f-bu f-bu-default\'>SVN-up</button></div></form><div id="svninfo"><div class="revno">Ревизия: '.$_svninfo[SVN_SIDE_PRODUCTION]['revision'].'</div><div class="svndate">'.date('Y-m-d H-i-s', $_svninfo[SVN_SIDE_PRODUCTION]['cmt_date']).'</div></div><div class="svnrollback"><form method="post" action="index.php?do=svnrollback&svnside='.SVN_SIDE_PRODUCTION.'"><button type="submit" name="projid" value="'.$projid.'" class="f-bu f-bu-default">Roll it back</button></form></div></td>';

	// "SVN-up"/"Roll it back" for develop
        if (haveright($_SESSION['login'], $projid, 2))
                $tablecontent .= empty($projdevsvnup) ? "<td>Отсутствует</td>" : "<td><div>$buttondevsvn</div></td>";

	// "DB-copy": prod -> dev
        if (haveright($_SESSION['login'], $projid, 3))
                $tablecontent .= (empty($projbase) || empty($projdevbase)) ? "<td>-</td>" : "<td><div>$buttondbcopy</div></td>";

        $tablecontent .= "</tr>";
	

        /*
        if  (empty($projdevurl))
        {
        $tablecontent = $tablecontent."<tr><td>$projname</td>
        <td>$projurl<div><button class='f-bu f-bu-default'>SVN-up</button></td>
        <td>Отсутствует</td><td></td></tr>";
        }
        else
        {
        $tablecontent = $tablecontent."<tr><td>$projname</td>
        <td>$projurl<div><button class='f-bu f-bu-default'>SVN-up</button></td>
        <td>$buttondevsvn</td><td><button class='f-bu f-bu-default'>DB-Copy</button></td></tr>";
        }

        */

} //while
//              mysql_free_result($sql); // Free

	if (haveright($_SESSION['login'], $projid, 1)) {
		$project_add = '';
		$project_edit = '';
		$users_edit = '';
		$project_add .= 	"<div class='mitem'><form><div><a href='#'>Добавить проект</a><div></form></div>";
		$project_edit .= 	"<div class='mitem'><form><a href='index.php?do=proj_edit'>редактировать проекты</a></form></div>";
	}
		

//SET VALUES FOR TEMPLATE VARS:

$tpl->set_value ('PROJECT_ADD', $project_add);
$tpl->set_value ('PROJECT_EDIT',$project_edit);
$tpl->set_value	('USERS_EDIT', $users_edit);
$tpl->set_value	('USERNAME', $_SESSION['username']);
$tpl->set_value	('CONTENT', $tablecontent);
$tpl->set_value	('POST_MESSAGES',$p_messages); //TODO : work around exeption messages
$tpl->tpl_parse	();
echo $tpl->html;

ob_end_flush();


?>
