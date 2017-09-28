<?php
$modUpdate = '07/22/14';
$allowed = TRUE;
mysql_close($database);
$database = mysql_connect($host,$user,$pass);
if(!mysql_select_db($db,$database)){
	$allowed = FALSE;
}

ini_set('upload_max_filesize', 6000000);
$pageName = "/?cmd=homeSlider";
$dir = '../images/slider/';   // Path To Images Directory

$file = $_FILES['imagefile'];
//print_r($file);

// GET SITE COMPATIBILITY
$linkquery = "SHOW COLUMNS FROM home_slideshow LIKE 'link'";
$linkresult = mysql_query($linkquery);
$linkrecord = mysql_fetch_assoc($linkresult);
if(mysql_num_rows($linkresult)>0){ $hasLink = TRUE; }else{ $hasLink = FALSE; }

$linktextquery = "SHOW COLUMNS FROM home_slideshow LIKE 'linktext'";
$linktextresult = mysql_query($linktextquery);
$linktextrecord = mysql_fetch_assoc($linktextresult);
if(mysql_num_rows($linktextresult)>0){ $hasLinkText = TRUE; }else{ $hasLinkText = FALSE; }

$copyquery = "SHOW COLUMNS FROM home_slideshow LIKE 'copy'";
$copyresult = mysql_query($copyquery);
$copyrecord = mysql_fetch_assoc($copyresult);
if(mysql_num_rows($copyresult)>0){ $hasCopy = TRUE; }else{ $hasCopy = FALSE; }

$shorttitlequery = "SHOW COLUMNS FROM home_slideshow LIKE 'shorttitle'";
$shorttitleresult = mysql_query($shorttitlequery);
$shorttitlerecord = mysql_fetch_assoc($shorttitleresult);
if(mysql_num_rows($shorttitleresult)>0){ $hasName = TRUE; $hasTitle = FALSE; }else{ $hasName = FALSE; $hasTitle = TRUE; }

if($_GET['action'] == 'delete' && is_numeric($_GET['id'])){
	
	//define
	$id = mysql_real_escape_string($_GET['id']);
	
	//define querys
	$deleteQuery = "DELETE FROM home_slideshow WHERE id='$id'";

	$currentQuery = "SELECT img FROM home_slideshow WHERE id=$id";
	$currentResult = mysql_query($currentQuery, $database);
	$currentRecord = mysql_fetch_assoc($currentResult);
	$current = $currentRecord['img'];
	
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
	$link = mysql_real_escape_string($_POST['link']);
	$linktext = mysql_real_escape_string($_POST['linktext']);
	$shorttitle = mysql_real_escape_string($_POST['shorttitle']);
	$copy = mysql_real_escape_string($_POST['copy']);
	$category = mysql_real_escape_string($_POST['category']);
	$id = mysql_real_escape_string($_POST['id']);
	
	$passCheck = TRUE;
	
	//allowable file types
	if($file['name'] != ''){
		$allowableFileTypes = array('image/jpg','image/jpeg','image/pjpeg','image/png');
		if(!in_array($file['type'],$allowableFileTypes)){
			$passCheck = FALSE;
			$error .= 'The file must be a jpg.<br />';
		}
		if($file['size'] > '6000000'){
			$passCheck = FALSE;
			$error = 'The file is too large. Please resize to less than 6MB.<br />';
		}
		
		$nameArray = array();
		$nameQuery = "SELECT img FROM home_slideshow";
		$nameResult = mysql_query($nameQuery, $database);
		while($nameRecord = mysql_fetch_array($nameResult)){
			array_push($nameArray, $nameRecord['img']);
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
	$vars = "";
	if($hasCopy){ $vars .= "copy='$copy'"; }
	
	if($url != ''){
		$vars .= ", img='$url'";
		if($id != ''){
			$deleteCurrent = TRUE;
		}
	}
	
	if($hasLink){ $vars .= ", link='$link'"; }
	if($hasTitle){ $vars .= ", title='$title'"; }
	if($hasName){ $vars .= ", shorttitle='$shorttitle'"; }
	if($hasLinkText){ $vars .= ", linktext='$linktext'"; }
	
	if($id != ''){//update a photo after uploading a new image
		$updateQuery = "UPDATE home_slideshow SET ".$vars." WHERE id='$id'";
	}elseif($file != ''){//add new photo
		$updateQuery = "INSERT INTO home_slideshow SET ".$vars.", sortOrder='1000'";
	}else{
		$error .= 'You must upload a photo.';
	}
	
	if($passCheck){
		if($deleteCurrent){
			$currentQuery = "SELECT img FROM home_slideshow WHERE id=$id";
			$currentResult = mysql_query($currentQuery, $database);
			$currentRecord = mysql_fetch_assoc($currentResult);
			$current = $currentRecord['img'];
			
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
			$_GET['id'] == $id;
		}else{
			$msg .= "Information updated successfully.<br />";
			unset($_POST);
		}
	}
}elseif($_POST['cmd'] == 'sortOrder'){
	///cleanse input
	$count = count($_POST['id']);
	$newSortOrder = $_POST['sortorder'];
	//print_r($_POST);
	$id = $_POST['id'];
	
	for ($i = 0, $l = count($_POST['sortorder']); $i < $l; $i++) {
		
		$updatedSlideID = $_POST['sortorder'][$i];
		if($updatedSlideID!= ''){
			$newOrderNum = $i*10;
			$query = "UPDATE home_slideshow SET sortOrder='$newOrderNum' WHERE id=".$_POST['sortorder'][$i]."";
			
			if(mysql_query($query)){
				$success = TRUE;
				$msg = 'Slides have been arranged in the order specified.';
				//unset($_POST);
			}else{
				$error .= 'There was an error processing your request. <br />'.mysql_error();
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
		$updateQuery = "UPDATE photoCategories SET ".$vars." WHERE id='$id'";
	}else{//add new photo
		$updateQuery = "INSERT INTO photoCategories SET ".$vars;
	}
	if($passCheck){
		if(!(mysql_query($updateQuery, $database))){
			$passCheck = FALSE;
			$error .= 'Query failed, '. mysql_error();
			$_GET['action'] == 'categories';
			$_GET['id'] == $id;
		}else{
			header("Location: /".$pageName."&success=1");
			die();
		}
	}
}
if($_GET['success'] == '1'){
	$msg .= "Information updated successfully.<br />";
}
?>