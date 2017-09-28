<?php
$allowed = TRUE;
mysql_close($database);
$database = mysql_connect($host,$user,$pass);
if(!mysql_select_db($db,$database)){
	$allowed = FALSE;
}
$pageName = "/?cmd=survey";
$error = '';
$msg = '';

$prev = '';

// Config
/////////////
//functions//
/////////////
function get_polls_menu($name){
	$query = "SELECT * FROM polls";
	$result = mysql_query($query);
	
	$menu = '<select class="dd" name="'.$name.'" id="'.$name.'" >';
	if($result){
		$result = mysql_fetch_assoc($siteResult);
		while($record = mysql_fetch_assoc($result)){
			$menu .= '<option value="'.$record['id'].'">'.$record['name'].'&nbsp;</option>';
		}
	}else{
		echo 'query failed'. mysql_error();
	}
	$menu .= '</select>';
	
	return $menu;
}
function get_questions($poll,$name = 'question'){
	clean($poll);
	$query = "SELECT * FROM pollQuestions WHERE pID='$poll' ORDER BY sortOrder";
	$result = mysql_query($query);
	
	$menu = '<select class="dd" name="'.$name.'" id="'.$name.'" >';
	while($record = mysql_fetch_assoc($result)){
		$menu .= '<option value="'.$record['id'].'">'.$record['question'].'&nbsp;</option>';
	}
	$menu .= '</select>';
	
	return $menu;
	
}
function delete_answer($id){
	clean($id);
	$query = "DELETE FROM pollAnswers WHERE qID='$id'";
	if(mysql_query($query)){
		return TRUE;
	}else{
		return mysql_error();
	}
}
/////////////////
//ebd functions//
/////////////////

