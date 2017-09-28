<?php
$allowed = TRUE;
mysql_close($database);
$database = mysql_connect($host,$user,$pass);
if(!mysql_select_db($db,$database)){
	$allowed = FALSE;
}
$pageBase = '/?cmd=specialSections';
$dir = '../images/uploads/';   // Path To Images Directory
ini_set('upload_max_filesize', 6000000);

function delete_image($id,$dir){
	//check to see if current image exists
	$q = "SELECT content FROM specialSections WHERE ID='$id'";
	$res = mysql_query($q);
	$rec = mysql_fetch_assoc($res);
	if($rec['content'] != ''){
		if(!unlink($dir.$rec['content'])){
			//$error .= 'There was an error deleting the current image.<br />';
			return FALSE;
		}else{
			return TRUE;
		}
	}
}

if($_POST['action'] == 'process'){
	$id = clean($_POST['id']);
	$name = clean($_POST['name']);
	$content = clean(trim(stripslashes($_POST['pageContent'])));
	
	$type = clean($_POST['type']);
	$image = $_FILES['image'];
	$imageName = clean($image['name']);
	$text = clean($_POST['text']);
	$link = clean($_POST['link']['1'].'|'.$_POST['link']['2']);
	
	$passCheck = TRUE;
	if($name == ''){ $passCheck = FALSE; $error = 'Name cannot be left blank.<br />'; }
	
	if($passCheck){
			$vals = "type='$type', name='$name'";
		if($type == 'htmltext'){
			$vals .= ", content='$content'";
		}elseif($type == 'text'){
			$vals .= ", content='$text'";
		}elseif($type == 'image'){		
			if($imageName != ''){
				if(file_upload($image,$dir)){
					$vals .= ", content='$imageName'";
					$msg .= 'Image successfully uploaded<br />';
					$deleteImage = TRUE;
				}else{
					$error .= 'The image didn\'t upload correctly<br />';
				}
				if($deleteImage){
					delete_image($id,$dir);
				}
			}
			
		}elseif($type == 'link'){
			$vals .= ", content='$link'";
		}
		
		
		
		if($id == ''){
			$query = "INSERT INTO specialSections SET $vals";
		}else{
			$query = "UPDATE specialSections SET $vals WHERE ID='$id'";
		}
		if(mysql_query($query)){
			$msg .= 'Information updated successfully.<br />';
			unset($_POST);
		}else{
			$error .= 'There was an error processing your query. '.mysql_error().' '.$query.'<br />';
			if($id != ''){
				$_GET['id'] = $id;
				$_GET['action'] = 'edit';
			}
		}
	}
}elseif($_GET['action'] == 'delete' && $_GET['id'] != '' && is_numeric($_GET['id'])){
	$id = clean($_GET['id']);
	$query = "DELETE FROM specialSections WHERE ID='$id'";
	
	if(mysql_query($query)){
		$msg .= 'Information deleted successfully';
	}else{
		$error .= 'There was an error processing your query. '.mysql_error().' '.$query.'<br />';
	}
}

?>