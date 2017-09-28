<?php
//ini_set('display_errors', 'On');

$allowed = TRUE;
mysql_close($database);
$database = mysql_connect($host,$user,$pass);
if(!mysql_select_db($db,$database)){
	$allowed = FALSE;
}
$pageBase = '/?cmd=officeLocations';

$query = "SELECT category FROM officeLocations LIMIT 1";
$result = mysql_query($query);
if($result){
	$hasCategories = TRUE;
}

$query = "SELECT contactName FROM officeLocations LIMIT 1";
$result = mysql_query($query);
if($result){
	$hasCName = TRUE;
}

$query = "SELECT contactEmail FROM officeLocations LIMIT 1";
$result = mysql_query($query);
if($result){
	$hasCEmail = TRUE;
}

$query = "SELECT contactName2 FROM officeLocations LIMIT 1";
$result = mysql_query($query);
if($result){
	$hasCName2 = TRUE;
}

$query = "SELECT contactEmail2 FROM officeLocations LIMIT 1";
$result = mysql_query($query);
if($result){
	$hasCEmail2 = TRUE;
}

$query = "SELECT img FROM officeLocations LIMIT 1";
$result = mysql_query($query);
if($result){
	$hasImage = TRUE;
}

$query = "SELECT description FROM officeLocations LIMIT 1";
$result = mysql_query($query);
if($result){
	$hasDescription = TRUE;
}

$query = "SELECT googleMap FROM officeLocations LIMIT 1";
$result = mysql_query($query);
if($result){
	$hasGoogleMap = TRUE;
}

$query = "SELECT directions FROM officeLocations LIMIT 1";
$result = mysql_query($query);
if($result){
	$hasDirections = TRUE;
}

if($hasCategories){
	function get_categories(){
		$query = "SELECT name, id FROM officeCategories ORDER BY name";
		$result = mysql_query($query);
		$array = array();
		while($record = mysql_fetch_assoc($result)){
			$array[$record['id']] = $record['name'];
		}
		return $array;
	}
	$categories = get_categories();
}

function delete_image($id,$dir){
	//check to see if current image exists
	$q = "SELECT img FROM officeLocations WHERE id='$id'";
	$res = mysql_query($q);
	$rec = mysql_fetch_assoc($res);
	if($rec['img'] != ''){
		if(!unlink($dir.$rec['img'])){
			//$error .= 'There was an error deleting the current image.<br />';
			return FALSE;
		}else{
			return TRUE;
		}
	}
}

ini_set('upload_max_filesize', 6000000);

//define dir for images
$dir = '../images/offices/';   // Path To Images Directory

if(file_exists($dir)){
	$hasImage = TRUE;
	$image = $_FILES['image'];
	$imageName = clean($image['name']);
}

