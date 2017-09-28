<?php
//Different pages available to user
if($_GET['action'] == 'manage'){
	$body .= '<label>Delete a Survey</label>
	<form name="deletePoll" onSubmit="return confirm(\'Are you sure you want to delete this survey?\');" action="'.$pageName.'&action=manage" method="post">
	'.get_polls_menu('poll').'
	<input type="hidden" name="cmd" value="deleteAction" /><input type="hidden" name="action" value="poll" /><input type="submit" class="side-button delete" value="Delete">
	</form>
	<label>Edit a Survey</label>
	<form action="'.$pageName.'" method="get">
	'.get_polls_menu('poll').'
	<input type="hidden" name="cmd" value="survey" /><input type="hidden" name="action" value="edit" /><input type="submit" class="side-button submit" value="Edit">
	</form>
	<h3>Add a Survey</h3>
	<form action="'.$pageName.'&action=manage" method="post">
	<label>Name:<br /><input type="text" class="text" name="name" value="'.$_POST['name'].'" /></label>
	<input type="hidden" name="cmd" value="addAction" /><input type="hidden" name="action" value="poll" /><input class="submit" style="margin-top: 5px;" type="submit" value="Add">
	</form>
	';
	
	$prev = ''.$pageName.'';
}elseif($_GET['action'] == 'edit' && is_numeric($_GET['poll'])){
	$poll = clean($_GET['poll']);
	
	$query = "SELECT name FROM polls WHERE id='$poll'";
	$result = mysql_query($query);
	$record = mysql_fetch_assoc($result);
	
	$name = $record['name'];
	
	$body .= '
	<form action="'.$pageName.'&action=edit&poll='.$poll.'" method="post">
	<label>Name:<br />
	<input type="text" class="text" name="name" value="'.$name.'" /></label>
	<input type="hidden" name="cmd" value="editAction" />
	<input type="hidden" name="action" value="poll" />
	<input type="hidden" name="id" value="'.$poll.'" />
	<input type="submit" class="submit" style="margin-top: 5px;" value="Update">
	</form>
	<h3>Questions</h3>
	<div id="manage-questions">';
	
	$query = "SELECT question, id, type FROM pollQuestions WHERE pID='$poll' ORDER BY sortOrder";
	$result = mysql_query($query);
	if(mysql_num_rows($result) == 0){
		$body .= 'No questions';
	}
	$i = 1;
	while($record = mysql_fetch_assoc($result)){
		$id = clean($record['id']);
		$question = clean($record['question']);
		
		$body .= '
		<div class="survey-question">'.$i.'. <a href="'.$pageName.'&action=question&id='.$id.'">'.$question.'</a>';
		if($record['type'] == 'radio' || $record['type'] == 'check' || $record['type'] == 'dropdown'){
			$body .= ' <a style="font-size: 10px;" href="'.$pageName.'&action=answers&qID='.$id.'">[MANAGE ANSWERS]</a>';
		}
		$body .= '
		<form name="deleteQuestion'.$id.'"  onSubmit="return confirm(\'Are you sure you want to delete this question?\');" style="display: inline;" action="'.$pageName.'&action=edit&poll='.$poll.'" method="post">
		<input type="hidden" name="id" value="'.$id.'" />
		<input type="hidden" name="cmd" value="deleteAction" />
		<input type="hidden" name="action" value="question" />
		<input style="font-size: 10px;" type="submit" value="DELETE" />
		</form></div>
		';
		$i++;
	}
	
	$body .= '
	</div>
	<h3>Add a Question</h3>
	<form action="'.$pageName.'&action=edit&poll='.$poll.'" method="post">
	<label>Question:<br />
	<input type="text" class="text" name="question" value="'.$_POST['question'].'" /></label>
	<label>Type:<br />
	<select class="dd" name="type">
		<option'; if($_POST['type'] == 'radio'){$body .= '  selected="selected"'; } $body .= ' onClick="showhide(\'size\', \'none\')" value="radio">radio</option>
		<option'; if($_POST['type'] == 'check'){$body .= '  selected="selected"'; } $body .= ' onClick="showhide(\'size\', \'none\')" value="check">check box</option>
		<option'; if($_POST['type'] == 'dropdown'){$body .= '  selected="selected"'; } $body .= ' onClick="showhide(\'size\', \'none\')" value="dropdown">dropdown</option>
		<option'; if($_POST['type'] == 'text'){$body .= '  selected="selected"'; } $body .= ' onClick="showhide(\'size\', \'none\')" value="text">short response</option>
		<option'; if($_POST['type'] == 'textarea'){$body .= '  selected="selected"'; } $body .= ' onClick="showhide(\'size\', \'block\')" value="textarea">long response</option>
	</select></label>
	<div id="size" style="'; if($_POST['type'] == 'textarea'){$body .= 'display:block;'; }else{ $body .= 'display:none;'; } $body .= ' margin-top: 10px;">
		<label class="radio" style="width: 310px;">Width: <input type="text" class="short-text" name="width" value="'.$_POST['width'].'" /></label>
		<label class="radio" style="width: 310px;">Height: <input type="text" class="short-text" name="height" value="'.$_POST['height'].'" /></label>
	</div>
	<input type="hidden" name="pID" value="'.$poll.'" />
	<input type="hidden" name="cmd" value="addAction" />
	<input type="hidden" name="action" value="question" />
	<input type="submit" class="submit" style="margin-top: 5px;" value="Submit">
	</form>
	';
	
	$prev = ''.$pageName.'&action=manage';
}elseif($_GET['action'] == 'question' && is_numeric($_GET['id'])){
	$id = clean($_GET['id']);
	
	$query = "SELECT * FROM pollQuestions WHERE id='$id' ORDER BY sortOrder";
	$result = mysql_query($query);
	$record = mysql_fetch_assoc($result);
	
	$pID = $record['pID'];
	
	$body .= '
	<form action="'.$pageName.'" method="post">
	<label>Question:<br />
	<input type="text" class="text" name="question" value="'.$record['question'].'"/></label>
	<label>Type:<br />
	<select class="dd" name="type">
		<option'; if($record['type'] == 'radio'){$body .= '  selected="selected"'; } $body .= ' onClick="showhide(\'size\', \'none\')" value="radio">radio</option>
		<option'; if($record['type'] == 'check'){$body .= '  selected="selected"'; } $body .= ' onClick="showhide(\'size\', \'none\')" value="check">check box</option>
		<option'; if($record['type'] == 'dropdown'){$body .= '  selected="selected"'; } $body .= ' onClick="showhide(\'size\', \'none\')" value="dropdown">dropdown</option>
		<option'; if($record['type'] == 'text'){$body .= '  selected="selected"'; } $body .= ' onClick="showhide(\'size\', \'none\')" value="text">short response</option>
		<option'; if($record['type'] == 'textarea'){$body .= '  selected="selected"'; } $body .= ' onClick="showhide(\'size\', \'block\')" value="textarea">long response</option>
	</select></label>
	<div id="size" style="'; if($record['type'] == 'textarea'){$body .= 'display:block;'; }else{ $body .= 'display:none;'; } $body .= ' margin-top: 10px;">
		<label class="radio" style="width: 310px;">Width: <input class="short-text" type="text" name="width" value="'.$record['width'].'" /></label>
		<label class="radio" style="width: 310px;">Height: <input class="short-text" type="text" name="height" value="'.$record['height'].'" /></label>
	</div>
	<input type="hidden" name="pID" value="'.$record['pID'].'" />
	<input type="hidden" name="id" value="'.$record['id'].'" />
	<input type="hidden" name="cmd" value="editAction" />
	<input type="hidden" name="action" value="question" />
	<input type="submit" class="submit" style="margin-top: 5px;" value="Update">
	</form>
	';
	
	$prev = ''.$pageName.'&action=edit&poll='.$pID;
}elseif($_GET['action'] == 'answers' && is_numeric($_GET['qID'])){
	$qID = clean($_GET['qID']);
	
	$qQuery = "SELECT question, pID FROM pollQuestions WHERE id='$qID' ORDER BY sortOrder";
	$qResult = mysql_query($qQuery);
	$qRecord = mysql_fetch_assoc($qResult);
	
	$pID = $qRecord['pID'];
	
	$question = $qRecord['question'];
	
	$body .= '<label>QUESTION: '.$question.'</label>
	<h3>Manage Options:</h3>';
	
	$query = "SELECT * FROM pollAnswers WHERE qID='$qID' ORDER BY sortOrder";
	$result = mysql_query($query);
	if(mysql_num_rows($result) == 0){
		$body .= '<p>No Options</p>';
	}
	$i = 1;
	while($record = mysql_fetch_assoc($result)){
		
		$body .= '
		'.$i.'. <form style="display: inline;" action="'.$pageName.'&action=answers&qID='.$qID.'" method="post">
			<input type="text" class="mid-text" name="answer" value="'.$record['answer'].'" />
			<input type="hidden" name="id" value="'.$record['id'].'" />
			<input type="hidden" name="qID" value="'.$qID.'" />
			<input type="hidden" name="cmd" value="editAction" />
			<input type="hidden" name="action" value="answer" />
		<input type="submit" class="side-button submit" value="Update">
		</form>
		<form name="deleteAnswer'.$record['id'].'" onSubmit="return confirm(\'Are you sure you want to delete this option?\');" style="display: inline;" action="'.$pageName.'&action=answers&qID='.$qID.'" method="post">
			<input type="hidden" name="id" value="'.$record['id'].'" />
			<input type="hidden" name="cmd" value="deleteAction" />
			<input type="hidden" name="action" value="answer" />
			<input type="submit" class="side-button delete" value="Delete">
		</form><br />
		';
		$i++;
	}
	$body .= '
	<h3>Add a Option</h3>
	<form action="'.$pageName.'&action=answers&qID='.$qID.'" method="post">
		<input type="text" class="text" name="answer" value="'.$_POST['answer'].'" />
		<input type="image" class="side-button" src="/images/button-add.gif" value="Add">
		<input type="hidden" name="qID" value="'.$qID.'" />
		<input type="hidden" name="cmd" value="addAction" />
		<input type="hidden" name="action" value="answer" />
	</form>
	';
	
	$prev = $pageName.'&action=edit&poll='.$pID;
}elseif($_GET['action'] == 'stats'){
	$body .= '
	<h3>View stats of a survey</h3>
	<form action="'.$pageName.'" method="get">
	'.get_polls_menu('poll').'
	<input type="hidden" name="cmd" value="survey" />
	<input type="hidden" name="action" value="view" />
	<input type="submit" class="submit" value="View">
	</form>
	';
	
	$prev = ''.$pageName.'';
	
}elseif($_GET['action'] == 'view' && is_numeric($_GET['question'])){
	$qID = clean($_GET['question']);
	$qQuery = "SELECT * FROM pollQuestions WHERE id='$qID' ORDER BY sortOrder";
	$qResult = mysql_query($qQuery);
	$qRecord = mysql_fetch_assoc($qResult);
	
	$question = $qRecord['question'];
	$type = $qRecord['type'];
	$height = $qRecord['height'];
	$width = $qRecord['width'];
	
	$pID = $qRecord['pID'];
	
	//add the question
	$body .= '<p class="poll-question" id="'.$qID.'">'.$question.'</p>';
	
	$query = "SELECT * FROM pollResults WHERE qID='$qID'";
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
	
	$prev = ''.$pageName.'&action=view&poll='.$pID;
}elseif($_GET['action'] == 'view' && is_numeric($_GET['user']) && is_numeric($_GET['poll'])){
	
	$uID = clean($_GET['user']);
	$pID = clean($_GET['poll']);
	
	$query = "SELECT * FROM pollUsers WHERE id='$uID'";
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
	
	$qQuery = "SELECT * FROM pollQuestions WHERE pID='$pID' ORDER BY sortOrder";
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
			$query = "SELECT answer FROM pollResults WHERE qID='$qID' AND uID='$uID'";
			if(!$result = mysql_query($query)){die(mysql_error());}
			
		if($type =='text' || $type =='textarea'){
			$record = mysql_fetch_assoc($result);
			$body .= '<li class="answer">'.$record['answer'].'</li>';
		}elseif($type == 'radio' || $type == 'dropdown'){
			$record = mysql_fetch_assoc($result);
			$aQuery = "SELECT * FROM pollAnswers WHERE id='".$record['answer']."' ORDER BY sortOrder";
			$aResult = mysql_query($aQuery);
			$aRecord = mysql_fetch_assoc($aResult);
			
			$body .= '<li class="answer"><span class="answer-left">'.$aRecord['answer'].'</span></li>'."\r\n";
		}elseif($type == 'check'){
			while($record = mysql_fetch_assoc($result)){
				$aQuery = "SELECT * FROM pollAnswers WHERE id='".$record['answer']."' ORDER BY sortOrder";
				$aResult = mysql_query($aQuery);
				$aRecord = mysql_fetch_assoc($aResult);
				
				$body .= '<li class="answer"><span class="answer-left">'.$aRecord['answer'].'</span></li>'."\r\n";
			}
		}
		$i++;
	}
	
	$prev = ''.$pageName.'&action=view&poll='.$pID;
}elseif($_GET['action'] == 'view' && is_numeric($_GET['poll'])){
	
	$pID = clean($_GET['poll']);
	
	$body .= '<a class="export" title="Export to Excel" href="'.$pageName.'&action=export&id='.$pID.'"><img src="/images/icon_download.png" /> export results</a><br /><br />';
	
	$body .= '
	<form action="'.$pageName.'" method="get" >
	<label class="radio">View Results by user:</label>
	<select class="" name="user">';
	
	$query = "SELECT * FROM pollUsers WHERE pID='$pID'";
	$result = mysql_query($query);
	while($record = mysql_fetch_assoc($result)){
		$body .= '<option value="'.$record['id'].'">';
		if($record['email'] != ''){
			$body .= $record['email'];
		}else{
			$body .= $record['IP']; 
		}
		$body .= '</option>';
	}

	$body .= '
	</select>
	<input type="hidden" name="poll" value="'.$pID.'" />
	<input type="hidden" name="action" value="view" />
	<input type="hidden" name="cmd" value="survey" />
	<input type="submit" value="go" />
	</form>
	';
	
	$qQuery = "SELECT * FROM pollQuestions WHERE pID='$pID' ORDER BY sortOrder";
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
			$query = "SELECT * FROM pollAnswers WHERE qID='$qID' ORDER BY sortOrder";
			if(!$result = mysql_query($query)){
				die('cannot retrieve answer records'.mysql_error());
			}
			while($record = mysql_fetch_assoc($result)){
				
				$query = "SELECT id FROM pollResults WHERE qID='$qID' AND answer='".$record['id']."'";
				$response = mysql_query($query);
				$count = mysql_num_rows($response);
				
				$body .= '<li class="answer"><span class="answer-left">'.$record['answer'].'</span> <span class="answer-right">'.$count.'</span><div class="clear"></div></li>'."\r\n";
			}
		}elseif($type == 'radio' || $type == 'dropdown'){
			$query = "SELECT * FROM pollAnswers WHERE qID='$qID' ORDER BY sortOrder";
			if(!$result = mysql_query($query)){
				die('cannot retrieve answer records'.mysql_error());
			}
			
			$tQuery = "SELECT id FROM pollResults WHERE qID='$qID'";
			$tResult = mysql_query($tQuery);
			$total = mysql_num_rows($tResult);
			$body .= ' <p class="total">Total: '.$total.'</p>';
			while($record = mysql_fetch_assoc($result)){
				
				$query = "SELECT id FROM pollResults WHERE qID='$qID' AND answer='".$record['id']."'";
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
	<div id="index-link">
	<div id="index-link-top-cap"></div>
	<a href="'.$pageName.'&action=manage">Manage your campaigns.</a>
	<div id="index-link-bot-cap"></div>
	</div>
	<div id="index-link">
	<div id="index-link-top-cap"></div>
	<a href="'.$pageName.'&action=stats">View statistics of your campaigns.</a>
	<div id="index-link-bot-cap"></div>
	</div>
	';
}
?>
        <h1>Manage Your Surveys:</h1>
        <?php if($prev != ''){ echo '<p class="prev"><a href="'.$prev.'">Return to previous screen</a></p>'; } ?>
        <?php
        if($error != ''){
			echo '<p class="error" >'.$error.'</p>';
		}
        if($msg != ''){
			echo '<p class="success" >'.$msg.'</p>';
		}
		echo $body; ?>
        <div class="clear"></div>