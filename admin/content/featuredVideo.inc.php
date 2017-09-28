<?php
$allowed = TRUE;
mysql_close($database);
$database = mysql_connect($host,$user,$pass);
if(!mysql_select_db($db,$database)){
	$allowed = FALSE;
}
$pageName = "/?cmd=featuredVideo";
$error = '';
$msg = '';

$prev = '';
if($allowed){
	if($_POST['cmd'] == 'process'){
		
		//pass vars
		$url = clean($_POST['url']);
		$runtime = clean($_POST['runtime']);
		$title = clean($_POST['title']);
		$description = clean(stripslashes($_POST['description']));
		
		$passCheck = TRUE;
		if($url == ''){ $passCheck = FALSE; $error .= 'YouTube URL cannot be left blank.<br />'; }
		if($runtime == ''){ $passCheck = FALSE; $error .= 'Runtime cannot be left blank.<br />'; }
		if($title == ''){ $passCheck = FALSE; $error .= 'Title cannot be left blank.<br />'; }
		
		if($passCheck){
			$query = "TRUNCATE TABLE featuredVideo";
			if(!mysql_query($query)){
				die('there was an error processing your query');
			}
			$query = "INSERT INTO featuredVideo SET url='$url', runtime='$runtime', title='$title', description='$description'";
			if(!mysql_query($query)){
				die('there was an error processing your second query');
			}else{
				$msg .= 'Video updated successfully.<br />';
			}
		}
	}
}
?>