if($_POST['action'] == 'process'){
	$id = clean($_POST['id']);
	$link = clean($_POST['link']);
	$name = clean($_POST['name']);
	$address = clean($_POST['address']);
	$address2 = clean($_POST['address2']);
	$phone = clean($_POST['phone']);
	$fax = clean($_POST['fax']);
	$contactName = clean($_POST['contactName']);
	$contactEmail = clean($_POST['contactEmail']);
	$contactName2 = clean($_POST['contactName2']);
	$contactEmail2 = clean($_POST['contactEmail2']);
	$description = clean(stripslashes($_POST['description']));
	$googleMap = clean($_POST['googleMap']);
	$directions = clean($_POST['directions']);
	$category = clean($_POST['category']);
	$active = clean($_POST['active']);
	
	$passCheck = TRUE;
	if($name == ''){ $passCheck = FALSE; $error = 'Name cannot be left blank.<br />'; }
	if($hasCategories){
		if($category == '' || !is_numeric($category)){ $passCheck = FALSE; $error = 'Invalid Category selection.<br />'; }
	}
	
	if($passCheck){
		$vals = "name='$name', active='$active', address='$address', address2='$address2', phone='$phone', fax='$fax', link='$link'";
		
		if($hasCName){
			$vals .= ", contactName='$contactName'";
		}
		if($hasCEmail){
			$vals .= ", contactEmail='$contactEmail'";
		}
		if($hasCName2){
			$vals .= ", contactName2='$contactName2'";
		}
		if($hasCEmail2){
			$vals .= ", contactEmail2='$contactEmail2'";
		}
		if($hasCategories){
			$vals .= ", category='$category'";
		}
		if($hasDescription){
			$vals .= ", description='$description'";
		}
		if($hasGoogleMap){
			$vals .= ", googleMap='$googleMap'";
		}
		if($hasDirections){
			$vals .= ", directions='$directions'";
		}
		if($hasImage){
			
			if($imageName != ''){
				if(file_upload($image,$dir)){
					$vals .= ", img='$imageName'";
					$msg .= 'Image successfully uploaded<br />';
					$deleteImage = TRUE;
				}else{
					$error .= 'The image didn\'t upload correctly<br />';
				}
				if($deleteImage){
					delete_image($id,$dir);
				}
			}
			
		}
		
		if($id == ''){
			$query = "INSERT INTO officeLocations SET $vals";
		}else{
			$query = "UPDATE officeLocations SET $vals WHERE id='$id'";
		}
		if(mysql_query($query)){
			$msg .= 'Information updated successfully.<br />';
			unset($_POST);
			if($hasCategories){
			$categories = get_categories();
			}
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
			
			$updatedOfficeID = $_POST['sortorder'][$i];
			if($updatedOfficeID!= ''){
				$newOrderNum = $i*10;
				$query = "UPDATE officeLocations SET sortOrder='$newOrderNum' WHERE id='".$_POST['sortorder'][$i]."' AND category = '".$_POST['catsort']."'";
				
				if(mysql_query($query)){
					$success = TRUE;
					$msg = 'Offices have been arranged in the order specified.';
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
			
			$updatedOfficeID = $_POST['sortorder'][$i];
			if($updatedOfficeID!= ''){
				$newOrderNum = $i*10;
				$query = "UPDATE officeLocations SET sortOrder='$newOrderNum' WHERE id='".$_POST['sortorder'][$i]."' ";
				
				if(mysql_query($query)){
					$success = TRUE;
					$msg = 'Offices have been arranged in the order specified.';
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
			$query = "INSERT INTO officeCategories SET $vals";
		}else{
			$query = "UPDATE officeCategories SET $vals WHERE id='$id'";
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
		$query = "UPDATE officeCategories SET active='0' WHERE id='$id'";
		
		if(mysql_query($query)){
			$msg .= 'Information deleted successfully';
			unset($_POST);
		}else{
			$error .= 'There was an error processing your query. '.mysql_error().' '.$query.'<br />';
		}
	}elseif($item == 'office'){
		$query = "UPDATE officeLocations SET active='0' WHERE id='$id'";
		
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
		$query = "UPDATE officeCategories SET active='1' WHERE id='$id'";
		
		if(mysql_query($query)){
			$msg .= 'Information deleted successfully';
			unset($_POST);
		}else{
			$error .= 'There was an error processing your query. '.mysql_error().' '.$query.'<br />';
		}
	}elseif($item == 'office'){
		$query = "UPDATE officeLocations SET active='1' WHERE id='$id'";
		
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
		$query = "DELETE FROM officeCategories WHERE id='$id'";
		$query2 = "DELETE FROM officeLocations WHERE category='$id'";
		
		$q = "SELECT id FROM officeLocations WHERE category = '$id'";
		$res = mysql_query($q);
		while($rec = mysql_fetch_assoc($res)){
			delete_image($rec['id'],$dir);
		}
		
		if(mysql_query($query) && mysql_query($query2)){
			$msg .= 'Information deleted successfully';
		}else{
			$error .= 'There was an error processing your query. '.mysql_error().' '.$query.'<br />';
		}
	}elseif($item == 'office'){
		$query = "DELETE FROM officeLocations WHERE id='$id'";
		delete_image($id,$dir);
		
		if(mysql_query($query)){
			$msg .= 'Information deleted successfully';
		}else{
			$error .= 'There was an error processing your query. '.mysql_error().' '.$query.'<br />';
		}
	}
}
?>