<?php
$modUpdate = '04/24/14';
$allowed = TRUE;
mysql_close($database);
$database = mysql_connect($host,$user,$pass);
if(!mysql_select_db($db,$database)){
	$allowed = FALSE;
}
$pageBase = '/?cmd=blog';
if(isset($_GET['cat'])){ $pageBase .= '&cat='.strip_tags($_GET['cat']); }

$query = "SELECT id FROM blogCategories";
$result = mysql_query($query);
if($result){
	$hasCategories = TRUE;
}
$query = "SELECT id, replyID FROM blogResponses";
$result = mysql_query($query);
if($result){
	$hasComments = TRUE;
}

$query = "SELECT img FROM blogPosts";
$result = mysql_query($query);
if($result){
	$hasImg = TRUE;
}

$query = "SELECT vid FROM blogPosts";
$result = mysql_query($query);
if($result){
	$hasVid = TRUE;
}

$query = "SELECT role FROM blogPosts";
$result = mysql_query($query);
if($result){
	$hasRole = TRUE;
}

$query = "SELECT metaDescription FROM blogPosts";
$result = mysql_query($query);
if($result){
	$hasMeta = TRUE;
}

$query = "SELECT titleTag FROM blogPosts";
$result = mysql_query($query);
if($result){
	$hasTitleTag = TRUE;
}

$query = "SELECT previewEnabled FROM blogPosts";
$result = mysql_query($query);
if($result){
	$hasPreview = TRUE;
}

//ini_set('display_errors', 'On');
ini_set('upload_max_filesize', 50331648);
ini_set('php_value post_max_size', 50331648);
ini_set('php_value session.gc_maxlifetime', 10800);
ini_set('php_value max_input_time', 10800);
ini_set('php_value max_execution_time', 10800);

$dir = '../images/blog/';
$image = $_FILES['image'];
//print_r($_FILES['file']);

//categories function
function get_categories(){
	$query = "SELECT name, id FROM blogCategories ORDER BY name";
	$result = mysql_query($query);
	$array = array();
	while($record = mysql_fetch_assoc($result)){
		$array[$record['id']] = $record['name'];
	}
	return $array;
}

if($hasCategories){
	$categories = get_categories();
}

