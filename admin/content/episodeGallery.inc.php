<?php
//parse a url with get variable
function parse_gets($url){
	$url = explode('?',$url);
	$vars = explode('&',$url[1]);
	foreach($vars as $value){
		$explode = explode('=',$value);
		$new_vars[$explode[0]] = $explode[1];
	}
	return $new_vars;
}

$allowed = TRUE;
mysql_close($database);
$database = mysql_connect($host,$user,$pass);
if(!mysql_select_db($db,$database)){
	$allowed = FALSE;
}

$pageName = "/?cmd=episodeGallery";

if($_GET['action'] == 'delete' && is_numeric($_GET['id'])){
	
	//define
	$ID = clean($_GET['id']);
	
	//define querys
	$deleteQuery = "DELETE FROM videos WHERE ID='$ID'";
		
	if(!(mysql_query($deleteQuery))){
		$error .= "There was an error removing this video. ".mysql_error().'<br />';
	}else{
		$msg .= 'The video was successfully removed.<br />';
	}
	
}elseif($_POST['cmd'] == 'editAction'){
	//define variables
	$URL = clean($_POST['URL']);
	$episodeNumber = clean($_POST['episodeNumber']);
	$episodeName = clean($_POST['episodeName']);
	$synopsis = clean($_POST['synopsis']);
	$originalAir = clean($_POST['originalAir']);
	$reAir = clean($_POST['reAir']);
	$ID = clean($_POST['ID']);
	
	$passCheck = TRUE;
	
	
	//define querys
	$vars = "episodeNumber='$episodeNumber',episodeName='$episodeName', synopsis='$synopsis', originalAir='$originalAir', reAir='$reAir', URL='$URL'";
	
	if($ID != ''){//update a video after uploading a new image
		$updateQuery = "UPDATE videos SET ".$vars." WHERE ID='$ID'";
	}else{//add new video
		$updateQuery = "INSERT INTO videos SET ".$vars;
	}
	if($passCheck){
		if(!(mysql_query($updateQuery))){
			$passCheck = FALSE;
			$error .= 'Query failed, '. mysql_error();
			$_GET['action'] = 'edit';
			$_GET['id'] = $ID;
		}else{
			$msg .= "Information updated successfully.<br />";
			unset($_POST);
		}
	}
}/*elseif($_GET['action'] == 'feature' && is_numeric($_GET['id'])){
	//define variables
	$ID = clean($_GET['id']);
	
	//unset the current featured vid
	$feauredQuery = "UPDATE videos SET featured='0' WHERE featured='1'";
	if(!mysql_query($feauredQuery)){
		die(mysql_error());
	}
	
	$query = "UPDATE videos SET featured='1' WHERE ID='$ID'";
	if(!mysql_query($query)){
		die(mysql_error());
	}else{
		$msg = 'Video successfully updated.<br />';
	}
}*/
if($_GET['success'] == '1'){
	$msg .= "Information updated successfully.<br />";
}
?>