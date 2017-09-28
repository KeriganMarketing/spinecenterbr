<?php
//ini_set('display_errors', 'On');
$allowed = TRUE;
mysql_close($database);
$database = mysql_connect($host,$user,$pass);
if(!mysql_select_db($db,$database)){
	$allowed = FALSE;
}
$pageBase = '/?cmd=newsletters';

$defaultDate = date('Y-m-d');

$query = "SELECT id FROM newsletterCategories";
$result = mysql_query($query);
if($result){
	$hasCategories = TRUE;
}

$query = "SELECT url FROM newsletters";
$result = mysql_query($query);
if($result){
	$isOnline = TRUE;
}

$query = "SELECT image FROM newsletters";
$result = mysql_query($query);
if($result){
	$isPrinted = TRUE;
}

//categories function
function get_categories(){
	$query = "SELECT name, id FROM newsletterCategories ORDER BY name";
	$result = mysql_query($query);
	$array = array();
	while($record = mysql_fetch_assoc($result)){
		$array[$record['id']] = $record['name'];
	}
	return $array;
}

if($hasCategories){
	function get_categories(){
		$query = "SELECT name, id FROM newsletterCategories ORDER BY name";
		$result = mysql_query($query);
		$array = array();
		while($record = mysql_fetch_assoc($result)){
			$array[$record['id']] = $record['name'];
		}
		return $array;
	}
	$categories = get_categories();
}

//define dir for images
$dir = '../images/uploads/newsletters/';   // Path To Images Directory

function delete_image($id,$dir){
	//check to see if current image exists
	$q = "SELECT image FROM newsletters WHERE id='$id'";
	$res = mysql_query($q);
	$rec = mysql_fetch_assoc($res);
	if($rec['image'] != ''){
		if(!unlink($dir.$rec['image'])){
			//$error .= 'There was an error deleting the current image.<br />';
			return FALSE;
		}else{
			return TRUE;
		}
	}
}


ini_set('upload_max_filesize', 6000000);

if(file_exists($dir)){
	$hasImage = TRUE;
	$image = $_FILES['image'];
	$imageName = clean($image['name']);
}