////////edit a post
if($_POST['action'] == 'process'){
	$rand = rand();
	$id = clean($_POST['id']);
	$title = clean($_POST['title']);
	$controller = clean($_POST['controller']);
	$date = clean(server_date($_POST['date']));
	$author = clean($_POST['author']);
	$active = clean($_POST['active']);
	$email = clean($_POST['email']);
	$category = clean($_POST['category']);
	$content = clean(stripslashes($_POST['pageContent']));
	$imagename = clean($image['name']);
	$vid = clean($_POST['vid']);
	$save = clean($_POST['save']);
	$publish = clean($_POST['publish']);
	$role = clean($_POST['role']);
	$metadescription = clean($_POST['metadescription']);
	$titletag = clean($_POST['titletag']);
	
	if($save){
		$active = '0';
	}elseif($publish){
		$active = '1';	
	}
	
	if($controller == 'auto'){
		$controller = strtolower($title);
		$controller = str_replace(' ', '-', $controller);
		$controller = str_replace(',', '', $controller);
		$controller = str_replace('&', '-', $controller);
		$controller = str_replace('|', '-', $controller);
	}
		
		
	if($imagename != ''){
		
		$filecheck = basename($image['name']);
		$ext = strtolower(substr($filecheck, strrpos($filecheck, '.') + 1));

		if (!(($ext == 'jpg' || $ext == 'gif' || $ext == 'png'))){
			$passCheck = FALSE;
			$error .= 'The image must me a jpg, gif or png.<br />';
		}
		
		$nameArray = array();
		$nameQuery = "SELECT image FROM blogPosts";
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
	
	$passCheck = TRUE;
	if($title == ''){ $passCheck = FALSE; $error = 'Name cannot be left blank.<br />'; }
	if($controller == ''){ $passCheck = FALSE; $error = 'Controller cannot be left blank.<br />'; }
	//if($author == ''){ $passCheck = FALSE; $error = 'Author cannot be left blank.<br />'; }
	//if($email == ''){ $passCheck = FALSE; $error = 'Email cannot be left blank.<br />'; }
	if($hasCategories){
		if($category == '' || !is_numeric($category)){ $passCheck = FALSE; $error = 'Invalid Category selection.<br />'; }
	}
	
	
	if($passCheck){
		$vals = "title='$title', controller='$controller', date='$date', author='$author', active='$active', email='$email', content='$content', hash=md5($rand)";
		if($hasCategories){
			$vals .= ", category='$category'";
		}
		if($hasMeta){
			$vals .= ", metaDescription='$metadescription'";
		}
		if($hasTitleTag){
			$vals .= ", titleTag='$titletag'";
		}
		if($hasVid){
			if($vid != ''){
				$vals .= ", vid='$vid'";
			}
		}
		if($hasRole){
			if($role != ''){
				$vals .= ", role='$role'";
			}
		}
		if($imagename != '' && $passCheck){
			// Uploading/Resizing Script
			if(file_upload($image,$dir)){
				$msg .= 'Image successfully uploaded<br />';
				$vals .= ", img='$imagename'";
			}else{
				$error .= 'The image didn\'t upload correctly<br />';
				$passCheck = FALSE;
			}
		}
		
		if($id == ''){
			$query = "INSERT INTO blogPosts SET $vals";

		}else{
			$query = "UPDATE blogPosts SET $vals WHERE id='$id'";
		}
		if(mysql_query($query)){
			
			$msg .= 'Information updated successfully.<br />';
			if($id == ''){
				$id = mysql_insert_id();
				header('Location: /?cmd=blog&action=edit&id='.$id);
			}else{
				$_GET['id'] = $id;
				$_GET['action'] = 'edit';
			}
			
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
	
////////edit a comment
}elseif($_POST['action'] == 'processcomment'){
	//print 'you edited a comment!';
	$id = clean($_POST['id']);
	$name = clean($_POST['name']);
	$active = clean($_POST['active']);
	$email = clean($_POST['email']);
	$message = clean($_POST['message']);
	
	$passCheck = TRUE;
	
	if($name == ''){ $passCheck = FALSE; $error = 'Name cannot be left blank.<br />'; }
	if($message == ''){ $passCheck = FALSE; $error = 'Message cannot be left blank.<br />'; }
	if($email == ''){ $passCheck = FALSE; $error = 'Email cannot be left blank.<br />'; }
	
	if($passCheck){
		$vals = "name='$name', approved='$active', email='$email', content='$message'";
		
		$query = "UPDATE blogResponses SET $vals WHERE id='$id'";

		if(mysql_query($query)){
			$msg .= 'Comment updated successfully.<br />';
			unset($_POST);

		}else{
			$error .= 'There was an error processing your query. '.mysql_error().' '.$query.'<br />';
		}
	}
	
	
////////edit a category	
}elseif($_POST['action'] == 'category'){
	$id = clean($_POST['id']);
	$name = clean($_POST['cat_name']);
	
	$passCheck = TRUE;
	if($name == ''){ $passCheck = FALSE; $error .= 'Name cannot be left blank.<br />'; }
	
	if($passCheck){
		$vals = "name='$name'";
		if($id == ''){
			$query = "INSERT INTO blogCategories SET $vals";
		}else{
			$query = "UPDATE blogCategories SET $vals WHERE id='$id'";
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


////////post actions
}elseif($_GET['action'] == 'disable' && $_GET['item'] == 'post' && $_GET['id'] != '' && is_numeric($_GET['id'])){
	$id = clean($_GET['id']);
	
	$query = "UPDATE blogPosts SET active='0' WHERE id='$id'";
	
	if(mysql_query($query)){
		$msg .= 'Information updated successfully';
		//unset($_POST);
	}else{
		$error .= 'There was an error processing your query. '.mysql_error().' '.$query.'<br />';
	}
}elseif($_GET['action'] == 'enable' && $_GET['item'] == 'post' && $_GET['id'] != '' && is_numeric($_GET['id'])){
	$id = clean($_GET['id']);

	$query = "UPDATE blogPosts SET active='1' WHERE id='$id'";
	
	if(mysql_query($query)){
		$msg .= 'Information updated successfully';
	}else{
		$error .= 'There was an error processing your query. '.mysql_error().' '.$query.'<br />';
	}
}elseif($_GET['action'] == 'delete' && $_GET['item'] == 'post' && $_GET['id'] != '' && is_numeric($_GET['id'])){
	$id = clean($_GET['id']);

	$query = "DELETE FROM blogPosts WHERE id='$id'";
	
	//get current filename
	$fileQ = "SELECT img FROM blogPosts WHERE id='$id' LIMIT 1";
	$fileRes = mysql_query($fileQ);
	$fileRec = mysql_fetch_assoc($fileRes);

	if($fileRec['image'] != ''){
		if(unlink($dir.$fileRec['image'])){
			$msg.='Image Deleted successfully<br />';
		}else{
			$error .= 'There was an error deleting the current image';
		}
	}
	
	if(mysql_query($query)){
		$msg .= 'Information deleted successfully';
	}else{
		$error .= 'There was an error processing your query. '.mysql_error().' '.$query.'<br />';
	}
	
	
////////comment actions	
}elseif($_GET['action'] == 'disable' && $_GET['item'] == 'comment' && $_GET['id'] != '' && is_numeric($_GET['id'])){
	$id = clean($_GET['id']);
	
	$query = "UPDATE blogResponses SET approved='0' WHERE id='$id'";
	
	if(mysql_query($query)){
		$msg .= 'Comment has been disabled';
	}else{
		$error .= 'There was an error processing your query. '.mysql_error().' '.$query.'<br />';
	}
}elseif($_GET['action'] == 'enable' && $_GET['item'] == 'comment' && $_GET['id'] != '' && is_numeric($_GET['id'])){
	$id = clean($_GET['id']);

	$query = "UPDATE blogResponses SET approved='1' WHERE id='$id'";
	
	if(mysql_query($query)){
		$msg .= 'Comment has been enabled';
	}else{
		$error .= 'There was an error processing your query. '.mysql_error().' '.$query.'<br />';
	}
}elseif($_GET['action'] == 'delete' && $_GET['item'] == 'comment' && $_GET['id'] != '' && is_numeric($_GET['id'])){
	$id = clean($_GET['id']);

	$query = "DELETE FROM blogResponses WHERE id='$id'";
	
	if(mysql_query($query)){
		$msg .= 'Comment deleted successfully';
	}else{
		$error .= 'There was an error processing your query. '.mysql_error().' '.$query.'<br />';
	}
	
}elseif($_GET['action'] == 'deleteImage' && is_numeric($_GET['id'])){
	$id = clean($_GET['id']);
	$show = 'edit';
	//get current filename
	$fileQ = "SELECT img FROM blogPosts WHERE id='$id' LIMIT 1";
	$fileRes = mysql_query($fileQ);
	$fileRec = mysql_fetch_assoc($fileRes);
	
	if(unlink($dir.$fileRec['img'])){
		$query = "UPDATE blogPosts SET img='' WHERE id='$id'";
		$msg.='File Deleted successfully<br />';
	}else{
		$error .= 'There was an error deleting the current file';
	}
	if(!$result = mysql_query($query)){
		$error .= 'Delete Query failed. '.mysql_query;
	}
}elseif($_GET['action'] == 'deleteVideo' && is_numeric($_GET['id'])){
	$id = clean($_GET['id']);
	$show = 'edit';
	//get current filename
	$fileQ = "SELECT vid FROM blogPosts WHERE id='$id' LIMIT 1";
	$fileRes = mysql_query($fileQ);
	$fileRec = mysql_fetch_assoc($fileRes);
	
	$query = "UPDATE blogPosts SET vid='' WHERE id='$id'";
		
	if(!$result = mysql_query($query)){
		$error .= 'Delete Query failed. '.mysql_query;
	}else{
		$msg.='Video Removed successfully<br />';
	}

/////////////reply actions	
}elseif($_POST['action'] == 'processreply'){
	//print 'you replied to a comment!';
	$commentId = clean($_POST['commentid']);
	$articleId = clean($_POST['articleid']);
	$name = clean($_POST['replyname']);
	$active = clean($_POST['replyactive']);
	$email = clean($_POST['replyemail']);
	$message = clean($_POST['replycomment']);
	$date = clean($_POST['date']);
	
	$passCheck = TRUE;
	
	if($name == ''){ $passCheck = FALSE; $error = 'Name cannot be left blank.<br />'; }
	if($message == ''){ $passCheck = FALSE; $error = 'Message cannot be left blank.<br />'; }
	if($email == ''){ $passCheck = FALSE; $error = 'Email cannot be left blank.<br />'; }
	
	if($passCheck){
		$vals = "name='$name', postID='$articleId', replyID='$commentId', approved='$active', email='$email', content='$message', date='$date'";
		
		$query = "INSERT INTO blogResponses SET $vals";

		if(mysql_query($query)){
			$msg .= 'Comment updated successfully.<br />';
			unset($_POST);

		}else{
			$error .= 'There was an error processing your query. '.mysql_error().' '.$query.'<br />';
		}
	}
			
////////category actions
}elseif($_GET['action'] == 'deleteCategory' && is_numeric($_GET['id'])){
	
	//define
	$id = mysql_real_escape_string($_GET['id']);
	
	//define querys
	$deleteQuery = "DELETE FROM blogCategories WHERE id='$id'";
	
	if(!(mysql_query($deleteQuery, $database))){
		$error .= "There was an error removing this photo. ".mysql_error().'<br />';
	}else{
		$msg .= 'The category was successfully removed.<br />';
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
		$updateQuery = "UPDATE blogCategories SET ".$vars." WHERE id='$id'";
	}else{//add new photo
		$updateQuery = "INSERT INTO blogCategories SET ".$vars;
	}
	if($passCheck){
		if(!(mysql_query($updateQuery, $database))){
			$passCheck = FALSE;
			$error .= 'Query failed, '. mysql_error();
			$_GET['action'] == 'categories';
			$_GET['id'] == $ID;
		}else{
			$msg .= "Update successful";
			unset($_POST);
		}
	}
}
?>