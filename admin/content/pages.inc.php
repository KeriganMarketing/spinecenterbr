<?php
$modUpdate = '07/22/14';
$allowed = TRUE;
mysql_close($database);
$database = mysql_connect($host,$user,$pass);
if(!mysql_select_db($db,$database)){
	$allowed = FALSE;
}
if($hasPages == FALSE){
	header("Location: /?cmd=".$cmsPages[0]);
}

$query = "SELECT headline FROM pageTable";
$result = mysql_query($query);
if($result){
	$hasHeadline = TRUE;
}

$query2 = "SELECT vanTitle FROM pageTable";
$result2 = mysql_query($query2);
if($result2){
	$hasVanTitle = TRUE;
}

$query3 = "SELECT id FROM pageArchive WHERE id='$id'";
$result3 = mysql_query($query3);
if($result3){
	$archives = TRUE;
}

$query4 = "SELECT featuredImage FROM pageTable";
$result4 = mysql_query($query4);
if($result4){
	$hasImg = TRUE;
}

$query5 = "SELECT postedon FROM pageTable";
$result5 = mysql_query($query5);
if($result5){
	$hasPostedDates = TRUE;
}


//ini_set('display_errors', 'On');
ini_set('upload_max_filesize', 50331648);
ini_set('php_value post_max_size', 50331648);
ini_set('php_value session.gc_maxlifetime', 10800);
ini_set('php_value max_input_time', 10800);
ini_set('php_value max_execution_time', 10800);

$dir = '../images/uploads/';
$image = $_FILES['image'];


//define page content
if($_GET['action'] == 'navOrder'){
	$body = 'navOrder';
}
if($_GET['action'] == 'archive'){
	$body = 'archive';
}

//They're updating their navigation order
if($_POST['action'] == 'navOrder' ){
	
	//cleanse input
	$count = count($_POST['id']);
	
	//print_r($_POST);
	$tnavOrder = $_POST['navOrder'];
	$snavOrder = $_POST['subNavOrder'];
	
	$id = $_POST['id'];
	
	for ($i = 0, $l = count($_POST['navOrder']); $i < $l; $i++) {
		
		$updatedPageID = $_POST['navOrder'][$i];
		if($updatedPageID!= ''){
			$newOrderNum = $i*10;
			$tquery = "UPDATE pageTable SET navOrder='$newOrderNum' WHERE pageID='".$_POST['navOrder'][$i]."'";
			
			if(mysql_query($tquery)){
				$success = TRUE;
				$msg = 'Page '.$_POST['navOrder'][$i].' has been arranged in the order specified.';
				//unset($_POST);
			}else{
				$error .= 'There was an error processing your request. <br />'.mysql_error();
			}
		}
	}
		
	for ($j = 0, $k = count($_POST['subNavOrder']); $j < $k; $j++) {
		$supdatedPageID = $_POST['subNavOrder'][$i];
		if($supdatedPageID!= ''){
			$snewOrderNum = $j*10;
			$squery = "UPDATE pageTable SET navOrder='$snewOrderNum' WHERE pageID='".$_POST['subNavOrder'][$j]."'";
			
			if(mysql_query($squery)){
				$success = TRUE;
				$msg = 'Page '.$_POST['subNavOrder'][$j].' has been arranged in the order specified.';
				//unset($_POST);
			}else{
				$error .= 'There was an error processing your request. <br />'.mysql_error();
			}
		}
		
	}
//print $tquery;
//print $squery;
	/*while($i < $count){
		$newNavOrder = clean($navOrder[$i]);
		$updatedPageID = clean($id[$i]);
		print_r($newNavOrder);
		
		$query = "UPDATE pageTable SET navOrder=$newNavOrder WHERE pageID=$updatedPageID";
		if(mysql_query($query)){
			$success = TRUE;
			$msg = 'The Information has been submitted successfully.';
			unset($_POST);
		}else{
			$error .= 'There was an error processing your request. <br />'.mysql_error();
		}
		$i++;
//		print $i;
		
	}*/
}

if($_POST['action'] == 'revert'){
	$id = clean($_POST['id']); //id of item
	//get content from archive
	$query = "SELECT pageID, content FROM pageArchive WHERE id='$id' LIMIT 1";
	$result = mysql_query($query);
	$record = mysql_fetch_assoc($result);
	$pageID = $record['pageID'];
	$content = $record['content'];
	
	$query = "UPDATE pageTable SET pageContent='$content' WHERE pageID='$pageID'";
	if(mysql_query($query)){
		$msg = 'Page content reverted successfully<br />';
	}else{
		$error = 'There was a problem processing this request.<br /><!--'.mysql_error().'-->';
	}
	
}