//ACTIONS
if($_POST['cmd'] == 'deleteAction'){
	if($_POST['action'] == 'poll'){
		$poll = clean($_POST['poll']);
		$query = "SELECT id FROM pollQuestions WHERE pID='$poll'";
		$result = mysql_query($query);
		
		while($record = mysql_fetch_assoc($result)){
			$query = "DELETE FROM pollAnswers WHERE qID='".$record['id']."'";
			if(!mysql_query($query)){
				$error .= 'There was an issue deleting answers associated with the question in the poll.'.mysql_error().'<br />';
			}
			$query2 = "DELETE FROM pollQuestions WHERE id='".$record['id']."'";
			if(!mysql_query($query2)){
				$error .= 'There was an issue deleting question associated with the poll.'.mysql_error().'<br />';
			}
		}
		
		$query2 = "DELETE FROM polls WHERE id='$poll'";
		if(!mysql_query($query2)){
			$error .= 'There was an issue deleting answers associated with the question in the poll.'.mysql_error().'<br />';
		}else{
			$msg .= 'The Poll was deleted successfully.<br>';
		}
	}
	if($_POST['action'] == 'question'){
		$id = clean($_POST['id']);
		$query = "DELETE FROM pollQuestions WHERE id='$id'";
		if(!mysql_query($query)){
			$error .= 'There was a problem deleting the question from the database'.mysql_error().'<br/>';
		}else{
			$msg .= 'Question was deleted successfully.<br>';
		}
		
		$query2 = "DELETE FROM pollAnswers WHERE qID='$id'";
		if(!mysql_query($query2)){
			$error .= 'There was a problem deleting the answers from the database'.mysql_error().'<br/>';
		}else{
			$msg .= 'Answers were deleted successfully.<br>';
		}
	}
	if($_POST['action'] == 'answer'){
		$id = clean($_POST['id']);
		$query = "DELETE FROM pollAnswers WHERE id='$id'";
		if(!mysql_query($query)){
			$error .= 'There was a problem deleting the answer from the database'.mysql_error().'<br/>';
		}else{
			$msg .= 'Answer was deleted successfully.<br>';
		}
	}
}
if($_POST['cmd'] == 'editAction'){
	if($_POST['action'] == 'poll'){
		$name = clean($_POST['name']);
		$id = clean($_POST['id']);
		
		$passCheck = TRUE;
		if($name == ''){$passCheck = FALSE; $error .= 'Name cannot be left blank.<br>';}
		if($id == '' || !is_numeric($id)){$passCheck = FALSE; $error .= 'id is invalid.<br>';}
		
		if($passCheck){
			$query = "UPDATE polls SET name='$name' WHERE id='$id'";
			if(mysql_query($query)){
				$msg .= 'Poll updated Successfully.';
				unset($_POST);
			}else{
				$error .= 'There was an error updating the Poll.'.mysql_error().'<br>';
			}
		}
	}
	if($_POST['action'] == 'question'){
		$id = clean($_POST['id']);
		$pID = clean($_POST['pID']);
		$question = clean($_POST['question']);
		$type = clean($_POST['type']);
//		$count = clean($_POST['count']);
		$height = clean($_POST['height']);
		$width = clean($_POST['width']);
		
		$passCheck = TRUE;
		if($id == '' || !is_numeric($id)){$passCheck = FALSE; $error .= 'ID appears to be invalid.<br>';}
		if($pID == '' || !is_numeric($pID)){$passCheck = FALSE; $error .= 'pID appears to be invalid.<br>';}
		if($question == ''){$passCheck = FALSE; $error .= 'Question cannot be left blank.<br>';}
		if($type != 'text' && $type != 'check' && $type != 'radio' && $type != 'textarea' && $type != 'dropdown'){$passCheck = FALSE; $error .= 'invalid type.<br>';}
//		if($type == 'check' || $type == 'radio' || $type == 'dropdown'){
//			if($count == ''){$passCheck = FALSE; $error .= 'Answer count cannot be left blank.<br>';}
//		}
		if($type == 'textarea'){
			if($height == ''){$passCheck = FALSE; $error .= 'Height cannot be left blank.<br>';}
			if($width == ''){$passCheck = FALSE; $error .= 'Width cannot be left blank.<br>';}
		}
		
		if($passCheck){
			$query = "UPDATE pollQuestions SET pID='$pID', question='$question', type='$type', width='$width', height='$height' WHERE id='$id'";
			if($result = mysql_query($query)){
				$msg .= 'Question updated Successfully.';
				unset($_POST);
				$_GET['cmd'] = 'edit';
				$_GET['poll'] = $pID;
			}else{
				$error .= 'There was an error updating the Question.'.mysql_error().'<br>';
				$_GET['cmd'] = 'question';
				$_GET['id'] = $id;
			}
		}else{
			$_GET['cmd'] = 'question';
			$_GET['id'] = $id;
			echo $_POST['id'];
		}
	}
	if($_POST['action'] == 'answer'){
		$id = clean($_POST['id']);
		$answer = clean($_POST['answer']);
		$qID = clean($_POST['qID']);
		
		$passCheck = TRUE;
		if($id == '' || !is_numeric($id)){$passCheck = FALSE; $error .= 'qID appears to be invalid.<br>';}
		if($qID == '' || !is_numeric($qID)){$passCheck = FALSE; $error .= 'qID appears to be invalid.<br>';}
		if($answer == ''){$passCheck = FALSE; $error .= 'Option cannot be left blank.<br>';}
		
		if($passCheck){
			$query = "UPDATE pollAnswers SET qID='$qID', answer='$answer' WHERE id='$id'";
			if($result = mysql_query($query)){
				$msg .= 'Answer Updated Successfully.';
				unset($_POST);
			}else{
				$error .= 'There was an error Updating the answer. '.mysql_error().'<br>';
			}
		}
	}
}
if($_POST['cmd'] == 'addAction'){
	if($_POST['action'] == 'poll'){
		$name = clean($_POST['name']);
		
		$passCheck = TRUE;
		if($name == ''){$passCheck = FALSE; $error .= 'Name cannot be left blank.<br>';}
		
		if($passCheck){
			$query = "INSERT INTO polls SET name='$name', hash='".md5(time())."'";
			if($result = mysql_query($query)){
				$msg .= 'Poll added Successfully.';
				unset($_POST);
			}else{
				$error .= 'There was an error adding the Poll.'.mysql_error().'<br>';
			}
		}
	}
	if($_POST['action'] == 'question'){
		$pID = clean($_POST['pID']);
		$question = clean($_POST['question']);
		$type = clean($_POST['type']);
//		$count = clean($_POST['count']);
		$height = clean($_POST['height']);
		$width = clean($_POST['width']);
		
		$passCheck = TRUE;
		if($pID == '' || !is_numeric($pID)){$passCheck = FALSE; $error .= 'pID appears to be invalid.<br>';}
		if($question == ''){$passCheck = FALSE; $error .= 'Question cannot be left blank.<br>';}
		if($type != 'text' && $type != 'check' && $type != 'radio' && $type != 'textarea' && $type != 'dropdown'){$passCheck = FALSE; $error .= 'invalid type.<br>';}
//		if($type == 'check' || $type == 'radio' || $type == 'dropdown'){
//			if($count == ''){$passCheck = FALSE; $error .= 'Answer count cannot be left blank.<br>';}
//		}
		if($type == 'textarea'){
			if($height == ''){$passCheck = FALSE; $error .= 'Height cannot be left blank.<br>';}
			if($width == ''){$passCheck = FALSE; $error .= 'Width cannot be left blank.<br>';}
		}
		
		if($passCheck){
			$query = "INSERT INTO pollQuestions SET pID='$pID', question='$question', type='$type', width='$width', height='$height'";
			if($result = mysql_query($query)){
				$msg .= 'Question added Successfully.';
				unset($_POST);
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
			$query = "INSERT INTO pollAnswers SET qID='$qID', answer='$answer'";
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
	
	
	
	$pID = clean($_GET['id']);
	
	//put the quetions in an array with id as key
	$query = "SELECT id, question, type FROM pollQuestions WHERE pID=$pID ORDER BY sortOrder";
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
	$query = "SELECT * FROM pollUsers WHERE pID=$pID";
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
			$query = "SELECT answer FROM pollResults WHERE uID=$id AND qID='$key'";
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
					$res = mysql_query("SELECT answer FROM pollAnswers WHERE id='".clean($v)."'");
					$rec = mysql_fetch_assoc($res);
					$answer .= $rec['answer'].' / ';
				}
			}elseif(in_array($qID,$extract_answer)){
				$res = mysql_query("SELECT answer FROM pollAnswers WHERE id='$aID'");
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
	
	$filename = "surveyExport.csv";
	
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