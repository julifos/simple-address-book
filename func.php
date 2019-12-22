<?php
/*
Manage Ajax calls
*/
include_once("config.php");
include_once("db.php");
global $db;

if ($_POST['method']=='addContact'){
	echo $db->addContact($_POST['name'],$_POST['lastname'],$_POST['phones'],$_POST['addresses'],$_POST['emails'],$_POST['webs'],$_POST['notes'],$_POST['update'],$_POST['uid']);
} else if ($_POST['method']=='delContact'){
	echo $db->delContact($_POST['uid']);
}
?>