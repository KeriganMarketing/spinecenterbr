<?php
$db = 'kmaserv_admin';
$host = 'localhost';
$user = 'kmaserv_usr';
$pass = 'R08Z9xZ?4{it';
$rurl = $_SERVER['REQUEST_URI'];

$database = mysql_connect($host,$user,$pass);
if(!mysql_select_db($db,$database)){
	die('error in db_connect'.mysql_error());
}
?>