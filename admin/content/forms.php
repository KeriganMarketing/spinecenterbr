<script type="text/javascript">
	$(function(){
		$('#tabs').tabs();
		$(".sortable").sortable();
		$(".datepicker").datepicker();
	});
</script>

<?php

//ini_set('display_errors', 'On');

//reg field function
function make_field($field_val,$fieldID='0',$pageFile='/?cmd=forms'){
	$field = '<li>
	<input type="text" class="mid-text" name="answer[]" value="'.$field_val.'" '; if($field_val == 'Answer'){ $field .= 'style="color:#555;" '; } $field .= 'onFocus="if(this.value == \'Answer\'){ this.value = \'\';} this.style.color = \'#000\';" />
	<input name="fieldID[]" type="hidden" value="'.$fieldID.'" />
	<input type="button" style="font-size: 12px; padding: 2px 5px;" value="remove" onclick="if(confirm(\'Are you sure you want to remove this?\')){this.parentNode.parentNode.removeChild(this.parentNode);'; if($fieldID != 0){ $field .= ' window.open(\''.$pageFile.'&action=deleteAnswer&id='.$fieldID.'\', \'killfield\', \'scrollbars=no,menubar=no,height=100,width=400,resizable=no,toolbar=no,status=no\');'; } $field .= ' }" /></li>';
	return $field;
}

