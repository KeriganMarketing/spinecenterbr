<?php
$allowed = TRUE;
mysql_close($database);
$database = mysql_connect($host,$user,$pass);
if(!mysql_select_db($db,$database)){
	$allowed = FALSE;
}

$query = "SELECT * FROM reg_fields LIMIT 1";
$result = mysql_query($query);
if($result){
	$registrationAllowed = TRUE;
}

ini_set('upload_max_filesize', 6000000);
if($_GET['action'] == 'edit' && is_numeric($_GET['id'])){
	$show = 'edit';
}elseif($_GET['action'] == 'users' && is_numeric($_GET['id'])){
	$show = 'users';
}elseif($_GET['action'] == 'user' && is_numeric($_GET['id'])){
	$show = 'user';
}else{
	$show = 'list';
}

$dir = '../calendar/';
$image = $_FILES['image'];
$file = $_FILES['file'];
//print_r($_FILES['file']);
	
$action = $_POST['action'];
//print_r($_POST);

if($_GET['action'] == 'deleteField' && $_GET['id'] != '' && is_numeric($_GET['id'])){
	if($_GET['id'] != 0){
		$q = "DELETE FROM reg_fields WHERE id=".$_GET['id']." LIMIT 1";
		if(mysql_query($q)){
			$msg = ('The field has been successfully removed.');
		}else{
			$msg = ('There was an error removing the field.');
		}
	}
	die('<p style="margin: 10px; font-family:Arial; font-size: 14px;">'.$msg.'</p>');
}
if($action != ''){
	$id = clean($_POST['id']);
	$title = clean($_POST['title']);
	$subhead = clean($_POST['subhead']);
	$showinfo = clean($_POST['showinfo']);
	$text = clean(stripslashes($_POST['pageContent']));
	$startDate = clean(server_date($_POST['startDate']));
	$endDate = clean(server_date($_POST['endDate']));
	$recurring = clean($_POST['recurring']);
	$frequency = clean($_POST['frequency']);
	$active = clean($_POST['active']);
	$time = clean($_POST['time']);
	$price = clean($_POST['price']);
	$free = clean($_POST['free']);
	$location = clean($_POST['location']);
	$url = clean($_POST['url']);
	$email = clean($_POST['email']);
	$registration = clean($_POST['registration']);
	$imagename = clean($image['name']);
	$filename = clean($file['name']);
	$comments = clean($_POST['comments']);
	$paymentInstructions = clean($_POST['paymentInstructions']);
	//checks
	$passCheck = TRUE;
	if($title == ''){ $passCheck = FALSE; $error .= 'Title cannot be left blank.<br />'; }
	if($startDate == ''){ $passCheck = FALSE; $error .= 'Start Date cannot be left blank.<br />'; }
	if($endDate == ''){ $passCheck = FALSE; $error .= 'End Date cannot be left blank.<br />'; }
	//if($time == ''){ $passCheck = FALSE; $error .= 'Time cannot be left blank.<br />'; }
	if($filename != ''){
		//allowable file types
		$allowableFileTypes = array('application/msword','application/pdf','application/x-pdf','application/acrobat','text/pdf','text/x-pdf','application/vnd.openxmlformats-officedocument.wordprocessingml.document');
		if(!in_array($file['type'],$allowableFileTypes)){
			$passCheck = FALSE;
			$error .= 'The file must be a pdf or MS Word.<br />';
		}
		$nameArray = array();
		$nameQuery = "SELECT file FROM eventCalendar";
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
	if($imagename != ''){
		//allowable file types
		$allowableFileTypes = array('image/jpg','image/jpeg','image/pjpeg','image/gif');
		if(!in_array($image['type'],$allowableFileTypes)){
			$passCheck = FALSE;
			$error .= 'The image must me a jpg or gif.<br />';
		}
		$nameArray = array();
		$nameQuery = "SELECT image FROM eventCalendar";
		$nameResult = mysql_query($nameQuery, $database);
		while($nameRecord = mysql_fetch_array($nameResult)){
			array_push($nameArray,$nameRecord['image']);
		}
		if(in_array($imagename,$nameArray)){
			$fileUsed = TRUE;
		}
		if($fileUsed){
			$passCheck = FALSE;
			$error .= 'An image with that name already exists. Please change the file name before uploading.<br />';
		}
	}
	
	if($action == 'delete'){$error = ''; $passCheck = TRUE;}
	
	$params = "title='$title', showinfo='$showinfo', subhead='$subhead', text='$text', startDate='$startDate', endDate='$endDate', recurring='$recurring', frequency='$frequency', active='$active', time='$time', price='$price', free='$free', location='$location', url='$url', email='$email', register='$registration', comments='$comments', paymentInstructions='$paymentInstructions'";
	
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
	
	if($imagename != '' && $passCheck){
		// Uploading/Resizing Script
		if(file_upload($image,$dir)){
			$msg .= 'Image successfully uploaded<br />';
			$params .= ", image='$imagename'";
		}else{
			$error .= 'The image didn\'t upload correctly<br />';
			$passCheck = FALSE;
		}
	}
	
	if($passCheck){
		if($action == 'add'){
			$query = "INSERT INTO eventCalendar SET ".$params;
		}elseif($action == 'edit'){
			$query = "UPDATE eventCalendar SET ".$params." WHERE id='$id'";
		}elseif($action == 'delete'){
			
			//get current filename
			$fileQ = "SELECT file,image FROM eventCalendar WHERE id='$id' LIMIT 1";
			$fileRes = mysql_query($fileQ);
			$fileRec = mysql_fetch_assoc($fileRes);
	
			if($fileRec['file'] != ''){
				if(unlink($dir.$fileRec['file'])){
					$msg.='File Deleted successfully<br />';
				}else{
					$error .= 'There was an error deleting the current file';
				}
			}
	
			if($fileRec['image'] != ''){
				if(unlink($dir.$fileRec['image'])){
					$msg.='Image Deleted successfully<br />';
				}else{
					$error .= 'There was an error deleting the current image';
				}
			}
			
			mysql_query("DELETE FROM reg_users WHERE eID='$id'");
			mysql_query("DELETE FROM reg_responses WHERE eID='$id'");
			mysql_query("DELETE FROM reg_fields WHERE eID='$id'");
			
			$query = "DELETE FROM eventCalendar WHERE id='$id'";
			$registration = 1;
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
	}else{
		if($action == 'edit'){
			$show = 'edit';
			$_GET['id'] = $id;
		}
	}
	if($registration == '1'){
		$field = make_array($_POST['field']);
		$type = make_array($_POST['type']);
		$required = make_array($_POST['required']);
		$fieldID = make_array($_POST['fieldID']);
		$field = clean($field);
		$type = clean($type);
		$required = clean($required); 
		$fieldID = clean($fieldID);
		
		if($action == 'add'){
			$query = "SELECT id FROM eventCalendar ORDER BY id DESC LIMIT 1";
			$result = mysql_query($query);
			$record = mysql_fetch_assoc($result);
			
			$id = $record['id'];
		}
		
		foreach($field as $k => $v){
			$vals = "content='$v', eID='$id', type='".$type[$k]."', required='".$required[$k]."', sortOrder=$k";
			if($fieldID[$k] == '0'){
				$query = "INSERT INTO reg_fields SET $vals";
			}elseif($v != ''){
				$query = "UPDATE reg_fields SET $vals WHERE id=".$fieldID[$k];
			}
			if(!mysql_query($query)){
				die(mysql_error());
			}
		}
	}
}
if($_GET['action'] == 'deleteFile' && is_numeric($_GET['id'])){
	$id = clean($_GET['id']);
	$show = 'edit';
	//get current filename
	$fileQ = "SELECT file FROM eventCalendar WHERE id='$id' LIMIT 1";
	$fileRes = mysql_query($fileQ);
	$fileRec = mysql_fetch_assoc($fileRes);
	
	if(unlink($dir.$fileRec['file'])){
		$query = "UPDATE eventCalendar SET file='' WHERE id='$id'";
		$msg.='File Deleted successfully<br />';
	}else{
		$error .= 'There was an error deleting the current file';
	}
	if(!$result = mysql_query($query)){
		$error .= 'Delete Query failed. '.mysql_query;
	}
	
}
if($_GET['action'] == 'deleteImage' && is_numeric($_GET['id'])){
	$id = clean($_GET['id']);
	$show = 'edit';
	//get current filename
	$fileQ = "SELECT image FROM eventCalendar WHERE id='$id' LIMIT 1";
	$fileRes = mysql_query($fileQ);
	$fileRec = mysql_fetch_assoc($fileRes);
	
	if(unlink($dir.$fileRec['image'])){
		$query = "UPDATE eventCalendar SET image='' WHERE id='$id'";
		$msg.='File Deleted successfully<br />';
	}else{
		$error .= 'There was an error deleting the current file';
	}
	if(!$result = mysql_query($query)){
		$error .= 'Delete Query failed. '.mysql_query;
	}
	
}
if($passCheck && $action != ''){
	unset($_POST);
}

if($_GET['action'] == 'export' && is_numeric($_GET['id'])){
	
	$eID = clean($_GET['id']);
	
	//put the quetions in an array with id as key
	$query = "SELECT id, content FROM reg_fields WHERE eID=$eID ORDER BY sortOrder";
	$result = mysql_query($query);
	
	$questions = array();
	while($record = mysql_fetch_assoc($result)){
		$questions[$record['id']] = $record['content'];
	}
	$questions['comments'] = "Comments";
	
	//put users into an array
	$query = "SELECT * FROM reg_users WHERE eID=$eID";
	if(!$result = mysql_query($query)){
		die(mysql_error());
	}
	
	$users = array();
	while($record = mysql_fetch_assoc($result)){
		$users[$record['id']] = array('name'=>$record['name'], 'date'=>$record['date'], 'paid'=>$record['paid']);
	}
	
	$data = array();
	foreach($users as $id => $user){
		$query = "SELECT fID, response FROM reg_responses WHERE uID=$id";
		if(!$result = mysql_query($query)){
			die(mysql_error());
		}
		$answers = array();
		while($record = mysql_fetch_assoc($result)){
			$answers[$questions[$record['fID']]] = $record['response'];
		}
		$data[] = array_merge($user,$answers);
	}
	
	//write an array to excel
	function getExcelData($data){
		$retval = "";
		if(is_array($data) && !empty($data)){
			$row = 0;
			foreach($data as $_data){
				if(is_array($_data) && !empty($_data)){
					if($row == 0){
						// write the column headers
						$retval = implode(",",array_keys(str_replace(',','',$_data)));
						$retval .= "\n";
					}
					//create a line of values for this row...
					$retval .= implode(",",array_values(str_replace(',','',$_data)));
					$retval .= "\n";
					//increment the row so we don't create headers all over again
					$row++;
				}
			}
		}
		return $retval;
	}
	
	//feed the final array to our formatting function...
	$contents = getExcelData($data);
	
	$filename = "calendarExport.csv";
	
	//prepare to give the user a Save/Open dialog...
	header ("Content-type: application/octet-stream");
	header ("Content-Disposition: attachment; filename=".$filename);
	
	//setting the cache expiration to 30 seconds ahead of current time. an IE 8 issue when opening the data directly in the browser without first saving it to a file
	$expiredate = time() + 30;
	$expireheader = "Expires: ".gmdate("D, d M Y G:i:s",$expiredate)." GMT";
	header ($expireheader);
	
	//output the contents
	echo $contents;
	die();
	
}

?>