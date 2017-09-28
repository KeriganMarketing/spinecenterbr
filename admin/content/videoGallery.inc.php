<?php
$allowed = TRUE;
mysql_close($database);
$database = mysql_connect($host,$user,$pass);
if(!mysql_select_db($db,$database)){
	$allowed = FALSE;
}

$query = "SELECT * FROM videoCategories";
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

$pageName = "/?cmd=videoGallery";

if($_GET['action'] == 'delete' && is_numeric($_GET['id'])){
	
	//define
	$id = clean($_GET['id']);
	
	//define querys
	$deleteQuery = "DELETE FROM videos WHERE id='$id'";

	$currentQuery = "SELECT fileName FROM videos WHERE id=$id";
	$currentResult = mysql_query($currentQuery, $database);
	$currentRecord = mysql_fetch_assoc($currentResult);
	$current = $currentRecord['fileName'];
		
	if(!(mysql_query($deleteQuery, $database))){
		$error .= "There was an error removing this video. ".mysql_error().'<br />';
	}else{
		$msg .= 'The video was successfully removed.<br />';
	}
	
}elseif($_GET['action'] == 'deleteCategory' && is_numeric($_GET['id'])){
	
	//define
	$id = mysql_real_escape_string($_GET['id']);
	
	//define querys
	$deleteQuery = "DELETE FROM videoCategories WHERE id='$id'";
	
	if(!(mysql_query($deleteQuery, $database))){
		$error .= "There was an error removing this video category. ".mysql_error().'<br />';
	}else{
		$msg .= 'The category was successfully removed.<br />';
	}
	
}elseif($_POST['cmd'] == 'editAction'){
	//define variables
	$url = clean($_POST['url']);
	$title = clean($_POST['title']);
	$description = clean(stripslashes($_POST['description']));
	$category = mysql_real_escape_string($_POST['category']);
	$time = clean($_POST['time']);
	$id = clean($_POST['id']);
	
	$passCheck = TRUE;
	
	
	//define querys
	$vars = "title='$title', description='$description', time='$time', url='$url'";
	if($hasCategories){
		$vars .= ", category='$category'";
	}
	
	if($id != ''){//update a video after uploading a new image
		$updateQuery = "UPDATE videos SET ".$vars." WHERE id='$id'";
	}else{//add new video
		$updateQuery = "INSERT INTO videos SET ".$vars.", navOrder='1000'";
	}
	if($passCheck){
		if(!(mysql_query($updateQuery))){
			$passCheck = FALSE;
			$error .= 'Query failed, '. mysql_error();
			$_GET['action'] = 'edit';
			$_GET['id'] = $id;
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
			
			$updatedVideoID = $_POST['sortorder'][$i];
			if($updatedVideoID!= ''){
				$newOrderNum = $i*10;
				$query = "UPDATE videos SET navOrder='$newOrderNum' WHERE id=".$_POST['sortorder'][$i]." AND category = '".$_POST['catsort']."'";
				
				if(mysql_query($query)){
					$success = TRUE;
					$msg = 'Videos have been arranged in the order specified.';
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
			
			$updatedVideoID = $_POST['sortorder'][$i];
			if($updatedVideoID!= ''){
				$newOrderNum = $i*10;
				$query = "UPDATE videos SET navOrder='$newOrderNum' WHERE id=".$_POST['sortorder'][$i];
				
				if(mysql_query($query)){
					$success = TRUE;
					$msg = 'Videos have been arranged in the order specified.';
					//unset($_POST);
				}else{
					$error .= 'There was an error processing your request. <br />'.mysql_error();
				}
			}
		}
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
		$updateQuery = "UPDATE videoCategories SET ".$vars." WHERE id='$id'";
	}else{//add new photo
		$updateQuery = "INSERT INTO videoCategories SET ".$vars;
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
}elseif($_GET['action'] == 'feature' && is_numeric($_GET['id'])){
	//define variables
	$id = clean($_GET['id']);
	
	//unset the current featured vid
	$feauredQuery = "UPDATE videos SET featured='0' WHERE featured='1'";
	if(!mysql_query($feauredQuery)){
		die(mysql_error());
	}
	
	$query = "UPDATE videos SET featured='1' WHERE id='$id'";
	if(!mysql_query($query)){
		die(mysql_error());
	}else{
		$msg = 'Video successfully updated.<br />';
	}
}
if($_GET['success'] == '1'){
	$msg .= "Information updated successfully.<br />";
}
?>