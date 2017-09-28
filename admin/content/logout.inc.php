<?php
	setcookie("session_id", '');
	if($_COOKIE['remember'] == '1'){ setcookie("remember", ''); }
	header('Location: /');
?>