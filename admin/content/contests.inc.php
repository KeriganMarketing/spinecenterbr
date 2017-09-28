<?php
$modUpdate = '04/22/14';
$allowed = TRUE;
mysql_close($database);
$database = mysql_connect($host,$user,$pass);
if(!mysql_select_db($db,$database)){
	$allowed = FALSE;
}
$pageName = "/?cmd=contests";
$error = '';
$msg = '';

$prev = '';

$query = "SELECT errorReporting FROM contest_questions";
$result = mysql_query($query);
if($result){
	$hasErrorChecking = TRUE;
}

// Config
/////////////
//functions//
/////////////
function get_contests_menu($name){
	$query = "SELECT * FROM contests";
	$result = mysql_query($query);
	
	$menu = '<select class="dd" name="'.$name.'" id="'.$name.'" style="margin-top:0;" >
	<option>-- Select --</option>';
	while($record = mysql_fetch_assoc($result)){
		$menu .= '<option value="'.$record['id'].'">'.$record['name'].'&nbsp;</option>';
	}
	$menu .= '</select>';
	
	return $menu;
}
function get_questions($contest,$name = 'question'){
	clean($contest);
	$query = "SELECT * FROM contest_questions WHERE fID='$contest' ORDER BY sortOrder";
	$result = mysql_query($query);
	
	$menu = '<select class="dd" name="'.$name.'" id="'.$name.'" >
	<option>-- Select --</option>';
	while($record = mysql_fetch_assoc($result)){
		$menu .= '<option value="'.$record['id'].'">'.$record['question'].'&nbsp;</option>';
	}
	$menu .= '</select>';
	
	return $menu;
	
}
function delete_answer($id){
	clean($id);
	$query = "DELETE FROM contest_answers WHERE qID='$id'";
	if(mysql_query($query)){
		return TRUE;
	}else{
		return mysql_error();
	}
}
/////////////////
//ebd functions//
/////////////////