//Different pages available to user
if($_GET['action'] == 'edit' && is_numeric($_GET['form'])){
	$form = clean($_GET['form']);
	
	$query = "SELECT * FROM forms WHERE id='$form'";
	$result = mysql_query($query);
	
	if(mysql_num_rows($result) == 0){ die('invalid ID');}
	$record = mysql_fetch_assoc($result);
	
	$body .= '<h1>Editing "'.$record['name'].'"</h1>
	<p>EMBED CODE: [form]'.$form.'[/form]</p>
	<div id="tabs">
		<ul>
			<li><a href="#questions">Manage Questions</a></li>
			<li><a href="#settings">Manage Form Settings</a></li>';		
	$body .= '</ul>
		<div id="settings">';
		
	$body .= '<form action="'.$pageName.'&action=edit&form='.$form.'" method="post">
	<label>Name:<br />
	<input type="text" class="text" name="name" value="'.$record['name'].'" /></label>
	<label>Submit Button Text:<br />
	<input type="text" class="text" name="button_text" value="'.$record['button_text'].'" /></label>
	<label>Email Adresses to be Alerted When the Form is Submitted:<span class="small">(separated by commas)</span><br />
	<input type="text" class="text" name="form_contacts" value="'.$record['form_contacts'].'" /></label>
	<label>Instructions:<br />
	<textarea class="textarea" id="instructions" name="instructions">'.stripslashes($record['instructions']).'</textarea></label>
	<label>Success Message:<br />
	<textarea class="textarea" id="success_message" name="success_message">'.stripslashes($record['success_message']).'</textarea></label>
	<input type="hidden" name="cmd" value="editAction" />
	<input type="hidden" name="action" value="form" />
	<input type="hidden" name="id" value="'.$form.'" />
	<input type="submit" class="submit" style="margin-top: 5px;" value="Update">
	</form>
	</div>
	<div id="questions">
	<h3>Manage Questions</h3>
	<div id="manage-questions">';
	
	$query = "SELECT * FROM form_questions WHERE fID='$form' ORDER BY id ASC";
	
	$result = mysql_query($query);
	if(mysql_num_rows($result) == 0){
		$body .= 'No questions';
	}else {
		
	$i = 1;
		while($record = mysql_fetch_assoc($result)){
			$id = clean($record['id']);
			$question = clean($record['question']);
			$required = clean($record['required']);
			
			$body .= '
			<div class="survey-question">'.$i.'. <a href="'.$pageName.'&action=question&id='.$id.'">'.$question; if($required == 1){ $body .= '*'; } $body .= '</a>
			<form name="deleteQuestion'.$id.'"  onSubmit="return confirm(\'Are you sure you want to delete this question?\');" style="display: inline;" action="'.$pageName.'&action=edit&form='.$form.'" method="post">
			<input type="hidden" name="id" value="'.$id.'" />
			<input type="hidden" name="cmd" value="deleteAction" />
			<input type="hidden" name="action" value="question" />
			<input style="font-size: 10px;" type="submit" value="DELETE" />
			</form></div>
			';
			$i++;
		}
	}
	//GET THE WYSISYG 
	
	
	$body .= '
	</div>
	<h3>Add a Question</h3>
	<form action="'.$pageName.'&action=edit&form='.$form.'" method="post">
	<label>Question:<br />
	<input type="text" class="text" name="question" value="'.$_POST['question'].'" /></label>
	<label class="radio" style="width: 310px;"><input type="checkbox" class="checkbox" name="required" value="1"'; if($_POST['required']== 1){ $body.= ' checked'; } $body .= ' />&nbsp; Required</label>
	<label>Type:<br />
	<select class="dd" name="type">
		<option'; if($_POST['type'] == 'text'){$body .= '  selected="selected"'; } $body .= ' onClick="showhide(\'size\', \'none\')" value="text">short response</option>
		<option'; if($_POST['type'] == 'radio'){$body .= '  selected="selected"'; } $body .= ' onClick="showhide(\'size\', \'none\')" value="radio">radio</option>
		<option'; if($_POST['type'] == 'check'){$body .= '  selected="selected"'; } $body .= ' onClick="showhide(\'size\', \'none\')" value="check">check box</option>
		<option'; if($_POST['type'] == 'dropdown'){$body .= '  selected="selected"'; } $body .= ' onClick="showhide(\'size\', \'none\')" value="dropdown">dropdown</option>
		<option'; if($_POST['type'] == 'textarea'){$body .= '  selected="selected"'; } $body .= ' onClick="showhide(\'size\', \'block\')" value="textarea">long response</option>
	</select></label>
	<div id="size" style="'; if($_POST['type'] == 'textarea'){$body .= 'display:block;'; }else{ $body .= 'display:none;'; } $body .= ' margin-top: 10px;">
		<label class="radio" style="width: 310px;">Width: <input type="text" class="short-text" name="width" value="'.$_POST['width'].'" /></label>
		<label class="radio" style="width: 310px;">Height: <input type="text" class="short-text" name="height" value="'.$_POST['height'].'" /></label>
	</div>';
	if($hasErrorChecking){
		$body .= '
		<div id="error-reporting">
		<label>Error Checking Type: (optional)<br />
		<select class="dd" name="errorrep">
			<option '; if($_POST['errorrep'] == ''){ $body .= ' selected="selected"'; } $body .= ' value="">none</option>
			<option '; if($_POST['errorrep'] == 'email'){ $body .= ' selected="selected"'; } $body .= ' value="email">Email address</option>
			<option '; if($_POST['errorrep'] == 'phone'){ $body .= ' selected="selected"'; } $body .= ' value="phone">10-digit phone number</option>
			<option '; if($_POST['errorrep'] == 'date'){ $body .= ' selected="selected"'; } $body .= ' value="date">Date (adds calendar selector)</option>
			<option '; if($_POST['errorrep'] == 'address'){ $body .= ' selected="selected"'; } $body .= ' value="address">Address (adds fields for city, state, zip)</option>
		</select></label>
		</div>';
	}
	
	$body .= '<input type="hidden" name="fID" value="'.$form.'" />
	<input type="hidden" name="cmd" value="addAction" />
	<input type="hidden" name="action" value="question" />
	<input type="submit" class="submit" style="margin-top: 5px;" value="Submit">
	</form>
	';
	
	$prev = ''.$pageName.'&action=manage';
	
}elseif($_GET['action'] == 'question' && is_numeric($_GET['id'])){
	$id = clean($_GET['id']);
	
	$query = "SELECT * FROM form_questions WHERE id='$id'";
	
	$result = mysql_query($query);
	$record = mysql_fetch_assoc($result);
	
	$fID = $record['fID'];
	$body .= '
			<div id="readroot" style="display: none;">
			'.make_field('Answer').'
			</div>
	<form action="'.$pageName.'" method="post">
	<label>Question:<br />
	<input type="text" class="text" name="question" value="'.$record['question'].'"/></label>
	<label class="radio" style="width: 310px;"><input type="checkbox" class="checkbox" name="required" value="1"'; if($record['required']== 1){ $body.= ' checked'; } $body .= ' />&nbsp; Required</label><br />

	';

	if($hasErrorChecking){
		$body .= '
		<div id="error-reporting">
		<label>Error Checking Type: (optional)<br />
		<select class="dd" name="errorrep">
			<option '; if($_POST['errorrep'] == ''){ $body .= ' selected="selected"'; } $body .= ' value="">none</option>
			<option '; if($_POST['errorrep'] == 'email'){ $body .= ' selected="selected"'; } $body .= ' value="email">Email address</option>
			<option '; if($_POST['errorrep'] == 'phone'){ $body .= ' selected="selected"'; } $body .= ' value="phone">10-digit phone number</option>
			<option '; if($_POST['errorrep'] == 'date'){ $body .= ' selected="selected"'; } $body .= ' value="date">Date (adds calendar selector)</option>
			<option '; if($_POST['errorrep'] == 'address'){ $body .= ' selected="selected"'; } $body .= ' value="address">Address (adds fields for city, state, zip)</option>
		</select></label>
		</div>';
	}

	if($record['type'] == 'radio' || $record['type'] == 'check' || $record['type'] == 'dropdown'){
		$body .= '
			<label>Manage your Answers</label>
			<ol id="answers">';
			$que = "SELECT * FROM form_answers WHERE qID='$id' ORDER BY id ASC";
			$res = mysql_query($que);
			if(mysql_num_rows($res) > 0){
				while($rec = mysql_fetch_assoc($res)){
					$body .= make_field($rec['answer'], $rec['id']);
				}
			}else{
				$body .= make_field('Answer');
			}
			$body .= '
			<span id="writeroot"></span>
			</ol><br />
			<input type="button" value="Add Answer" onclick="moreFields();"/>&nbsp;&nbsp;<br />
		';
	}
	if($record['type'] == 'textarea'){
		$body .= '<div id="size" style="'; $body .= 'display:block; margin-top: 10px;">
			<label class="radio" style="width: 310px;">Width: <input class="short-text" type="text" name="width" value="'.$record['width'].'" /></label>
			<label class="radio" style="width: 310px;">Height: <input class="short-text" type="text" name="height" value="'.$record['height'].'" /></label>
		</div>';
	}
	$body .='
	<input type="hidden" name="fID" value="'.$fID.'" />
	<input type="hidden" name="id" value="'.$id.'" />
	<input type="hidden" name="cmd" value="editAction" />
	<input type="hidden" name="action" value="question" />
	<input type="submit" class="submit" style="margin-top: 5px;" value="Update">
	</form>
	';
	
	$prev = ''.$pageName.'&action=edit&form='.$fID;
}elseif($_GET['action'] == 'view' && is_numeric($_GET['question'])){
	$qID = clean($_GET['question']);
	$qQuery = "SELECT * FROM form_questions WHERE id='$qID' ORDER BY sortOrder";
	$qResult = mysql_query($qQuery);
	$qRecord = mysql_fetch_assoc($qResult);
	
	$question = $qRecord['question'];
	$type = $qRecord['type'];
	$height = $qRecord['height'];
	$width = $qRecord['width'];
	
	$fID = $qRecord['fID'];
	
	//add the question
	$body .= '<p class="poll-question" id="'.$qID.'">'.$question.'</p>';
	
	$query = "SELECT * FROM form_results WHERE qID='$qID'";
	$result = mysql_query($query);
	while($record = mysql_fetch_assoc($result)){
		if($record['answer'] != ''){
			if($type == 'text' || $type == 'textarea'){
				$body .= '<div class="text-answer">'.$record['answer'].'</div>'."\r\n";
			}else{
				$body .= '<li class="answer"><span class="answer-left">'.$record['answer'].'</span></li>'."\r\n";
			}
		}
	}
	
	$prev = ''.$pageName.'&action=view&form='.$fID;
}elseif($_GET['action'] == 'view' && is_numeric($_GET['user']) && is_numeric($_GET['form'])){
	
	$uID = clean($_GET['user']);
	$fID = clean($_GET['form']);
	
	$query = "SELECT * FROM form_users WHERE id='$uID'";
	$result = mysql_query($query);
	$record = mysql_fetch_assoc($result);
	
	$body .= '
	<p class="user-info"><b>USER INFO</b><br />';
	if($record['email'] != ''){
		$body .= 'Email: '.$record['email'].'<br />';
	}
	$body .= '
	IP Address: '.$record['IP'].'<br />
	Time: '.$record['time'].'<br />
	</p>
	';
	
	$qQuery = "SELECT * FROM form_questions WHERE fID='$fID' ORDER BY sortOrder";
	$qResult = mysql_query($qQuery);
	
	$i = 1;
	while($qRecord = mysql_fetch_assoc($qResult)){
		$qID = $qRecord['id'];
		$question = $qRecord['question'];
		$type = $qRecord['type'];
		$height = $qRecord['height'];
		$width = $qRecord['width'];
		
		
		//add the question
		$body .= '<p class="poll-question" id="'.$qID.'">'.$i.'. '.$question.'</p>';
			$query = "SELECT answer FROM form_results WHERE qID='$qID' AND uID='$uID'";
			if(!$result = mysql_query($query)){die(mysql_error());}
			
		if($type =='text' || $type =='textarea'){
			$record = mysql_fetch_assoc($result);
			$body .= '<li class="answer">'.$record['answer'].'</li>';
		}elseif($type == 'radio' || $type == 'dropdown'){
			$record = mysql_fetch_assoc($result);
			$aQuery = "SELECT * FROM form_answers WHERE id='".$record['answer']."' ORDER BY sortOrder";
			$aResult = mysql_query($aQuery);
			$aRecord = mysql_fetch_assoc($aResult);
			
			$body .= '<li class="answer"><span class="answer-left">'.$aRecord['answer'].'</span></li>'."\r\n";
		}elseif($type == 'check'){
			while($record = mysql_fetch_assoc($result)){
				$aQuery = "SELECT * FROM form_answers WHERE id='".$record['answer']."' ORDER BY sortOrder";
				$aResult = mysql_query($aQuery);
				$aRecord = mysql_fetch_assoc($aResult);
				
				$body .= '<li class="answer"><span class="answer-left">'.$aRecord['answer'].'</span></li>'."\r\n";
			}
		}
		$i++;
	}
	
	$prev = ''.$pageName.'&action=view&form='.$fID;
}elseif($_GET['action'] == 'view' && is_numeric($_GET['form'])){
	
	$fID = clean($_GET['form']);
	
	$body .= '<a class="export" title="Export to Excel" href="'.$pageName.'&action=export&id='.$fID.'"> export results to excel </a><br /><br />';
	
	$body .= '
	<form action="'.$pageName.'" method="get" >
	<label class="radio">View Results by user:</label>
	<select class="" name="user">
	<option>-- Select --</option>';
	
	$query = "SELECT * FROM form_users WHERE fID='$fID'";
	$result = mysql_query($query);
	while($record = mysql_fetch_assoc($result)){
		$body .= '<option value="'.$record['id'].'">';
		$body .= $record['email'].' | '.$record['time']; 
		$body .= '</option>';
	}

	$body .= '
	</select>
	<input type="hidden" name="form" value="'.$fID.'" />
	<input type="hidden" name="action" value="view" />
	<input type="hidden" name="cmd" value="forms" />
	<input type="submit" value="go" />
	</form>
	';
	
	$qQuery = "SELECT * FROM form_questions WHERE fID='$fID' ORDER BY id ASC";
	$qResult = mysql_query($qQuery);
	
	$i = 1;
	while($qRecord = mysql_fetch_assoc($qResult)){
		$qID = $qRecord['id'];
		$question = $qRecord['question'];
		$type = $qRecord['type'];
		$height = $qRecord['height'];
		$width = $qRecord['width'];
		
		//add the question
		$body .= '<p class="poll-question" id="'.$qID.'">'.$i.'. '.$question.'</p>';
		if($type =='text' || $type =='textarea'){
			$body .= '<a class="view-responses" href="'.$pageName.'&action=view&question='.$qID.'">View Responses</a>';
		}elseif($type == 'check'){
			$query = "SELECT * FROM form_answers WHERE qID='$qID' ORDER BY id ASC";
			if(!$result = mysql_query($query)){
				die('cannot retrieve answer records'.mysql_error());
			}
			while($record = mysql_fetch_assoc($result)){
				
				$query = "SELECT id FROM form_results WHERE qID='$qID' AND answer='".$record['id']."'";
				$response = mysql_query($query);
				$count = mysql_num_rows($response);
				
				$body .= '<li class="answer"><span class="answer-left">'.$record['answer'].'</span> <span class="answer-right">'.$count.'</span><div class="clear"></div></li>'."\r\n";
			}
		}elseif($type == 'radio' || $type == 'dropdown'){
			$query = "SELECT * FROM form_answers WHERE qID='$qID' ORDER BY id ASC";
			if(!$result = mysql_query($query)){
				die('cannot retrieve answer records'.mysql_error());
			}
			
			$tQuery = "SELECT id FROM form_results WHERE qID='$qID'";
			$tResult = mysql_query($tQuery);
			$total = mysql_num_rows($tResult);
			$body .= ' <p class="total">Total: '.$total.'</p>';
			while($record = mysql_fetch_assoc($result)){
				
				$query = "SELECT id FROM form_results WHERE qID='$qID' AND answer='".$record['id']."'";
				$response = mysql_query($query);
				$rows = mysql_num_rows($response);
				
				$count = $rows/$total;
				$count = $count*100;
				$count = number_format($count, 2, '.', '');
				
				$body .= '<li class="answer"><span class="answer-left">'.$record['answer'].'</span> <span class="answer-right">'.$count.'% ('.$rows.')</span><div class="clear"></div></li>'."\r\n";
			}
		}
		$i++;
	}
	
	$prev = ''.$pageName.'&action=stats';

}else{
	$body .= '
	<form action="'.$pageName.'" method="get">
	<p><label>Edit a Form<br>
	'.get_forms_menu('form').'
	<input type="hidden" name="cmd" value="forms" /><input type="hidden" name="action" value="edit" /><input type="submit" class="side-button submit" value="Edit" style="margin-top:0;"></label></p>
	</form><br>

	
	<form action="'.$pageName.'" method="get">
	<p><label>View Form Results<br>
	'.get_forms_menu('form').'
	<input type="hidden" name="cmd" value="forms" /><input type="hidden" name="action" value="view" /><input type="submit" class="side-button submit" value="View" style="margin-top:0;"></label></p>
	</form><br>


	<form name="deletePoll" onSubmit="return confirm(\'Are you sure you want to delete this form?\');" action="'.$pageName.'&action=manage" method="post">
	<p><label>Delete a form<br>
	'.get_forms_menu('form').'
	<input type="hidden" name="cmd" value="deleteAction" /><input type="hidden" name="action" value="form" /><input type="submit" class="side-button delete" value="Delete" style="margin-top:0;"></label></p>
	</form>
	<br>
	<h3>Add a Form</h3>
	<form action="'.$pageName.'&action=manage" method="post">
	<label>Name:<br /><input type="text" class="text" name="name" value="'.$_POST['name'].'" /></label>
	<input type="hidden" name="cmd" value="addAction" /><input type="hidden" name="action" value="form" /><input class="submit" style="margin-top: 5px;" type="submit" value="Add">
	</form>
	';
}
?>

        
        <?php if($prev != ''){ echo '<p class="prev"><a href="'.$prev.'">Return to previous screen</a></p>'; } ?>
        <?php
        if($error != ''){
			echo '<p class="error" >'.stripslashes($error).'</p>';
		}
        if($msg != ''){
			echo '<p class="success" >'.stripslashes($msg).'</p>';
		}
		echo $body; ?>
        
        <script>
    CKEDITOR.replace( 'success_message', {
		
		<?php //if($siteID =='14' || $siteID =='5'){?>
		filebrowserBrowseUrl: '/filemanager/index.php?filemanager=1&site=|<?php echo $siteID; ?>|',
		<?php //} ?>

    });
	
	CKEDITOR.replace( 'instructions', {
		
		<?php //if($siteID =='14' || $siteID =='5'){?>
		filebrowserBrowseUrl: '/filemanager/index.php?filemanager=1&site=|<?php echo $siteID; ?>|',
		<?php //} ?>

    });
	
	CKEDITOR.replace( 'editableContent3', {

		<?php //if($siteID =='14' || $siteID =='5'){?>
		filebrowserBrowseUrl: '/filemanager/index.php?filemanager=1&site=|<?php echo $siteID; ?>|',
		<?php //} ?>

    });
	
</script>

        <div class="clear"></div>