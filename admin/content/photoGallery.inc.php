<?php
$allowed = TRUE;
mysql_close($database);
$database = mysql_connect($host,$user,$pass);
if(!mysql_select_db($db,$database)){
	$allowed = FALSE;
}

$query = "SELECT * FROM photoCategories";
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

$query = "SELECT datePosted FROM photos";
$result = mysql_query($query);
if($result){
	$datePosted = TRUE;
}

ini_set('upload_max_filesize', 6000000);
$pageName = "/?cmd=photoGallery";
$dir = '../images/gallery/images/';   // Path To Images Directory

$file = $_FILES['imagefile'];
//print_r($file);

if($_GET['action'] == 'delete' && is_numeric($_GET['id'])){
	
	//define
	$ID = mysql_real_escape_string($_GET['id']);
	
	//define querys
	$deleteQuery = "DELETE FROM photos WHERE ID='$ID'";

	$currentQuery = "SELECT fileName FROM photos WHERE ID=$ID";
	$currentResult = mysql_query($currentQuery, $database);
	$currentRecord = mysql_fetch_assoc($currentResult);
	$current = $currentRecord['fileName'];
	
	if(!unlink($dir.$current)){
		$passCheck = FALSE;
		$error .= "There was an error trying to delete the file.<br />";
	}
		
	if(!(mysql_query($deleteQuery, $database))){
		$error .= "There was an error removing this photo. ".mysql_error().'<br />';
	}else{
		$msg .= 'The photo was successfully removed.<br />';
	}
	
}elseif($_GET['action'] == 'deleteCategory' && is_numeric($_GET['id'])){
	
	//define
	$id = mysql_real_escape_string($_GET['id']);
	
	//define querys
	$deleteQuery = "DELETE FROM photoCategories WHERE id='$id'";
	
	if(!(mysql_query($deleteQuery, $database))){
		$error .= "There was an error removing this photo. ".mysql_error().'<br />';
	}else{
		$msg .= 'The category was successfully removed.<br />';
	}
	
}elseif($_POST['cmd'] == 'editAction'){
	//define variables
	$title = mysql_real_escape_string($_POST['title']);
	$caption = mysql_real_escape_string($_POST['caption']);
	$category = mysql_real_escape_string($_POST['category']);
	$ID = mysql_real_escape_string($_POST['ID']);
	
	$passCheck = TRUE;
	
	//allowable file types
	if($file['name'] != ''){
		$allowableFileTypes = array('image/jpg','image/jpeg','image/pjpeg');
		if(!in_array($file['type'],$allowableFileTypes)){
			$passCheck = FALSE;
			$error .= 'The file must be a jpg.<br />';
		}
		if($file['size'] > '6000000'){
			$passCheck = FALSE;
			$error = 'The file is too large. Please resize to less than 6MB.<br />';
		}
		
		$nameArray = array();
		$nameQuery = "SELECT fileName FROM photos";
		$nameResult = mysql_query($nameQuery, $database);
		while($nameRecord = mysql_fetch_array($nameResult)){
			array_push($nameArray, $nameRecord['fileName']);
		}
		
		if(in_array($file['name'], $nameArray)){
			$passCheck = FALSE;
			$error .= 'A file with that name already exists, please change the filename before you upload.<br />';
		}
	}
//	echo($_FILES['imagefile']['name']."<br>".);
//	print_r($nameArray);
	if($passCheck){
		// Uploading/Resizing Script
		$url = mysql_real_escape_string($file['name']);   // Set $url To Equal The Filename For Later Use
		
		if($url != ''){
			if(file_upload($file,$dir)){
				$msg .= 'File successfully uploaded<br />';
			}else{
				$error .= 'The image didn\'t upload correctly<br />';
			}
		}
	}
	
	
	//define querys
	$vars = "title='$title', caption='$caption'";
	if($hasCategories){
		$vars .= ", category='$category'";
	}
	if($url != ''){
		$vars .= ", fileName='$url'";
		if($ID != ''){
			$deleteCurrent = TRUE;
		}
	}
	
	if($ID != ''){//update a photo after uploading a new image
		$updateQuery = "UPDATE photos SET ".$vars." WHERE ID='$ID'";
	}elseif($file != ''){//add new photo
		$updateQuery = "INSERT INTO photos SET ".$vars.", sortOrder='0'";
	}else{
		$error .= 'You must upload a photo.';
	}
	
	if($passCheck){
		if($deleteCurrent){
			$currentQuery = "SELECT fileName FROM photos WHERE ID=$ID";
			$currentResult = mysql_query($currentQuery, $database);
			$currentRecord = mysql_fetch_assoc($currentResult);
			$current = $currentRecord['fileName'];
			
			if(!unlink($dir.$current)){
				$passCheck = FALSE;
				$error .= "There was an error trying to delete the current file.<br />";
			}
		}
	}
	if($passCheck){
		if(!(mysql_query($updateQuery, $database))){
			$passCheck = FALSE;
			$error .= 'Query failed, '. mysql_error();
			$_GET['action'] == 'edit';
			$_GET['id'] == $ID;
		}else{
			$msg .= "Information updated successfully.<br />";
			unset($_POST);
		}
	}
}elseif($_POST['cmd'] == 'sortOrder'){
	if($_POST['catsort']){
		//cleanse input
		$count = count($_POST['id']);
		$newSortOrder = $_POST['sortorder'];
		//print_r($_POST);
		$id = $_POST['id'];
		
		for ($i = 0, $l = count($_POST['sortorder']); $i < $l; $i++) {
			
			$updatedPhotoID = $_POST['sortorder'][$i];
			if($updatedPhotoID!= ''){
				$newOrderNum = $i*10;
				$query = "UPDATE photos SET sortOrder='$newOrderNum' WHERE ID=".$_POST['sortorder'][$i]." AND category = '".$_POST['catsort']."'";
				
				if(mysql_query($query)){
					$success = TRUE;
					$msg = 'Photos have been arranged in the order specified.';
					//unset($_POST);
				}else{
					$error .= 'There was an error processing your request. <br />'.mysql_error();
				}
			}
		}	
	} else {
		//cleanse input
		$count = count($_POST['id']);
		$newSortOrder = $_POST['sortorder'];
		//print_r($_POST);
		$id = $_POST['id'];
		
		for ($i = 0, $l = count($_POST['sortorder']); $i < $l; $i++) {
			
			$updatedPhotoID = $_POST['sortorder'][$i];
			if($updatedPhotoID!= ''){
				$newOrderNum = $i*10;
				$query = "UPDATE photos SET sortOrder='$newOrderNum' WHERE ID=".$_POST['sortorder'][$i]." ";
				
				if(mysql_query($query)){
					$success = TRUE;
					$msg = 'Photos have been arranged in the order specified.';
					//unset($_POST);
				}else{
					$error .= 'There was an error processing your request. <br />'.mysql_error();
				}
			}
		}	
		
		/*//get valuse and place them in a proper array
		$i = 0;
		foreach($_POST['id'] as $value){
			$value = clean($value);
			$order = clean($_POST['sortorder'][$i]);
			$sort[$value] = $order;
			$i++;
		}
		
		$passCheck = TRUE;
		//check to make sure all keys and values are numeric
		foreach($sort as $key=>$value){
			if(!is_numeric($key)){
				$passCheck = FALSE;
				$error .= 'There was an invalid id value';
				break;
			}
			if(!is_numeric($value)){
				$passCheck = FALSE;
				$error .= 'There was an invalid sortorder value';
				break;
			}
		}
		if($passCheck){
			reset($sort);
			foreach($sort as $key=>$value){
				$query = "UPDATE photos SET sortOrder='$value' WHERE ID='$key'";
				if(!mysql_query($query)){
					$error .= 'There was an error executing query. '.mysql_error().'<br>';
					break;
				}
			}
		}*/
	}
}elseif($_POST['cmd'] == 'categories'){
	//define variables
	$name = mysql_real_escape_string($_POST['name']);
	$id = mysql_real_escape_string($_POST['id']);
	
	$passCheck = TRUE;
	if($name == ''){ $passCheck = FALSE; $error .= 'Name cannot be left blank'; }
	
	if(in_array($name, $categories)){
		$passCheck = FALSE;
		$error .= 'A category with that name already exists.<br />';
	}
	
	//define querys
	$vars = "name='$name'";
	
	if($id != ''){//update a photo after uploading a new image
		$updateQuery = "UPDATE photoCategories SET ".$vars." WHERE id='$id'";
	}else{//add new photo
		$updateQuery = "INSERT INTO photoCategories SET ".$vars;
	}
	if($passCheck){
		if(!(mysql_query($updateQuery, $database))){
			$passCheck = FALSE;
			$error .= 'Query failed, '. mysql_error();
			$_GET['action'] == 'categories';
			$_GET['id'] == $ID;
		}else{
			header("Location: ".$pageName."&success=1");
			die();
		}
	}
}
if($_GET['success'] == '1'){
	$msg .= "Information updated successfully.<br />";
}
?>