//popup for deleting answers
if($_GET['action'] == 'deleteAnswer'){
	$id = clean($_GET['id']);
	$query = "DELETE FROM contest_answers WHERE id='$id'";
	if(!mysql_query($query)){
		$msg = 'There was a problem deleting the answer from the database'.mysql_error().'<br/>';
	}else{
		$msg = 'Answer was deleted successfully.<br>';
	}
	die('
		<div style="margin: 10px; font-family:Arial; font-size: 14px;">'.$msg.'</div>
	');
}


//ACTIONS
if($_POST['cmd'] == 'deleteAction'){
	if($_POST['action'] == 'contest'){
		$contest = clean($_POST['contest']);
		$query = "SELECT id FROM contest_questions WHERE fID='$contest'";
		$result = mysql_query($query);
		
		while($record = mysql_fetch_assoc($result)){
			$query = "DELETE FROM contest_answers WHERE qID='".$record['id']."'";
			if(!mysql_query($query)){
				$error .= 'There was an issue deleting answers associated with the question in the contest.'.mysql_error().'<br />';
			}
			$query2 = "DELETE FROM contest_questions WHERE id='".$record['id']."'";
			if(!mysql_query($query2)){
				$error .= 'There was an issue deleting question associated with the contest.'.mysql_error().'<br />';
			}
		}
		
		$query2 = "DELETE FROM contests WHERE id='$contest'";
		if(!mysql_query($query2)){
			$error .= 'There was an issue deleting answers associated with the question in the contest.'.mysql_error().'<br />';
		}else{
			$msg .= 'The contest was deleted successfully.<br>';
		}
	}
	if($_POST['action'] == 'question'){
		$id = clean($_POST['id']);
		$query = "DELETE FROM contest_questions WHERE id='$id'";
		if(!mysql_query($query)){
			$error .= 'There was a problem deleting the question from the database'.mysql_error().'<br/>';
		}else{
			$msg .= 'Question was deleted successfully.<br>';
		}
		
		$query2 = "DELETE FROM contest_answers WHERE qID='$id'";
		if(!mysql_query($query2)){
			$error .= 'There was a problem deleting the answers from the database'.mysql_error().'<br/>';
		}else{
			$msg .= 'Answers were deleted successfully.<br>';
		}
	}
}
if($_POST['cmd'] == 'editAction'){
	if($_POST['action'] == 'contest'){
		$name = clean($_POST['name']);
		$startDate = clean(server_date($_POST['startDate']));
		$endDate = clean(server_date($_POST['endDate']));
		$instructions = clean(stripslashes($_POST['instructions']));
		$button_text = clean($_POST['button_text']);
		$contest_contacts = clean($_POST['contest_contacts']);
		$success_message = clean(stripslashes($_POST['success_message']));
		$fblike = clean($_POST['fblike']);
		$rules = clean(stripslashes($_POST['rules']));
		$id = clean($_POST['id']);
		
		$passCheck = TRUE;
		if($name == ''){$passCheck = FALSE; $error .= 'Name cannot be left blank.<br>';}
		if($id == '' || !is_numeric($id)){$passCheck = FALSE; $error .= 'id is invalid.<br>';}
		
		if($passCheck){
			$query = "UPDATE contests SET name='$name', facebookLike='$fblike', rules='$rules', contest_contacts='$contest_contacts', instructions='$instructions', button_text='$button_text', success_message='$success_message', contestStart='$startDate', contestEnd='$endDate' WHERE id='$id'";
			if(mysql_query($query)){
				$msg .= 'contest updated Successfully.';
				unset($_POST);
			}else{
				$error .= 'There was an error updating the contest.'.mysql_error().'<br>';
			}
		}
	}
	if($_POST['action'] == 'question'){
		$fID = clean($_POST['fID']);
		$id = clean($_POST['id']);
		$question = clean($_POST['question']);
		$required = clean($_POST['required']);
		$height = clean($_POST['height']);
		$width = clean($_POST['width']);
		$errorrep = clean($_POST['errorrep']);
		$specialInstructions = clean($_POST['specialInstructions']);
		
		$passCheck = TRUE;
		if($id == '' || !is_numeric($id)){$passCheck = FALSE; $error .= 'ID appears to be invalid.<br>';}
		if($question == ''){$passCheck = FALSE; $error .= 'Question cannot be left blank.<br>';}
//		if($type == 'check' || $type == 'radio' || $type == 'dropdown'){
//			if($count == ''){$passCheck = FALSE; $error .= 'Answer count cannot be left blank.<br>';}
//		}
		if($type == 'textarea'){
			if($height == ''){$passCheck = FALSE; $error .= 'Height cannot be left blank.<br>';}
			if($width == ''){$passCheck = FALSE; $error .= 'Width cannot be left blank.<br>';}
		}
		
		if($passCheck){
			$query = "UPDATE contest_questions SET question='$question', specialInstructions='$specialInstructions', errorReporting='$errorrep', width='$width', height='$height', required='$required' WHERE id='$id'";
			if($result = mysql_query($query)){
				$msg .= 'Question updated Successfully.';
				foreach($_POST['answer'] as $key=>$value){
					$fieldID=$_POST['fieldID'][$key];
					$pc = TRUE;
					if($value == ''){$pc = FALSE; $error .= 'Answer cannot be left blank';}
					if(!is_numeric($fieldID)){$pc = FALSE; $error .= 'Answer ID was invalid';}
					if($pc){
						if($fieldID == 0){
							$que = "INSERT INTO contest_answers SET answer='$value', qID='$id'";
						}else{
							$que = "UPDATE contest_answers SET answer='$value' WHERE id='$fieldID'";
						}
						if(!mysql_query($que)){
							$error .= 'There was trouble querying the database'.mysql_error();
						}
					}
				}
				unset($_POST);
				$_GET['action'] = 'edit';
				$_GET['contest'] = $fID;
			}else{
				$error .= 'There was an error updating the Question.'.mysql_error().'<br>';
				$_GET['action'] = 'question';
				$_GET['id'] = $id;
			}
		}else{
			$_GET['action'] = 'question';
			$_GET['id'] = $id;
		}
	}
}
if($_POST['cmd'] == 'addAction'){
	if($_POST['action'] == 'contest'){
		$name = clean($_POST['name']);
		
		$passCheck = TRUE;
		if($name == ''){$passCheck = FALSE; $error .= 'Name cannot be left blank.<br>';}
		
		if($passCheck){
			$query = "INSERT INTO contests SET name='$name', hash='".md5(time())."'";
			if($result = mysql_query($query)){
				$msg .= 'contest added Successfully.';
				$_GET['action'] = 'edit';
				$_GET['contest'] = mysql_insert_id();
				unset($_POST);
			}else{
				$error .= 'There was an error adding the contest.'.mysql_error().'<br>';
			}
		}
	}
	if($_POST['action'] == 'question'){
		$fID = clean($_POST['fID']);
		$question = clean($_POST['question']);
		$type = clean($_POST['type']);
		$required = clean($_POST['required']);
		$height = clean($_POST['height']);
		$width = clean($_POST['width']);
		$specialinstrucions = clean($_POST['specialinstructions']);
		
		$passCheck = TRUE;
		if($fID == '' || !is_numeric($fID)){$passCheck = FALSE; $error .= 'fID appears to be invalid.<br>';}
		if($question == ''){$passCheck = FALSE; $error .= 'Question cannot be left blank.<br>';}
		if($type != 'text' && $type != 'check' && $type != 'radio' && $type != 'textarea' && $type != 'dropdown' && $type != 'file'){$passCheck = FALSE; $error .= 'invalid type.<br>';}
//		if($type == 'check' || $type == 'radio' || $type == 'dropdown'){
//			if($count == ''){$passCheck = FALSE; $error .= 'Answer count cannot be left blank.<br>';}
//		}
		if($type == 'textarea'){
			if($height == ''){$passCheck = FALSE; $error .= 'Height cannot be left blank.<br>';}
			if($width == ''){$passCheck = FALSE; $error .= 'Width cannot be left blank.<br>';}
		}
		
		if($passCheck){
			$query = "INSERT INTO contest_questions SET fID='$fID', question='$question', specialInstructions='$specialinstrucions', type='$type', width='$width', height='$height', required='$required'";
			if($result = mysql_query($query)){
				$msg .= 'Question added Successfully.';
				unset($_POST);
				if($type == 'check' || $type == 'radio' || $type == 'dropdown'){
					$_GET['action'] = 'question';
					$_GET['id'] = mysql_insert_id();
				}
			}else{
				$error .= 'There was an error adding the Question.'.mysql_error().'<br>';
			}
		}
	}
	if($_POST['action'] == 'answer'){
		$answer = clean($_POST['answer']);
		$qID = clean($_POST['qID']);
		
		$passCheck = TRUE;
		if($qID == '' || !is_numeric($qID)){$passCheck = FALSE; $error .= 'qID appears to be invalid.<br>';}
		if($answer == ''){$passCheck = FALSE; $error .= 'Option cannot be left blank.<br>';}
		
		if($passCheck){
			$query = "INSERT INTO contest_answers SET qID='$qID', answer='$answer'";
			if($result = mysql_query($query)){
				$msg .= 'Answer added Successfully.';
				unset($_POST);
			}else{
				$error .= 'There was an error adding the Answer.'.mysql_error().'<br>';
			}
		}
	}
}
if($_GET['action'] == 'export' && is_numeric($_GET['id'])){
	
	
	
	$fID = clean($_GET['id']);
	
	//put the quetions in an array with id as key
	$query = "SELECT id, question, type FROM contest_questions WHERE fID=$fID ORDER BY sortOrder";
	$result = mysql_query($query);
	
	$questions = array();
	$multiple_choice = array('radio','check','dropdown');
	while($record = mysql_fetch_assoc($result)){
		$questions[$record['id']] = str_replace(',','',$record['question']);
		if(in_array($record['type'],$multiple_choice)){
			$extract_answer[] = $record['id'];
		}
	}
	
	//put users into an array
	$query = "SELECT * FROM contest_users WHERE fID=$fID";
	if(!$result = mysql_query($query)){
		die(mysql_error());
	}
	
	$users = array();
	while($record = mysql_fetch_assoc($result)){
		$users[$record['id']] = array('email'=>$record['email'], 'time'=>$record['time'],'IP'=>$record['IP']);
	}
	
	$data = array();
	$answers = array();
	foreach($users as $id => $user){
		foreach($questions as $key => $value){
			$qID = clean($key);
			$query = "SELECT answer FROM contest_results WHERE uID=$id AND qID='$key'";
			$result = mysql_query($query);
			if(mysql_num_rows($result) == 1){
				$record = mysql_fetch_assoc($result);
				$aID = $record['answer'];
			}else{
				$aID = array();
				while($record = mysql_fetch_assoc($result)){
					$aID[] = $record['answer'];
				}
			}
			
			
			if(is_array($aID)){
				$answer = '';
				foreach($aID as $v){
					$res = mysql_query("SELECT answer FROM contest_answers WHERE id='".clean($v)."'");
					$rec = mysql_fetch_assoc($res);
					$answer .= $rec['answer'].' / ';
				}
			}elseif(in_array($qID,$extract_answer)){
				$res = mysql_query("SELECT answer FROM contest_answers WHERE id='$aID'");
				$rec = mysql_fetch_assoc($res);
				$answer = $rec['answer'];
			}else{
				$answer = $aID;
			}
			$answers[$value] = str_replace(',','',$answer);
		}
		$data[] = array_merge($user,$answers);
	}
	//print_r($data);
	//feed the final array to our formatting function...
	$contents = getExcelData($data);
	
	$filename = "contestExport.csv";
	
	//prepare to give the user a Save/Open dialog...
	header ("Content-type: application/octet-stream");
	header ("Content-Disposition: attachment; filename=".$filename);
	
	//setting the cache expiration to 30 seconds ahead of current time. an IE 8 issue when opening the data directly in the browser without first saving it to a file
	$expiredate = time() + 30;
	$expireheader = "Expires: ".gmdate("D, d M Y G:i:s",$expiredate)." GMT";
	header ($expireheader);
	
	//output the contents
	echo $contents;
	die();
	
}
?>