<script type="text/javascript">
	$(function(){

		// Tabs
		$('#tabs').tabs();
		$("#startDate").datepicker();
		$("#endDate").datepicker();
	});
</script>
<?php
$id = clean($_GET['id']);
$body .= '<h1>Manage Your Calendar of Events</h1>';
if($error != ''){ $body .= '<p class="error">'.$error.'</p>'; }
if($msg != ''){ $body .= '<p class="success">'.$msg.'</p>'; }
$pageFile = '/?cmd=eventCalendar';//so i can reuse this code... it's a bitch to rewrite

//reg field function
function reg_field($reg_field_val,$fieldID='0',$pageFile='/?cmd=eventCalendar'){
	$reg_field = '<li>
	<input type="text" class="mid-text" name="field[]" value="'.$reg_field_val['field'].'" style="color:#555;" onFocus="if(this.value == \'Label\'){ this.value = \'\';} this.style.color = \'#000\';" />
	<select name="type[]" class="dd">
		<option'; if($reg_field_val['type'] == 'text')  { $reg_field .= ' selected'; } $reg_field .= ' value="text">Text Box&nbsp;</option>
		<option'; if($reg_field_val['type'] == 'y/n')   { $reg_field .= ' selected'; } $reg_field .= ' value="y/n">Yes / No&nbsp;</option>
		<option'; if($reg_field_val['type'] == 'check') { $reg_field .= ' selected'; } $reg_field .= ' value="check">Checkbox&nbsp;</option>
	</select>
	<select name="required[]" class="dd">
		<option'; if($reg_field_val['required'] == '0') { $reg_field .= ' selected'; } $reg_field .= ' value="0">Optional&nbsp;</option>
		<option'; if($reg_field_val['required'] == '1') { $reg_field .= ' selected'; } $reg_field .= ' value="1">Required&nbsp;</option>
	</select>
	<input name="fieldID[]" type="hidden" value="'.$fieldID.'" />
	<input type="button" style="font-size: 12px; padding: 2px 5px;" value="remove" onclick="if(confirm(\'Are you sure you want to remove this?\')){this.parentNode.parentNode.removeChild(this.parentNode);'; if($fieldID != 0){ $reg_field .= ' window.open(\''.$pageFile.'&action=deleteField&id='.$fieldID.'\', \'killfield\', \'scrollbars=no,menubar=no,height=100,width=400,resizable=no,toolbar=no,status=no\');'; } $reg_field .= ' }" /></li>';
	return $reg_field;
}
if($show == 'users'){
	$body .= '<p class="prev"><a href="'.$pageFile.'">Return to Previous Page</a></p>';
	$body .= '<a class="export" href="'.$pageFile.'&action=export&id='.$id.'"><img src="/images/icon_download.png" /> download registered users</a>';
	$query = "SELECT name, id, date FROM reg_users WHERE eID=$id";
	$result = mysql_query($query);
	if(mysql_num_rows($result) == 0){
		$body .= "<p>There are no users registered for this even</p>";
	}else{
		//here goes all the options. Need to be able to export list to excel
		
		//then show all the registrations
		$body .= '<ul class="page-list">';
		while($record = mysql_fetch_assoc($result)){
			$body .= '<li><a href="'.$pageFile.'&action=user&id='.$record['id'].'&eID='.$id.'">'.crop($record['name'],35).' <span class="small">'.$record['date'].'</span></a></li>';
		}
		$body .= '</ul>';
	}
	
}elseif($show == 'user'){
	$eID = clean($_GET['eID']);

	$body .= '<p class="prev"><a href="'.$pageFile.'&action=users&id='.$eID.'">Return to Previous Page</a></p>';
	
	$query = "SELECT * FROM reg_users WHERE id=$id AND eID=$eID LIMIT 1";
	$result = mysql_query($query);
	$record = mysql_fetch_assoc($result);
	
	$body .= '<h4>'.$record['name'];
		if($record['paid'] != ''){
			$body .= ' <span class="small">Paid: '.$record['paid'].'</span>';
		}
	$body .= '</h4>';
	
	//get their responses
	$query = "SELECT * FROM reg_responses WHERE uID=$id AND eID=$eID";
	if(!$result = mysql_query($query)){
		die(mysql_error());
	}
	$response = array();
	while($record = mysql_fetch_assoc($result)){
		$fID = $record['fID'];
		$response[$fID] = $record['response'];
	}
	
	//get questions and show the answers
	$i = 1;
	$query = "SELECT id, content FROM reg_fields WHERE eID=$eID ORDER BY sortOrder";
	$result = mysql_query($query);
	while($record = mysql_fetch_assoc($result)){
		$body .= '
			<p class="question">'.$i.'. '.$record['content'].'</p>
			<p class="answer">'.$response[$record['id']].'</p>
		';
		$i++;
	}
	if($response['comments'] != ''){
		$body .= '
			<p class="question">'.$i.'. Comments / Quesions</p>
			<p class="answer">'.$response['comments'].'</p>
		';
	}
	
}elseif($show == 'list'){
	$page = clean($_GET['p']);
	if($page == ''){
		$page = 1;
	}
	$prev = $page-1;
	$next = $page+1;
	//default page, list all events
	$query = "SELECT id,title,startDate,endDate,register FROM eventCalendar";
	if(!$result = mysql_query($query)){
		die(mysql_error());
	}
	$count = mysql_num_rows($result);
	
	$show = 10;
	$pageCount = ceil($count/$show);
	
	$start = ($page-1) * $show;
	
	$query = $query." ORDER BY startDate DESC LIMIT $start,$show";
	if(!$result = mysql_query($query)){
		die($query.mysql_error());
	}
	
	if($pageCount > 1){
		$pagination .= '<div class="pagination">';
		if($page != 1){
			$pagination .= ' <a href="'.$pageFile.'&p='. $prev .'">prev</a> ';
		}
		$i=1;
		while($i<=$pageCount){
			if($i != $page){
				$pagination .= ' <a href="'.$pageFile.'"&p='.$i.'">';
			}
			$pagination .= $i;
			if($i != $page){
				$pagination .= '</a> ';
			}
			$i++;
		}
		if($page != $pageCount){
			$pagination .= ' <a href="'.$pageFile.'&p='. $next .'">next</a> ';
		}
		$pagination .= '</div>';
	}
	$body .= '
		<div id="tabs">
		<ul>
			<li><a href="#manage">Manage Existing Events</a></li>
			<li><a href="#add">Add a New Event</a></li>
		</ul>
		<div id="manage">
			<ul class="page-list">';
			while($record = mysql_fetch_assoc($result)){
				$body .= '
				<li><a class="mid" href="'.$pageFile.'&action=edit&id='.$record['id'].'">'.crop(stripslashes($record['title']),35).' <span class="small">'.cal_date($record['startDate']).' - '.cal_date($record['endDate']).'</span></a>';
				if($record['register'] == 1){ $body .= ' <a class="icon" href="'.$pageFile.'&action=users&item=view&id='.$record['id'].'" title="View Registered Users"><img style="margin-right: 13px;" width="20" class="icon" src="/images/icon_users.png" alt="Delete" /></a>'; }
				$body .= '</li>
				';
			}
			$body .= '</ul>';
			
			$body = $body.$pagination;
			
			//get todays date and preload it into the date fields
			if($_POST['startdate'] == '' && $_POST['endDate'] == ''){
				$date = date("Y/m/d");
				$_POST['startDate'] = $date;
				$_POST['endDate'] = $date;
			}
			
			$reg_field_val = array('field' => 'Label', 'type' => 'text', 'required' => '0');
			
			$body .= '
			<div id="readroot" style="display: none;">
			'.reg_field($reg_field_val).'
		</div>
	</div>
	<div id="add">
		<h2>Add an Event</h2>
		<form name="form" class="site" action="'.$pageFile.'" method="post" enctype="multipart/form-data">
			<label>Title:<br />
			<input type="text" name="title" class="text" value="'.stripslashes($_POST['title']).'" /></label>
			<label>Subhead:<br />
			<input type="text" name="subhead" class="text" value="'.stripslashes($_POST['subhead']).'" /></label>
			<label class="radio" style="width: 300px;">Start Date: <input type="text" name="startDate" id="startDate" value="'.cal_date($_POST['startDate']).'" /></label>
			<label class="radio" style="width: 300px;">End Date: <input type="text" name="endDate" id="endDate" value="'.cal_date($_POST['endDate']).'" /></label><br />
			<label style=" display:inline-block; width: 100px;">Recurring?</label> <label class="radio">yes <input type="radio" onclick="isrecurring();" name="recurring" '; if($_POST['recurring'] == 'yes'){ $body .= 'checked ';} $body .= 'value="yes" /></label> <label class="radio">no <input type="radio" onclick="isntrecurring();" name="recurring" '; if($_POST['recurring'] != 'yes'){ $body .= 'checked ';} $body .= 'value="no" /></label>
			<div id="recurring"'; if($_POST['recurring'] != 'yes'){ $body .= 'style="display: none;" ';} $body .= '>
			<label style=" display:inline-block; width: 100px;">Frequency?</label> <label class="radio">Daily <input type="radio" name="frequency" '; if($_POST['frequency'] != 'monthly' && $_POST['frequency'] != 'weekly'){ $body .= 'checked ';} $body .= 'value="daily" /></label> <label class="radio">Weekly <input type="radio" name="frequency" '; if($_POST['frequency'] == 'weekly'){ $body .= 'checked ';} $body .= 'value="weekly" /></label> <label class="radio" disabled>Monthly <input type="radio" name="frequency" '; if($_POST['frequency'] == 'monthly'){ $body .= 'checked ';} $body .= 'value="monthly" /></label>
			</div>
			<label>Time: <input type="text" name="time" class="short-text" value="'.$_POST['time'].'" /> <span class="small">(as it will appear on the site)</span></label>
			<label>Price: $<input type="text" name="price" class="short-text" value="'.$_POST['price'].'"'; if($_POST['free'] == '1'){ $body.= ' disabled'; } $body .=' /> <span class="small">(USD, optional)</span> <input type="checkbox" name="free"'; if($_POST['free'] == '1'){ $body.= ' checked'; } $body .=' onclick="freetoggle();" value="1"> FREE </label>
			<label>Location:<br />
			<input type="text" name="location" class="text" value="'.$_POST['location'].'" /></label>
			<label style=" display:inline-block; width: 400px;">Show the info button?</label> <label class="radio">yes <input type="radio" name="showinfo" '; if($_POST['showinfo'] != '0'){ $body .= 'checked ';} $body .= 'value="1" /></label> <label class="radio">no <input type="radio" name="showinfo" '; if($_POST['showinfo'] == '0'){ $body .= 'checked ';} $body .= 'value="0" /></label>
			<label>Content:<br />
			<textarea name="pageContent" id="pageContent">'.stripslashes($_POST['pageContent']).'</textarea></label>
			<label style=" display:inline-block; width: 400px;">Is this event active?</label> <label class="radio">yes <input type="radio" name="active" '; if($_POST['active'] != '0'){ $body .= 'checked ';} $body .= 'value="1" /></label> <label class="radio">no <input type="radio" name="active" '; if($_POST['active'] == '0'){ $body .= 'checked ';} $body .= 'value="0" /></label>
			<label>URL: <span class="small">(must begin with http:// I.E. http://google.com)</span><br />
			<input type="text" name="url" class="text" value="'.$_POST['url'].'" /></label>
			<label>Contact email: <span class="small">(user@domain.com)</span><br />
			<input type="text" name="email" class="text" value="'.$_POST['email'].'" /></label>
			<label>Image associated with post:<br />
			<input type="file" id="image" name="image" /></label>
			<label id="file">PDF or Word Doc to be associated with post:<br />
			<input type="file" id="file" name="file" /></label>';
		$body .= '
			<input type="hidden" name="action" value="add" />
			<input type="hidden" name="id" value="'.$_POST['id'].'" />
			';
			if($registrationAllowed){
				$body .= '
					<label style=" display:inline-block; width: 400px;">Can users register for this event online?</label> <label class="radio">yes <input type="radio" name="registration" onclick="regYes();" '; if($_POST['registration'] == '1'){ $body .= 'checked ';} $body .= 'value="1" /></label> <label class="radio">no <input type="radio" name="registration" onclick="regNo();" '; if($_POST['registration'] != '1'){ $body .= 'checked ';} $body .= 'value="0" /></label>
					<div id="registration"'; if($_POST['registration'] != '1') { $body .= ' style="display: none;"'; } $body .= '>
					<p>Here you can create a custom registration form for your event. Every form will require a name, everything else is up to you. More help can be found by clicking "need help?" below the navigation on the left side of your screen. Click the "Add Field" button below to add to the registration form.</p>
					<ol id="fields">';
					if($_POST['field']){
						foreach($_POST['field'] as $key => $value){
							$reg_field_val = array('field' => $value, 'type' => $_POST['type'][$key], 'required' => $_POST['required'][$key]);
							$body .= reg_field($reg_field_val);
						}
					}
					$body .= '
					<span id="writeroot"></span>
					</ol>
					<input class="button" type="button" value="Add Field" onclick="moreFields();"/>&nbsp;&nbsp;
					<label style="width: 400px;">Add comments / concerns box?</label> <label class="radio">yes <input type="radio" name="comments" '; if($_POST['comments'] == '1'){ $body .= 'checked ';} $body .= 'value="1" /></label> <label class="radio">no <input type="radio" name="comments" '; if($_POST['comments'] != '1'){ $body .= 'checked ';} $body .= 'value="0" /></label>
					<label>Payment Instructions: <span class="small">(optional)</span><br />
					<textarea id="metaDesc" class="textarea" name="paymentInstructions">'.$_POST['paymentInstructions'].'</textarea></label>
					</div><br />
					<div class="clear"></div>
				';
			}
			$body .= '
			<input type="submit" class="submit" value="Submit" />
		</form>
	</div>
	';
}elseif($show == 'edit'){
	if($error == ''){
		$query = "SELECT * FROM eventCalendar WHERE id='$id'";
		if(!$result = mysql_query($query)){
			die($query.mysql_error());
		}
		$record = mysql_fetch_assoc($result);
	}else{
		$record = $_POST;
		$record['text'] = stripslashes($_POST['pageContent']);
		$record['register'] = $_POST['registration'];
	}
	
	$reg_field_val = array('field' => 'Label', 'type' => 'text', 'required' => '0');
	
	$body .= '
    <p class="prev"><a href="'.$pageFile.'">Return to Previous Page</a></p>
	<div id="readroot" style="display: none;">
	'.reg_field($reg_field_val).'
	</div>
	<h2>Edit an Event</h2>
	<form name="form" class="site" action="'.$pageFile.'" method="post" enctype="multipart/form-data">
		<label>Title:<br />
		<input type="text" name="title" class="text" value="'.stripslashes($record['title']).'" /></label>
		<label>Subhead:<br />
		<input type="text" name="subhead" class="text" value="'.stripslashes($record['subhead']).'" /></label>
		<label class="radio" style="width: 300px;">Start Date: <input type="text" name="startDate" id="startDate" value="'.cal_date($record['startDate']).'" /></label>
		<label class="radio" style="width: 300px;">End Date: <input type="text" name="endDate" id="endDate" value="'.cal_date($record['endDate']).'" /></label><br />
		<label style=" display:inline-block; width: 100px;">Recurring?</label> <label class="radio">yes <input type="radio" onclick="isrecurring();" name="recurring" '; if($record['recurring'] == 'yes'){ $body .= 'checked ';} $body .= 'value="yes" /></label> <label class="radio">no <input type="radio" onclick="isntrecurring();" name="recurring" '; if($record['recurring'] != 'yes'){ $body .= 'checked ';} $body .= 'value="no" /></label>
		<div id="recurring"'; if($record['recurring'] != 'yes'){ $body .= 'style="display: none;" ';} $body .= '>
		<label style=" display:inline-block; width: 100px;">Frequency?</label> <label class="radio">Daily <input type="radio" name="frequency" '; if($record['frequency'] != 'monthly' && $record['frequency'] != 'weekly'){ $body .= 'checked ';} $body .= 'value="daily" /></label> <label class="radio">Weekly <input type="radio" name="frequency" '; if($record['frequency'] == 'weekly'){ $body .= 'checked ';} $body .= 'value="weekly" /></label> <label class="radio" disabled>Monthly <input type="radio" name="frequency" '; if($record['frequency'] == 'monthly'){ $body .= 'checked ';} $body .= 'value="monthly" /></label>
		</div>
		<label>Time: <input type="text" name="time" class="short-text" value="'.$record['time'].'" /> <span class="small">(as it will appear on the site)</span></label>
		<label>Price: $<input type="text" name="price" class="short-text" value="'.$record['price'].'"'; if($record['free'] == '1'){ $body.= ' disabled'; } $body .=' /> <span class="small">(USD, optional)</span> <input type="checkbox" name="free"'; if($record['free'] == '1'){ $body.= ' checked'; } $body .=' onclick="freetoggle();" value="1"> FREE </label>
		<label>Location:<br />
		<input type="text" name="location" class="text" value="'.$record['location'].'" /></label>
		<label style=" display:inline-block; width: 400px;">Show the info button?</label> <label class="radio">yes <input type="radio" name="showinfo" '; if($record['showinfo'] != '0'){ $body .= 'checked ';} $body .= 'value="1" /></label> <label class="radio">no <input type="radio" name="showinfo" '; if($record['showinfo'] == '0'){ $body .= 'checked ';} $body .= 'value="0" /></label>
		<label>Content:<br />
		<textarea name="pageContent" id="pageContent">'.stripslashes($record['text']).'</textarea></label>
		<label style=" display:inline-block; width: 400px;">Is this event active?</label> <label class="radio">yes <input type="radio" name="active" '; if($record['active'] != '0'){ $body .= 'checked ';} $body .= 'value="1" /></label> <label class="radio">no <input type="radio" name="active" '; if($record['active'] == '0'){ $body .= 'checked ';} $body .= 'value="0" /></label>
		<label>URL: <span class="small">(must begin with http:// I.E. http://google.com)</span><br />
		<input type="text" name="url" class="text" value="'.$record['url'].'" /></label>
		<label>Contact email: <span class="small">(user@domain.com)</span><br />
		<input type="text" name="email" class="text" value="'.$record['email'].'" /></label>
		<label>Image associated with post:<br />
		<input type="file" id="image" name="image" /></label>'; if($record['image'] != ''){ $body.='<img src="'.$site.'/calendar/'.$record['image'].'" style="max-width:100%;" /><br><a class="small" onClick="return confirm(\'Are you sure you want to delete this image?\');" href="'.$pageFile.'&action=deleteImage&id='.$record['id'].'">Delete the image currently associated with this event</a>';} $body.='
		<label id="file">PDF or Word Doc to be associated with post:<br />
		<input type="file" id="file" name="file" /></label>'; if($record['file'] != ''){ $body.='<a class="small" onClick="return confirm(\'Are you sure you want to delete this file?\');" href="'.$pageFile.'&action=deleteFile&id='.$record['id'].'">Delete the file currently associated with this event</a>';}
	$body .= '
		<input type="hidden" name="action" value="add" />
		<input type="hidden" name="id" value="'.$record['id'].'" />
		';
		if($registrationAllowed == '1' ){
			$q = "SELECT * FROM reg_fields WHERE eID=".$record['id']." ORDER BY sortOrder";
			$res = mysql_query($q);
			$body .= '
				<label style=" display:inline-block; width: 400px;">Can users register for this event online?</label> <label class="radio">yes <input type="radio" name="registration" onclick="regYes();" '; if($record['register'] == '1'){ $body .= 'checked ';} $body .= 'value="1" /></label> <label class="radio">no <input type="radio" name="registration" onclick="regNo();" '; if($record['register'] != '1'){ $body .= 'checked ';} $body .= 'value="0" /></label>
				<div id="registration"'; if($record['register'] != '1') { $body .= ' style="display: none;"'; } $body .= '>
				<p>Here you can create a custom registration form for your event. Every form will require a name, everything else is up to you. More help can be found by clicking "need help?" below the navigation on the left side of your screen. Click the "Add Field" button below to add to the registration form.</p>
				<ol id="fields">';
				while($rec = mysql_fetch_assoc($res)){
					$reg_field_val = array('field' => $rec['content'], 'type' => $rec['type'], 'required' => $rec['required']);
					$body .= reg_field($reg_field_val,$rec['id']);
				}
				$body .= '
				<span id="writeroot"></span>
				</ol>
				<input class="" type="button" value="Add Field" onclick="moreFields();"/>&nbsp;&nbsp;
				<label style="width: 400px;">Add comments / concerns box?</label> <label class="radio">yes <input type="radio" name="comments" '; if($record['comments'] == '1'){ $body .= 'checked ';} $body .= 'value="1" /></label> <label class="radio">no <input type="radio" name="comments" '; if($record['comments'] != '1'){ $body .= 'checked ';} $body .= 'value="0" /></label>
                <label>Payment Instructions: <span class="small">(optional)</span><br />
                <textarea id="metaDesc" class="textarea" name="paymentInstructions">'.$record['paymentInstructions'].'</textarea></label>
				</div>
				<div class="clear"></div>
			';
		}
		$body .= '
		<input type="hidden" name="action" value="edit" />
		<input type="hidden" name="id" value="'.$record['id'].'" />
		<input type="submit" class="submit" value="Submit" />
	</form>
	<form name="deleteSite" id="deleteSite" action="'.$pageFile.'" onSubmit="return confirm(\'Are you sure you want to delete this item?\');" method="post">
		<input type="hidden" name="id" value="'.$record['id'].'"/>
		<input type="hidden" name="action" value="delete" />
		<input type="submit" class="delete button-fix" value="Delete" id="page-delete" />
	</form>
	';
	
}else{
	$redirect = TRUE;
}
if($redirect){
	header('Location: '.$pageFile);
}
echo $body;


?>

    <script>
    CKEDITOR.replace( 'pageContent', {

		<?php //if($siteID =='14' || $siteID =='5'){?>
		filebrowserBrowseUrl: '/filemanager/index.php?filemanager=1&site=|<?php echo $siteID; ?>|',
		<?php //} ?>
		
    });
	</script>
    
<script>
//enable / diable a form item
function isrecurring(){
		setVisibility('recurring', 'block');
		//document.form.endDate.disabled = true;
}
//enable / diable a form item
function isntrecurring(){
		setVisibility('recurring', 'none');
		//document.form.endDate.disabled = false;
}
//enable / diable a form item
function freetoggle(){
		if(document.form.free.checked){
			document.form.price.disabled = true;
		}else{
			document.form.price.disabled = false;
		}
}
function regYes(){
		setVisibility('registration', 'block');
}
function regNo(){
		setVisibility('registration', 'none');
}
function clearQuestion(){
	if(this.value == 'Label'){
		this.value = '';
		this.style.color = '#000';
	}
}
</script>
<?php /*
//update old databases with this 
ALTER TABLE `eventCalendar` ADD `recurring` CHAR( 3 ) NOT NULL ,
ADD `frequency` VARCHAR( 7 ) NOT NULL ,
ADD `price` DECIMAL( 5, 2 ) NOT NULL ,
ADD `free` INT( 1 ) NOT NULL ,
ADD `location` VARCHAR( 225 ) NOT NULL ,
ADD `email` VARCHAR( 225 ) NOT NULL ,
ADD `image` VARCHAR( 225 ) NOT NULL ,
ADD `register` INT( 1 ) NOT NULL ,
ADD `comments` INT( 1 ) NOT NULL ,
ADD `paymentInstructions` TEXT NOT NULL ;

//create table with this
CREATE TABLE `eventCalendar` (
  `id` int(225) NOT NULL auto_increment,
  `startDate` varchar(10) NOT NULL default '',
  `endDate` varchar(10) NOT NULL default '',
  `time` varchar(25) NOT NULL default '',
  `title` varchar(225) NOT NULL default '',
  `text` longtext NOT NULL,
  `url` varchar(225) NOT NULL default '',
  `file` varchar(225) NOT NULL default '',
  `active` int(1) NOT NULL default '1',
  `recurring` char(3) NOT NULL default '',
  `frequency` varchar(7) NOT NULL default '',
  `price` decimal(5,2) NOT NULL default '0.00',
  `free` int(1) NOT NULL default '0',
  `location` varchar(225) NOT NULL default '',
  `email` varchar(225) NOT NULL default '',
  `image` varchar(225) NOT NULL default '',
  `register` int(1) NOT NULL default '0',
  `comments` int(1) NOT NULL default '0',
  `paymentInstructions` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=44 ;
*/ ?>