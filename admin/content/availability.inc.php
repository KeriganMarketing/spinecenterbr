<?php
$allowed = TRUE;
mysql_close($database);
$database = mysql_connect($host,$user,$pass);
if(!mysql_select_db($db,$database)){
	$allowed = FALSE;
}
	
$action = $_POST['action'];
//print_r($_POST);
if($action != ''){
	$id = clean($_POST['id']);
	$name = clean($_POST['name']);
	$date = clean(server_date($_POST['date'], TRUE));
	$class = clean($_POST['class']);
	//checks
	$passCheck = TRUE;
	if($date == ''){ $passCheck = FALSE; $error .= 'Date cannot be left blank.<br />'; }
	if($class == ''){ $passCheck = FALSE; $error .= 'Select a class.<br />'; }
	
	if($id == ''){
		//check to see if date is already taken
		$query = "SELECT id FROM availability WHERE date='$date'";
		if(!$result = mysql_query($query)){
			die(mysql_error());
		}else{
			if(mysql_num_rows($result) != 0){
				$record = mysql_fetch_assoc($result);
				$usedID = $record['id'];
				$passCheck = FALSE;
				$error .= 'This date is already booked. Please <a href="/?cmd=availability&id='.$usedID.'">click here</a> to edit the existing record<br />'."\r\n";
			}
		}
	}
	
	if($action == 'delete'){$error = ''; $passCheck = TRUE;}
	
	$params = "name='$name', date='$date', class='$class'";
	if($passCheck){
		if($action == 'add'){
			$query = "INSERT INTO availability SET ".$params;
		}elseif($action == 'edit'){
			$query = "UPDATE availability SET ".$params." WHERE id='$id'";
		}elseif($action == 'delete'){			
			$query = "DELETE FROM availability WHERE id='$id'";
		}
		if(!$result = mysql_query($query)){
			$passCheck = FALSE;
			$error .= mysql_error().'<br>'.$query;
		}else{
			$msg .= 'success';
		}
		if(!$passCheck && $action == 'edit'){
			$_GET['id'] = $id;
		}
	}else{
		if($action == 'edit'){
			$_GET['id'] = $id;
		}
	}
}
if($passCheck && $action != ''){
	unset($_POST);
}
?>