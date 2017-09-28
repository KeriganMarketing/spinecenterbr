<?php
$allowed = TRUE;
mysql_close($database);
$database = mysql_connect($host,$user,$pass);
if(!mysql_select_db($db,$database)){
	$allowed = FALSE;
}
$pageBase = '/?cmd=clients';

$query = "SELECT id FROM clientCategories";
$result = mysql_query($query);
if($result){
	$hasCategories = TRUE;
}

$query = "SELECT projectTitle FROM clients";
$result = mysql_query($query);
if($result){
	$hasTitle = TRUE;
}

$query = "SELECT projectCost FROM clients";
$result = mysql_query($query);
if($result){
	$hasCost = TRUE;
}

$query = "SELECT pdf FROM clients";
$result = mysql_query($query);
if($result){
	$hasPDF = TRUE;
}

$query = "SELECT location FROM clients";
$result = mysql_query($query);
if($result){
	$hasLocation = TRUE;
}

$query = "SELECT timeframe FROM clients";
$result = mysql_query($query);
if($result){
	$hasTime = TRUE;
}

$query = "SELECT client FROM clients";
$result = mysql_query($query);
if($result){
	$hasName = TRUE;
}

if($hasCategories){
	function get_categories(){
		$query = "SELECT name, id FROM clientCategories ORDER BY name";
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
$dir = '../../../images/clients/';   // Path To Images Directory
$dir2 = '../../../images/clients/thumbs/';   // Path To Thumbs Directory
$dir3 = '../../../images/clients/pdf/';   // Path To pdf Directory

function delete_image($id,$dir){
	//check to see if current image exists
	$q = "SELECT image FROM clients WHERE id='$id'";
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
function delete_thumb($id,$dir2){
	//check to see if current image exists
	$q = "SELECT image FROM clients WHERE id='$id'";
	$res = mysql_query($q);
	$rec = mysql_fetch_assoc($res);
	if($rec['thumbnail'] != ''){
		if(!unlink($dir2.$rec['thumbnail'])){
			//$error .= 'There was an error deleting the current image.<br />';
			return FALSE;
		}else{
			return TRUE;
		}
	}
}
function delete_pdf($id,$dir3){
	//check to see if current image exists
	$q = "SELECT pdf FROM clients WHERE id='$id'";
	$res = mysql_query($q);
	$rec = mysql_fetch_assoc($res);
	if($rec['pdf'] != ''){
		if(!unlink($dir3.$rec['pdf'])){
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

if(file_exists($dir2)){
	$hasThumbnail = TRUE;
	$thumbnail = $_FILES['thumb'];
	$thumbName = clean($thumbnail['name']);
}

if(file_exists($dir3)){
	$hasPDF = TRUE;
	$pdf = $_FILES['pdf'];
	$pdfName = clean($pdf['name']);
}

if($_POST['action'] == 'process'){
	$id = clean($_POST['id']);
	$url = clean($_POST['url']);
	$name = clean($_POST['name']);
	$info = clean(stripslashes($_POST['info']));
	$active = clean($_POST['active']);
	$category = clean($_POST['category']);
	$projectTitle = clean($_POST['projectTitle']);
	$projectCost = clean($_POST['projectCost']);
	$client = clean($_POST['client']);
	$location = clean($_POST['location']);
	$timeframe = clean($_POST['timeframe']);
	
	$passCheck = TRUE;
	if($name == ''){ $passCheck = FALSE; $error = 'Name cannot be left blank.<br />'; }
	if($hasCategories){
		if($category == '' || !is_numeric($category)){ $passCheck = FALSE; $error = 'Invalid Category selection.<br />'; }
	}
	
	if($passCheck){
		$vals = "name='$name', url='$url', category='$category', active='$active', info='$info'";
		
		if($hasTitle){
			$vals .= ", projectTitle='$projectTitle'";
		}
		if($hasName){
			$vals .= ", client='$client'";
		}
		if($hasLocation){
			$vals .= ", location='$location'";
		}
		if($hasTime){
			$vals .= ", timeframe='$timeframe'";
		}
		if($hasCost){
			$vals .= ", projectCost='$projectCost'";
		}
		if($hasImage){
			
			if($imageName != ''){
				if(file_upload($image,$dir)){
					$vals .= ", image='$imageName'";
					$msg .= 'Image successfully uploaded<br />';
					$deleteImage = TRUE;
				}else{
					$error .= 'The image didn\'t upload correctly<br />';
				}
				if($deleteImage){
					delete_image($id,$dir);
				}
			}
			if($thumbName != ''){
				if(file_upload($thumbnail,$dir2)){
					$vals .= ", thumbnail='$thumbName'";
					$msg .= 'Thumbnail successfully uploaded<br />';
					$deleteThumb = TRUE;
				}else{
					$error .= 'The image didn\'t upload correctly<br />';
				}
				if($deleteThumb){
					delete_thumb($id,$dir2);
				}
			}
		}
		if($hasPDF){
			
			if($pdfName != ''){
				if(file_upload($pdf,$dir3)){
					$vals .= ", pdf='$pdfName'";
					$msg .= 'PDF successfully uploaded<br />';
					$deletePDF = TRUE;
				}else{
					$error .= 'The PDF didn\'t upload correctly<br />';
				}
				if($deletePDF){
					delete_pdf($id,$dir3);
				}
			}
		}
		
		if($id == ''){
			$query = "INSERT INTO clients SET $vals";
		}else{
			$query = "UPDATE clients SET $vals WHERE id='$id'";
		}
		if(mysql_query($query)){
			$msg .= 'Information updated successfully.<br />';
			unset($_POST);
			//reget campains
			$categories = get_categories();
		}else{
			$error .= 'There was an error processing your query. '.mysql_error().'<br />';
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
				$query = "UPDATE clients SET sortOrder='$newOrderNum' WHERE id=".$_POST['sortorder'][$i]." AND category = '".$_POST['catsort']."'";
				
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
			
			$updatedClientID = $_POST['sortorder'][$i];
			if($updatedClientID!= ''){
				$newOrderNum = $i*10;
				$query = "UPDATE clients SET sortOrder='$newOrderNum' WHERE id=".$_POST['sortorder'][$i]." ";
				
				if(mysql_query($query)){
					$success = TRUE;
					$msg = 'Photos have been arranged in the order specified.';
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
			$query = "INSERT INTO clientCategories SET $vals";
		}else{
			$query = "UPDATE clientCategories SET $vals WHERE id='$id'";
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
		$query = "UPDATE clientCategories SET active='0' WHERE id='$id'";
		
		if(mysql_query($query)){
			$msg .= 'Information deleted successfully';
			unset($_POST);
		}else{
			$error .= 'There was an error processing your query. '.mysql_error().' '.$query.'<br />';
		}
	}elseif($item == 'client'){
		$query = "UPDATE clients SET active='0' WHERE id='$id'";
		
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
		$query = "UPDATE clientCategories SET active='1' WHERE id='$id'";
		
		if(mysql_query($query)){
			$msg .= 'Information deleted successfully';
			unset($_POST);
		}else{
			$error .= 'There was an error processing your query. '.mysql_error().' '.$query.'<br />';
		}
	}elseif($item == 'client'){
		$query = "UPDATE clients SET active='1' WHERE id='$id'";
		
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
		$query = "DELETE FROM clientCategories WHERE id='$id'";
		$query2 = "DELETE FROM clients WHERE category='$id'";
		
		$q = "SELECT id FROM clients WHERE category = '$id'";
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
		$query = "DELETE FROM clients WHERE id='$id'";
		delete_image($id,$dir);
		
		if(mysql_query($query)){
			$msg .= 'Information deleted successfully';
		}else{
			$error .= 'There was an error processing your query. '.mysql_error().' '.$query.'<br />';
		}
	}
}
?>