if($_POST['action'] == 'process'){
	$id = clean($_POST['id']);
	$url = clean($_POST['url']);
	$name = clean($_POST['name']);
	$info = clean(stripslashes($_POST['info']));
	$active = clean($_POST['active']);
	if($hasCategories){
		$category = clean($_POST['category']);
	}
	$datePosted = clean($_POST['datePosted']);
	
	$passCheck = TRUE;
	if($name == ''){ $passCheck = FALSE; $error = 'Name cannot be left blank.<br />'; }
	if($hasCategories){
		if($category == '' || !is_numeric($category)){ $passCheck = FALSE; $error = 'Invalid Category selection.<br />'; }
	}
	
	if($passCheck){
		$vals = "name='$name', active='$active', info='$info', datePosted='$datePosted'";
		if($hasCategories){
			$vals .= ", category='$category'";
		}
		if($isOnline){
			$vals .= ", url='$url'";
		}
		if($hasImage){
			if($imageName != ''){
				if(file_upload($image,$dir)){
					$vals .= ", image='$imageName'";
					$msg .= 'Newsletter successfully uploaded<br />';
					
					$deleteImage = TRUE;
				}else{
					$error .= 'The newsletter didn\'t upload correctly<br />';
				}
				if($deleteImage){
					delete_image($id,$dir);
				}
			}
		}
		
		if($id == ''){
			$query = "INSERT INTO newsletters SET $vals";
		}else{
			$query = "UPDATE newsletters SET $vals WHERE id='$id'";
		}
		if(mysql_query($query)){
			$msg .= 'Information updated successfully.<br />'.$vals;
			unset($_POST);
			//reget campains
			$categories = get_categories();
		}else{
			$error .= 'There was an error processing your query. '.mysql_error().' '.$query.'<br />';
			if($id != ''){
				$_GET['id'] = $id;
				$_GET['action'] = 'edit';
			}
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
			
			$updatedClientID = $_POST['sortorder'][$i];
			if($updatedClientID!= ''){
				$newOrderNum = $i*10;
				$query = "UPDATE newsletters SET sortOrder='$newOrderNum' WHERE id=".$_POST['sortorder'][$i]." AND category = '".$_POST['catsort']."'";
				
				if(mysql_query($query)){
					$success = TRUE;
					$msg = 'Newsletters have been arranged in the order specified.';
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
			
			$updatedClientID = $_POST['sortorder'][$i];
			if($updatedClientID!= ''){
				$newOrderNum = $i*10;
				$query = "UPDATE newsletters SET sortOrder='$newOrderNum' WHERE id=".$_POST['sortorder'][$i]." ";
				
				if(mysql_query($query)){
					$success = TRUE;
					$msg = 'Newsletters have been arranged in the order specified.';
					//unset($_POST);
				}else{
					$error .= 'There was an error processing your request. <br />'.mysql_error();
				}
			}
		}	
	}
}elseif($_POST['action'] == 'category'){
	$id = clean($_POST['id']);
	$name = clean($_POST['cat_name']);
	$active = clean($_POST['active']);
	
	$passCheck = TRUE;
	if($name == ''){ $passCheck = FALSE; $error .= 'Name cannot be left blank.<br />'; }
	
	if($passCheck){
		$vals = "name='$name', active='$key'";
		if($id == ''){
			$query = "INSERT INTO newsletterCategories SET $vals";
		}else{
			$query = "UPDATE newsletterCategories SET $vals WHERE id='$id'";
		}
		if(mysql_query($query)){
			$msg .= 'Information updated successfully.<br />';
			//reget campains
			$categories = get_categories();
		}else{
			$error .= 'There was an error processing your query. '.mysql_error().' '.$query.'<br />';
			if($id != ''){
				$_GET['id'] = $id;
				$_GET['action'] = 'category';
			}
		}
	}
}elseif($_GET['action'] == 'disable' && $_GET['id'] != '' && is_numeric($_GET['id'])){
	$id = clean($_GET['id']);
	$item = $_GET['item'];
	
	if($item == 'category'){
		$query = "UPDATE newsletterCategories SET active='0' WHERE id='$id'";
		
		if(mysql_query($query)){
			$msg .= 'Information deleted successfully';
			unset($_POST);
		}else{
			$error .= 'There was an error processing your query. '.mysql_error().' '.$query.'<br />';
		}
	}elseif($item == 'client'){
		$query = "UPDATE newsletters SET active='0' WHERE id='$id'";
		
		if(mysql_query($query)){
			$msg .= 'Information updated successfully';
			//unset($_POST);
		}else{
			$error .= 'There was an error processing your query. '.mysql_error().' '.$query.'<br />';
		}
	}
}elseif($_GET['action'] == 'enable' && $_GET['id'] != '' && is_numeric($_GET['id'])){
	$id = clean($_GET['id']);
	$item = $_GET['item'];
	
	if($item == 'category'){
		$query = "UPDATE newsletterCategories SET active='1' WHERE id='$id'";
		
		if(mysql_query($query)){
			$msg .= 'Information deleted successfully';
			unset($_POST);
		}else{
			$error .= 'There was an error processing your query. '.mysql_error().' '.$query.'<br />';
		}
	}elseif($item == 'client'){
		$query = "UPDATE newsletters SET active='1' WHERE id='$id'";
		
		if(mysql_query($query)){
			$msg .= 'Information updated successfully';
		}else{
			$error .= 'There was an error processing your query. '.mysql_error().' '.$query.'<br />';
		}
	}
}elseif($_GET['action'] == 'delete' && $_GET['id'] != '' && is_numeric($_GET['id'])){
	$id = clean($_GET['id']);
	$item = $_GET['item'];
	
	if($item == 'category'){
		$query = "DELETE FROM newsletterCategories WHERE id='$id'";
		$query2 = "DELETE FROM newsletters WHERE category='$id'";
		
		$q = "SELECT id FROM newsletters WHERE category = '$id'";
		$res = mysql_query($q);
		while($rec = mysql_fetch_assoc($res)){
			delete_image($rec['id'],$dir);
		}
		
		if(mysql_query($query) && mysql_query($query2)){
			$msg .= 'Information deleted successfully';
		}else{
			$error .= 'There was an error processing your query. '.mysql_error().' '.$query.'<br />';
		}
	}elseif($item == 'client'){
		$query = "DELETE FROM newsletters WHERE id='$id'";
		delete_image($id,$dir);
		
		if(mysql_query($query)){
			$msg .= 'Information deleted successfully';
		}else{
			$error .= 'There was an error processing your query. '.mysql_error().' '.$query.'<br />';
		}
	}
}
?>