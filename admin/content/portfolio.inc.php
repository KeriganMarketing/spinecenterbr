<script type="text/javascript">
	$(function(){

		// Tabs
		$('#tabs').tabs();
		$("#datePosted").datepicker();
	});
</script>
<?php
$allowed = TRUE;
mysql_close($database);
$database = mysql_connect($host,$user,$pass);
if(!mysql_select_db($db,$database)){
	$allowed = FALSE;
}

$query = "SELECT * FROM portfolioCategories";
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
$pageName = "/?cmd=portfolio";
$dir = '../images/uploads/portfolio/';   // Path To Images Directory

$file = $_FILES['imagefile'];
//print_r($file);

if($_GET['action'] == 'delete' && is_numeric($_GET['id'])){
	
	//define
	$id = mysql_real_escape_string($_GET['id']);
	
	//define querys
	$deleteQuery = "DELETE FROM portfolio WHERE id='$id'";

	$currentQuery = "SELECT img FROM portfolio WHERE id=$id";
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
	$deleteQuery = "DELETE FROM portfolioCategories WHERE id='$id'";
	
	if(!(mysql_query($deleteQuery, $database))){
		$error .= "There was an error removing this photo. ".mysql_error().'<br />';
	}else{
		$msg .= 'The category was successfully removed.<br />';
	}
	
}elseif($_POST['cmd'] == 'editAction'){
	//define variables
	$name = mysql_real_escape_string($_POST['name']);
	$caption = mysql_real_escape_string($_POST['description']);
	$category = mysql_real_escape_string($_POST['category']);
	$id = mysql_real_escape_string($_POST['id']);
	
	$passCheck = TRUE;
	
	//allowable file types
	if($file['name'] != ''){
		$allowableFileTypes = array('image/jpg','image/jpeg','image/pjpeg','image/png','image/JPG','image/JPEG');
		if(!in_array($file['type'],$allowableFileTypes)){
			$passCheck = FALSE;
			$error .= 'The file must be a jpg or png.<br />';
		}
		if($file['size'] > '6000000'){
			$passCheck = FALSE;
			$error = 'The file is too large. Please resize to less than 6MB.<br />';
		}
		
		$nameArray = array();
		$nameQuery = "SELECT img FROM portfolio";
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
	$vars = "name='$name', description='$description', link='$link'";
	if($hasCategories){
		$vars .= ", category='$category'";
	}
	if($url != ''){
		$vars .= ", img='$url'";
		if($id != ''){
			$deleteCurrent = TRUE;
		}
	}
	
	if($id != ''){//update a photo after uploading a new image
		$updateQuery = "UPDATE portfolio SET ".$vars." WHERE id='$id'";
	}elseif($file != ''){//add new photo
		$updateQuery = "INSERT INTO portfolio SET ".$vars.", sortOrder='1000'";
	}else{
		$error .= 'You must upload a photo.';
	}
	
	if($passCheck){
		if($deleteCurrent){
			$currentQuery = "SELECT img FROM portfolio WHERE id=$id";
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
				$query = "UPDATE portfolio SET sortOrder='$newOrderNum' WHERE id=".$_POST['sortorder'][$i]." AND category = '".$_POST['catsort']."'";
				
				if(mysql_query($query)){
					$success = TRUE;
					$msg = 'portfolio have been arranged in the order specified.';
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
				$query = "UPDATE portfolio SET sortOrder='$newOrderNum' WHERE id=".$_POST['sortorder'][$i]." ";
				
				if(mysql_query($query)){
					$success = TRUE;
					$msg = 'portfolio have been arranged in the order specified.';
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
		$updateQuery = "UPDATE portfolioCategories SET ".$vars." WHERE id='$id'";
	}else{//add new photo
		$updateQuery = "INSERT INTO portfolioCategories SET ".$vars;
	}
	if($passCheck){
		if(!(mysql_query($updateQuery, $database))){
			$passCheck = FALSE;
			$error .= 'Query failed, '. mysql_error();
			$_GET['action'] == 'categories';
			$_GET['id'] == $id;
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