if($_POST['action'] == 'page'){
	$id = clean($_POST['id']);
	if($_POST['cmd'] == 'delete'){
		$query = "DELETE FROM pageTable WHERE pageID='$id'";
		if(mysql_query($query)){
			$success = TRUE;
			$msg = 'The Information has been deleted successfully.';
			unset($_POST);
			//check for sub-pages
			$query = "SELECT pageID FROM pageTable WHERE parent='$id'";
			$result = mysql_query($query);
			while($record = mysql_fetch_assoc($result)){
				$sql = "DELETE FROM pageTable WHERE pageID='".$record['id']."'";
				if(!mysql_query($sql)){
					$error .= 'There was a problem deleting page '.$record['id'].'.<br />'."\r\n".mysql_error();
				}
			}
		}else{
			$error .= 'There was a problem processing your request. Please try again.<br />'."\r\n".mysql_error();
		}
	}else{
		$title = clean($_POST['title']);
		$controller = clean($_POST['controller']);
		$content = clean(stripslashes($_POST['pageContent']));
		$type = clean($_POST['type']);
		$parent = clean($_POST['parent']);
		$metaDesc = clean($_POST['metaDesc']);
		$metaKeys = clean($_POST['metaKeys']);
		$meta = $metaDesc.'[/split/]'.$metaKeys;
		$headers = clean($_POST['headers']);
		$searchIndex = clean($_POST['searchIndex']);
		$inNav = clean($_POST['inNav']);
		$hasUsers = clean($_POST['hasUsers']);
		$loginRequired = clean($_POST['loginRequired']);
		$archive = clean($_POST['archive']);
		$headline = clean($_POST['headline']);
		$vanTitle = clean($_POST['vanTitle']);
		//$alias = clean($_POST['alias']);
		$imagename = clean($image['name']);
		$postedon = clean($_POST['postedon']);
		
		if($controller == 'auto'){
			$controller = strtolower($title);
			$controller = str_replace(' ', '-', $controller);
			$controller = str_replace(',', '', $controller);
			$controller = str_replace('&', '-', $controller);
			$controller = str_replace('|', '-', $controller);
		}
		
		if($archive == $content){
			$archive = '';
		}
		
		if($loginRequired != ''){
			$hasUsers = $loginRequired;
		}
		
		if($imagename != ''){
			
			$filecheck = basename($image['name']);
			$ext = strtolower(substr($filecheck, strrpos($filecheck, '.') + 1));
	
			if (!(($ext == 'jpg' || $ext == 'gif' || $ext == 'png'))){
				$passCheck = FALSE;
				$error .= 'The image must me a jpg, gif or png.<br />';
			}
			
			$nameArray = array();
			$nameQuery = "SELECT featuredImage FROM pageTable";
			$nameResult = mysql_query($nameQuery, $database);
			while($nameRecord = mysql_fetch_array($nameResult)){
				array_push($nameArray,$nameRecord['featuredImage']);
			}
			if(in_array($imagename,$nameArray)){
				$fileUsed = TRUE;
			}
			if($fileUsed){
				$passCheck = FALSE;
				$error .= 'An image with that name already exists. Please change the file name before uploading.<br />';
			}
		}
		
		//don't pass checks your ass can't cash
		$passCheck = TRUE;
		if($title == ''){ $passCheck = FALSE; $error .= 'The Title cannot be left blank<br />'."\r\n"; }
		if($controller == ''){ $passCheck = FALSE; $error .= 'The controller cannot be left blank<br />'."\r\n"; }
		if(!preg_match("/^[a-z0-9_-]+$/", $controller)){$passCheck = FALSE; $error .= 'controller can only contain lowercase letters dashes and underscores<br />'."\r\n";}
		if($type == ''){ $passCheck = FALSE; $error .= 'The type cannot be left blank<br />'."\r\n"; }
		if($inNav == '' || !is_numeric($inNav)){ $passCheck = FALSE; $error .= 'The inNav value cannot be left blank and must be a number<br />'."\r\n"; }
		if($searchIndex == '' || !is_numeric($searchIndex)){ $passCheck = FALSE; $error .= 'The searchIndex value cannot be left blank and must be a number<br />'."\r\n"; }
		if($loginSystem){
			if($hasUsers == '' || !is_numeric($hasUsers)){ $passCheck = FALSE; $error .= 'The hasUsers value cannot be left blank and must be a number<br />'."\r\n"; }
		}
		
		if($passCheck){
			$vals = "title='$title', controller='$controller', pageContent='$content', pageType='$type', meta='$meta', headers='$headers', searchIndex='$searchIndex', inNav='$inNav'";
			if($parent !=''){
				$vals .= ", parent='$parent'";
			}
			if($loginSystem){
				$vals .= ", hasUsers='$hasUsers'";
			}
			if($hasHeadline){
				$vals .= ", headline='$headline'";
			}
			if($hasVanTitle){
				$vals .= ", vanTitle='$vanTitle'";
			}
			if($hasPostedDates){
				$vals .= ", postedon='$postedon'";
			}
			if($imagename != '' && $passCheck){
				// Uploading/Resizing Script
				if(file_upload($image,$dir)){
					$msg .= 'Image successfully uploaded<br />';
					$vals .= ", featuredImage='$imagename'";
				}else{
					$error .= 'The image didn\'t upload correctly<br />';
					$passCheck = FALSE;
				}
			}
			//form query
			if($id != '' && is_numeric($id)){
				$query = "UPDATE pageTable SET ".$vals." WHERE pageID='$id'";
				if($archive != ''){
					$archive_query = "INSERT INTO pageArchive SET content='$archive', pageID='$id'";
				}
			}else{
				$query = "INSERT INTO pageTable SET ".$vals;
			}
			if(mysql_query($query)){
				$success = TRUE;
				$msg .= 'The Information has been submitted successfully.';
				unset($_POST);
			}else{
				$error .= 'There was a problem processing your request. Please try again.<br />'."\r\n".mysql_error();
			}
			if(isset($archive_query)){
				$count_query = "SELECT id FROM pageArchive WHERE pageID='$id' ORDER BY time ASC";
				$count_result = mysql_query($count_query);
				$archiveIDs = array();
				while($count_record = mysql_fetch_assoc($count_result)){
					$archiveIDs[] = $count_record['id'];
				}
				$count = count($archiveIDs);
				$i=0;
				while($count > 9){
					$delete_query = "DELETE FROM pageArchive WHERE id=".$archiveIDs[$i]." LIMIT 1";
					mysql_query($delete_query);
					$count = $count-1;
					$i++;
				}
				mysql_query($archive_query);
			}
			
		}
	}
}

?>