<?php
$allowed = TRUE;
mysql_close($database);
$database = mysql_connect($host,$user,$pass);
if(!mysql_select_db($db,$database)){
	$allowed = FALSE;
}
$pageBase = '/?cmd=testimonials';

$query = "SELECT sortOrder FROM testimonials";
$result = mysql_query($query);
if($result){
	$hasSorting = TRUE;
}

if($_POST['action'] == 'process'){
	$id = clean($_POST['id']);
	$author = clean($_POST['author']);
	$content = clean(trim(str_replace(array('<p>','</p>'),array('',''),stripslashes($_POST['pageContent']))));
	$display = clean($_POST['display']);
	
	$passCheck = TRUE;
	if($content == ''){ $passCheck = FALSE; $error = 'Content cannot be left blank.<br />'; }
	if($author == ''){ $passCheck = FALSE; $error = 'Signature cannot be left blank.<br />'; }
	
	if($passCheck){
		$vals = "author='$author', content='$content', display='$display'";
		
		if($id == ''){
			$query = "INSERT INTO testimonials SET $vals";
		}else{
			$query = "UPDATE testimonials SET $vals WHERE id='$id'";
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
	$query = "DELETE FROM testimonials WHERE id='$id'";
	
	if(mysql_query($query)){
		$msg .= 'Information deleted successfully';
	}else{
		$error .= 'There was an error processing your query. '.mysql_error().' '.$query.'<br />';
	}
}
if($_POST['action'] == 'sortOrder' ){
	
	$count = count($_POST['sortOrder']);
	$sortOrder = $_POST['sortOrder'];
	//print_r ($_POST);
	//print_r ($count);
	$id = $_POST['id'];
	for ($j = 0, $k = count($_POST['sortOrder']); $j < $k; $j++) {
		$updatedID = $_POST['sortOrder'][$j];
		if($updatedID != ''){
			//echo 'it works!';
			$newOrderNum = $j*10;
			$tquery = "UPDATE testimonials SET sortOrder='$newOrderNum' WHERE id='".$_POST['sortOrder'][$j]."'";
			
			if(mysql_query($tquery)){
				$success = TRUE;
				$msg = 'Testimonials have been arranged in the order specified.';
				//unset($_POST);
				
			}else{
				$error .= 'There was an error processing your request. <br />'.mysql_error();
				
			}
		}
	}
}
?>