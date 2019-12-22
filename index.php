<?
include_once("config.php");

if(DB_SERVER=='YOUR_SERVER_HERE'){
	if(isset($_POST['install'])){
		if(!isset($_POST['server']))die('Don\'t know `server`, please click <a href="index.php">here</a> and try again.');
		if(!isset($_POST['user']))die('Don\'t know `user`, please click <a href="index.php">here</a> and try again.');
		if(!isset($_POST['password']))die('Don\'t know `password`, please click <a href="index.php">here</a> and try again.');
		if(!isset($_POST['dbname']))die('Don\'t know `database name`, please click <a href="index.php">here</a> and try again.');
		$servername = $_POST['server'];
		$username = $_POST['user'];
		$password = $_POST['password'];
		$dbname = $_POST['dbname'];
		$lang = $_POST['lang'];
		// create DB
		try {
			$conn = new PDO("mysql:host=$servername", $username, $password);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
			$conn->exec($sql);
		} catch(PDOException $e) {
			die($e->getMessage() . '<br/>Please click <a href="index.php">here</a> and try again.');
		}
		$conn = null;
		// create table
		try {
			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "CREATE TABLE IF NOT EXISTS `sab_people` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_spanish_ci DEFAULT NULL,
  `apedillos` varchar(128) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tfnos` varchar(2048) COLLATE utf8_spanish_ci DEFAULT NULL,
  `emails` varchar(2048) COLLATE utf8_spanish_ci DEFAULT NULL,
  `addresses` varchar(2048) COLLATE utf8_spanish_ci DEFAULT NULL,
  `webs` varchar(2048) COLLATE utf8_spanish_ci DEFAULT NULL,
  `notas` varchar(2048) COLLATE utf8_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
			$conn->exec($sql);
			
			$sql = "INSERT INTO `sab_people` (`name`, `apedillos`, `tfnos`, `emails`, `addresses`, `webs`, `notas`) VALUES ('Julifos', 'Surname', '+34 915555555', 'julifos@dummy.com', 'c/ Address Street, 4', 'http://www.julifos.com/', 'This guy is your first contact');";
			$conn->exec($sql);

		} catch(PDOException $e) {
			die($e->getMessage() . '<br/>Please click <a href="index.php">here</a> and try again.');
		}
		$conn = null;

		// insert values into config.php
		$config = file_get_contents('config.php');
		$config = str_replace('YOUR_SERVER_HERE',$servername,$config);
		$config = str_replace('YOUR_USERNAME_HERE',$username,$config);
		$config = str_replace('YOUR_PASSWORD_HERE',$password,$config);
		$config = str_replace('YOUR_DATABASE_NAME_HERE',$dbname,$config);
		$config = str_replace('YOUR_LANGUAGE_HERE',$lang,$config);
		
		$f=fopen("./config.php","w") or die("Can't write to config.php, try setting its permissions to 666!");
		ftruncate($f, 0);
		fwrite($f,$config);
		fclose($f);
		
		// un-cache config.php from OPcache if needed
		if (function_exists('opcache_invalidate') && strlen(ini_get("opcache.restrict_api")) < 1) {
			opcache_invalidate('config.php', true);
		} elseif (function_exists('apc_compile_file')) {
			apc_compile_file('config.php');
		}
		
		// refresh!
		header("Refresh:0");
		//echo '<html><meta http-equiv="refresh" content="1"><body>';
		exit();
	} else {
		echo <<<EOL
<!DOCTYPE html>
<html>
<style>h2,input,label{display:block;margin:5px;}</style>
<body>
<form method="post" id="ins">
<input type="hidden" name="install" value="1" />
<h2>Simple Address Book Installation</h2>
<label>Enter the basic details. You can change them later in the "config.php" file.</label>
<br/>
<label for="server">MySQL host (ie, 127.0.0.1)</label>
<input type="text" id="server" name="server" size="30" placeholder="MySQL host, ie 127.0.0.1" />
<label for="user">MySQL user name</label>
<input type="text" id="user" name="user" size="30" placeholder="MySQL user name" />
<label for="password">MySQL user password</label>
<input type="password" id="password" name="password" size="30" placeholder="**********" />
<label for="dbname">MySQL database name</label>
<input type="text" id="dbname" name="dbname" size="30" placeholder="database_name" />
<label for="lang">Language</label>
<select id="lang" name="lang">
EOL;
		$files = scandir("lang/");
		foreach ($files as $value) {
			if(strpos($value, "locale_")===0 && strpos($value, ".json")===9){
				$value = substr($value,7,2);
				echo "	<option value=\"$value\"".($value=='en'?' selected':'').">$value</option>\n";
			}
		}

		echo <<<EOL
</select>
<br/><br/>
<input type="submit" value="Submit"/>
</form>
</body>
</html>

EOL;
		error_log('OK');
		die();
	}
}
include_once("db.php");


$page = isset($_GET["page"]) ? $_GET["page"] * 1 : 1;
if(isset($_GET["search"])){
	list($totalPages,$people) = $db->search(urldecode($_GET["search"]),$page);
	$totalPages = floor($totalPages * 1/CONTACTS_PER_SHEET) + 1;
	$addon = "&search=" . $_GET["search"];
} else {
	list($totalPages,$people) = $db->getContacts($page);
	$totalPages = floor($totalPages * 1/CONTACTS_PER_SHEET) + 1;
	$addon = "";
}
?>
<!DOCTYPE html>
<html lang="<? echo LANGUAGE; ?>" xml:lang="<? echo LANGUAGE; ?>">
<head>
<title><? echo MY_CONTACTS; ?></title>
<link rel="apple-touch-icon" sizes="180x180" href="favicons/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="favicons/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="favicons/favicon-16x16.png">
<link rel="manifest" href="favicons/site.webmanifest">
<link rel="mask-icon" href="favicons/safari-pinned-tab.svg" color="#5bbad5">
<link href="https://fonts.googleapis.com/css?family=Roboto:300,700&display=swap" rel="stylesheet">
<meta name="msapplication-TileColor" content="#da532c">
<meta name="theme-color" content="#ffffff">
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=2" />
<meta name="description" content="">
<meta name="keywords" content="">
<link href="css/doc.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="js/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/main.js"></script>
<script>
<?php
echo "var people = JSON.parse('".addslashes(json_encode($people))."');\n";
echo "const DELETE_CONFIRM = JSON.parse('".addslashes(json_encode(DELETE_CONFIRM))."');\n";
echo "const EDIT = JSON.parse('".addslashes(json_encode(EDIT))."');\n";
echo "const DONT_EDIT = JSON.parse('".addslashes(json_encode(DONT_EDIT))."');\n";
echo "var phoneRegexes = [";
echo implode(",", PHONE_REGEX_PATTERNS);
echo "];\n";
?>
</script>
</head>

<body>
	<div>
		<h1><a href="index.php" style="text-decoration: none;border:0px none;"><img src="favicons/apple-touch-icon.png" style="height:48px; border:none; display:inline-block;" /> <? echo CONTACTS; ?></a></h1>
		<p id="nav"<? echo ($totalPages==1 ? ' style="display:none;"' : ''); ?>><form style="display:inline-block;" method="get" id="nv">
			<a href="?page=1<? echo $addon; ?>" id="first"<? echo ($page===1 ? ' class="disabled"' : ''); ?>>d</a>
			<a href="?page=<? echo ($page-1); echo $addon; ?>" id="prev"<? echo ($page===1 ? ' class="disabled"' : ''); ?>>b</a>
			<? echo PAGE; ?> <input type="text" id="page" name="page" size="3" value="<? echo $page; ?>" /> <? echo OF; ?> <? echo $totalPages; ?>
			<a href="?page=<? echo $page+1;echo $addon; ?>" id="next"<? echo ($page==$totalPages ? ' class="disabled"' : ''); ?>>a</a>
			<a href="?page=<? echo $totalPages;echo $addon; ?>" id="last"<? echo (($page==$totalPages) ? ' class="disabled"' : ''); ?>>c</a>
		</form></p>
	</div>
	<div>
		<div style="display: inline-block;float: right;margin-right:10px;position: fixed;right: 120px;top: 20px;"><form method="get" id="sf"><input type="text" id="search" name="search" size="20" placeholder="<? echo SEARCH; ?>" /><button type="submit" id="doSearch" style="">e</button></form></div>
		<button type="button" name="add" id="add" style="float: right;margin-right:10px;position: fixed;right: 20px;top: 20px;"><? echo ADD; ?></button>
	</div>
	<div id="gentes">
		<?
		foreach($people as $person){
			echo '<div class="person" data-id="'.$person['id'].'">';
			echo '	<div class="pname">'.$person['name'].' '.$person['apedillos'].'</div>';
			echo '</div>';
		}
		?>
	</div>
	<div id="addFormC">
		<button type="button" name="close" id="close" style="float: right;width: 30px;padding: 0px;font-size: 24px;display: inline-block;position: absolute;right: -5px;margin: 0px;top: -15px;">×</button>
		<form method="post" id="addForm" name="addForm" onsubmit="return addContact();" style="margin-top:10px;">	<label for="name"><? echo NAME; ?></label>
			<input name="name" type="text" id="name" placeholder="<? echo NAME_PH; ?>" maxlength="64">	<label for="lastname"><? echo SURNAME; ?></label>
			<input name="lastname" type="text" id="lastname" placeholder="<? echo SURNAME_PH; ?>" maxlength="128">	<label for="phones"><? echo PHONES; ?></label>
			<textarea name="phones" id="phones" placeholder="<? echo PHONES_PH; ?>"></textarea>	<label for="addresses"><? echo ADDRESSES; ?></label>
			<textarea name="addresses" id="addresses" placeholder="<? echo ADDRESSES_PH; ?>"></textarea>	<label for="emails"><? echo MAILS; ?></label>
			<textarea name="emails" id="emails" placeholder="<? echo MAILS_PH; ?>"></textarea>	<label for="webs"><? echo WEBS; ?></label>
			<textarea name="webs" id="webs" placeholder="<? echo WEBS_PH; ?>"></textarea>	<label for="notes"><? echo NOTES; ?></label>
			<textarea name="notes" id="notes" placeholder="<? echo NOTES_PH; ?>"></textarea>
			<button type="submit" name="send" id="send" style="float: right;margin-right: 0px;"> <? echo ADD; ?> </button>
		</form>
	</div>
	<div id="addFormC2" class="noedit">
		<button type="button" name="close2" id="close2" style="">×</button>
		<form method="post" id="addForm2" name="addForm2" onsubmit="return updateContact();" style="margin-top:10px;">	<label for="name2"><? echo NAME; ?></label>
			<input name="name2" type="text" id="name2" placeholder="<? echo NAME_PH; ?>" maxlength="64">
			<div class="input" id="name2d"></div>	<label for="lastname2"><? echo SURNAME; ?></label>
			<input name="lastname2" type="text" id="lastname2" placeholder="<? echo SURNAME_PH; ?>" maxlength="128">
			<div class="input" id="lastname2d"></div>	<label for="phones2"><? echo PHONES; ?></label>
			<textarea name="phones2" id="phones2" placeholder="<? echo PHONES_PH; ?>"></textarea>
			<div class="textarea" id="phones2d"></div>	<label for="addresses2"><? echo ADDRESSES; ?></label>
			<textarea name="addresses2" id="addresses2" placeholder="<? echo ADDRESSES_PH; ?>"></textarea>
			<div class="textarea" id="addresses2d"></div>	<label for="emails2"><? echo MAILS; ?></label>
			<textarea name="emails2" id="emails2" placeholder="<? echo MAILS_PH; ?>"></textarea>
			<div class="textarea" id="emails2d"></div>	<label for="webs2"><? echo WEBS; ?></label>
			<textarea name="webs2" id="webs2" placeholder="<? echo WEBS_PH; ?>"></textarea>
			<div class="textarea" id="webs2d"></div>	<label for="notes2"><? echo NOTES; ?></label>
			<textarea name="notes2" id="notes2" placeholder="<? echo NOTES_PH; ?>"></textarea>
			<div class="textarea" id="notes2d"></div>
			<input type="hidden" name="uid" id="uid" />
			<button type="submit" name="send2" id="send2" style="float: right;margin-right: 0px;"><? echo SAVE; ?></button>
			<button type="button" class="button" name="edit" id="edit" style="float: right;margin-right: 0px;"><? echo EDIT; ?></button>
			<!-- <button type="button" name="del" id="del" style="float: right;margin-right: 0px; width:24px; background-image:url(trash.png);background-size:auto 95%;background-repeat:no-repeat;background-position: center center;"></button>-->
			<button type="button" name="del" id="del" style="float: right;margin-right: 0px;" title="<? echo DELETE; ?>">&#x1F5D1;</button>
		</form>
	</div>
	<style id="nedit"></style>
</body>
</html>
