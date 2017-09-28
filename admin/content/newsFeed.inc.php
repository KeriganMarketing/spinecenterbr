<?php
$allowed = TRUE;
mysql_close($database);
$database = mysql_connect($host,$user,$pass);
if(!mysql_select_db($db,$database)){
	$allowed = FALSE;
}
ini_set('upload_max_filesize', 6000000);
if($_GET['action'] == 'edit' && is_numeric($_GET['id'])){
	$show = 'edit';
}else{
	$show = 'list';
}

$dir = '../news-uploads/';
$file = $_FILES['file'];
//print_r($_FILES['file']);
	
$action = $_POST['action'];
//print_r($_POST);
if($action != ''){
	$id = clean($_POST['id']);
	$title = clean($_POST['title']);
	$content = clean(stripslashes($_POST['pageContent']));
	$date = clean(server_date($_POST['date']));
	$exp = clean(server_date($_POST['exp']));
	$urlText = clean($_POST['urlText']);
	$url = clean($_POST['url']);
	$filename = clean($file['name']);
	
	//checks
	$passCheck = TRUE;
	if($title == ''){ $passCheck = FALSE; $error .= 'Title cannot be left blank.<br />'; }
	if($date == ''){ $passCheck = FALSE; $error .= 'Post Date cannot be left blank.<br />'; }
	if($exp == ''){ $passCheck = FALSE; $error .= 'Ezpiration Date cannot be left blank.<br />'; }
	if($filename != ''){
		//allowable file types
		$allowableFileTypes = array('application/msword','application/pdf','application/x-pdf','application/acrobat','text/pdf','text/x-pdf','application/vnd.openxmlformats-officedocument.wordprocessingml.document');
		if(!in_array($file['type'],$allowableFileTypes)){
			$passCheck = FALSE;
			$error .= 'The file must be a pdf or MS Word.<br />';
		}
		$nameArray = array();
		$nameQuery = "SELECT file FROM newsFeed";
		$nameResult = mysql_query($nameQuery, $database);
		while($nameRecord = mysql_fetch_array($nameResult)){
			array_push($nameArray,$nameRecord['file']);
		}
		if(in_array($filename,$nameArray)){
			$fileUsed = TRUE;
		}
		if($fileUsed){
			$passCheck = FALSE;
			$error .= 'A file with that name already exists. Please change the file name before uploading.<br />';
		}
	}

	
	if($action == 'delete' && is_numeric($id)){ $passCheck = TRUE; $error = ''; }
	
	$params = "title='$title', date='$date', exp='$exp', urlText='$urlText', url='$url', content='$content'";
	
	if($filename != '' && $passCheck){
		// Uploading/Resizing Script
		if(file_upload($file,$dir)){
			$msg .= 'File successfully uploaded<br />';
			$params .= ", file='$filename'";
		}else{
			$error .= 'The file didn\'t upload correctly<br />';
			$passCheck = FALSE;
		}
	}
	
	if($passCheck){
		if($action == 'add'){
			$query = "INSERT INTO newsFeed SET ".$params;
		}elseif($action == 'edit'){
			$query = "UPDATE newsFeed SET ".$params." WHERE id='$id'";
		}elseif($action == 'delete'){
			$query = "DELETE FROM newsFeed WHERE id='$id'";
		}
		if(!$result = mysql_query($query)){
			$passCheck = FALSE;
			$error .= mysql_error().'<br>'.$query;
		}else{
			$msg .= 'Success';
		}
		if(!$passCheck && $action == 'edit'){
			$_GET['id'] = $id;
			$show = 'edit';
		}elseif($passCheck && $action != ''){
			unset($_POST);
		}
	}
}
if($_GET['action'] == 'deleteFile' && is_numeric($_GET['id'])){
	$id = clean($_GET['id']);
	//get current filename
	$fileQ = "SELECT file FROM newsFeed WHERE id='$id' LIMIT 1";
	$fileRes = mysql_query($fileQ);
	$fileRec = mysql_fetch_assoc($fileRes);
	
	if(unlink($dir.$fileRec['file'])){
		$query = "UPDATE newsFeed SET file='' WHERE id='$id'";
		$msg.='File Deleted successfully<br />';
	}else{
		$error .= 'There was an error deleting the current file';
	}
	if(!$result = mysql_query($query)){
		$error .= 'Delete Query failed. '.mysql_query;
	}
	
}
?>