<?php
$allowed = TRUE;
mysql_close($database);
$database = mysql_connect($host,$user,$pass);
if(!mysql_select_db($db,$database)){
	$allowed = FALSE;
}

$query = "SELECT * FROM downloadCategories";
$result = mysql_query($query);
if($result){
	$hasCategories = TRUE;
	$categories = array();
	while($record = mysql_fetch_assoc($result)){
		$key = $record['id'];
		$value = $record['name'];
		$categories[$key] = $value;
	}
}

ini_set('upload_max_filesize', 6000000);

$dir = '../downloads/';

$file = $_FILES['file'];
//print_r($_FILES['file']);
	
$action = $_POST['action'];
//print_r($_POST);


if($action != ''){
	$id = clean($_POST['id']);
	$title = clean($_POST['title']);
	$description = clean(stripslashes($_POST['description']));
	$today = date("Y/m/d");
	$filename = clean($file['name']);
	if($hasCategories){
		$category = clean($_POST['category']);
		$name = clean($_POST['name']);
	}
	
	//checks
	$passCheck = TRUE;
	if($title == ''){ $passCheck = FALSE; $error .= 'Title cannot be left blank.<br />'; }
	if($filename != ''){
		//allowable file types
		$allowableFileTypes = array('application/msword','application/pdf','application/x-pdf','application/acrobat','text/pdf','text/x-pdf','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		if(!in_array($file['type'],$allowableFileTypes)){
			$passCheck = FALSE;
			$error .= 'The file must be a pdf or MS Word.<br />';
		}
		$nameArray = array();
		$nameQuery = "SELECT file FROM downloads";
		$nameResult = mysql_query($nameQuery, $database);
		while($nameRecord = mysql_fetch_array($nameResult)){
			array_push($nameArray,$nameRecord['file']);
		}
		if(in_array($filename,$nameArray)){
			$fileUsed = TRUE;
		}
		if($fileUsed){
			$passCheck = FALSE;
			$error = 'A file with that name already exists. Please change the file name before uploading.<br />';
		}
	}
	if($action == 'add' && $filename == ''){ $passCheck = FALSE; $error .= 'You must select a file to upload.<br />'; }
	
	if($action == 'delete'){$error = ''; $passCheck = TRUE;}
	
	$params = "title='$title', date='$today', description='$description'";
	
	if($hasCategories){
		$params .= ", category='$category'";
		if($_POST['action'] == 'category'){
			if($name != ''){
				$passCheck = TRUE; $error = '';
			}else{
				$passCheck = FALSE; $error .= 'Name cannot be left blank<br />';
			}
		}
	}
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
		//get current filename
		$fileQ = "SELECT file FROM downloads WHERE id='$id' LIMIT 1";
		$fileRes = mysql_query($fileQ);
		$fileRec = mysql_fetch_assoc($fileRes);
		
		$currentFile = $fileRec['file'];
		if($action == 'add'){
			$query = "INSERT INTO downloads SET ".$params;
		}elseif($action == 'edit'){
			$query = "UPDATE downloads SET ".$params." WHERE id='$id'";
			if($filename != ''){
				unlink($dir.$currentFile);
			}
		}elseif($action == 'delete'){

			if(unlink($dir.$currentFile)){
				$msg.='File Deleted successfully<br />';
			}else{
				$error .= 'There was an error deleting the current file';
			}
			
			$query = "DELETE FROM downloads WHERE id='$id'";
		}elseif($action == 'category'){
			if($id != ''){
				$query = "UPDATE downloadCategories SET name='$name' WHERE id='$id' LIMIT 1";
			}else{
				$query = "INSERT INTO downloadCategories SET name='$name'";
			}
		}
		if(!$result = mysql_query($query)){
			$passCheck = FALSE;
			$error .= mysql_error().'<br>'.$query;
		}else{
			$msg .= 'success';
		}
		if(!$passCheck && $action == 'edit'){
			$_GET['id'] = $id;
			$show = 'edit';
		}
		unset($_POST);
	}else{
		if($action == 'edit'){
			$show = 'edit';
			$_GET['id'] = $id;
		}
	}
}
if($_GET['action'] == 'deleteCategory'){
	$id = clean($_GET['id']);
	$query = "DELETE FROM downloadCategories WHERE id='$id' LIMIT 1";
	if(!$result = mysql_query($query)){
		$passCheck = FALSE; $error .= 'Query couldn\'t run<br /> <!--'.mysql_error().'-->';
	}
}
?>