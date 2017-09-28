<?php
$allowed = TRUE;
mysql_close($database);
$database = mysql_connect($host,$user,$pass);
if(!mysql_select_db($db,$database)){
	$allowed = FALSE;
}
$pageBase = '/?cmd=trackingURLs';

//get llist of pages for use as the location
$query = "SELECT controller, title FROM pageTable WHERE parent IS NULL ORDER BY navOrder ASC";
$pageArray = array();
if($result = mysql_query($query)){
	while($record = mysql_fetch_assoc($result)){
		$a = $record['controller'];
		$b = $record['title'];
		$pageArray[$a] = $b;
		$que = "SELECT controller, title FROM pageTable WHERE parent='$a' ORDER BY navOrder ASC";
		$res = mysql_query($que);
		if(mysql_num_rows($res) > 0){
			while($rec = mysql_fetch_assoc($res)){
				$c = $a.'/'.$rec['controller'];
				$d = '> '.$rec['title'];
				$pageArray[$c] = $d;
			}
		}
	}
}

//place campaigns into a usable array
function get_campaigns(){
	$campaigns = array();
	$query = "SELECT id, name FROM trackingURLs";
	$result = mysql_query($query);
	while($record = mysql_fetch_assoc($result)){
		$campaigns[$record['id']] = $record['name'];
	}
	return $campaigns;
}
$campaigns = get_campaigns();

if($_POST['cmd'] == 'action'){
	$id = clean($_POST['id']);
	$name = clean($_POST['name']);
	$key = clean($_POST['key']);
	$location = clean($_POST['location']);
	
	$passCheck = TRUE;
	if($name == ''){ $passCheck = FALSE; $error = ''; }
	if($key == '' || array_key_exists($key,$pageArray)){ $passCheck = FALSE; $error = 'Controller cannot be left blank and cannot be already be used as a page.'; }
	if($id != ''){
		if($key != $campaigns['id'] && in_array($key,$campaigns)){
			$passCheck = FALSE; $error = 'Controller cannot be left blank and must be unique'; 
		}
	}else{
		if(in_array($key,$campaigns)){
			$passCheck = FALSE; $error = 'Controller cannot be left blank and must be unique'; 
		}
	}
		
	if(!array_key_exists($location,$pageArray)){ $passCheck = FALSE; $error = 'Invalid page selection'; }
	
	if($passCheck){
		$vals = "name='$name', cont='$key', location='$location'";
		if($id == ''){
			$query = "INSERT INTO trackingURLs SET $vals";
		}else{
			$query = "UPDATE trackingURLs SET $vals WHERE id='$id'";
		}
		if(mysql_query($query)){
			$msg .= 'Information updated successfully.<br />';
			unset($_POST);
			//reget campains
			$campaigns = get_campaigns();
		}else{
			$error .= 'There was an error processing your query. '.mysql_error().' '.$query.'<br />';
			if($id != ''){
				$_GET['id'] = $id;
				$_GET['action'] = 'edit';
			}
		}
	}
}elseif($_POST['cmd'] == 'delete' && $_POST['id'] != '' && is_numeric($_POST['id'])){
	$id = clean($_POST['id']);
	$query = "DELETE FROM trackingURLs WHERE id='$id'";
	$query2 = "DELETE FROM trackingURLImpressions WHERE campaign='$id'";
	if(mysql_query($query) && mysql_query($query2)){
		$msg .= 'Information deleted successfully';
		unset($_POST);
	}else{
		$error .= 'There was an error processing your query. '.mysql_error().' '.$query.'<br />';
	}
}
?>