<?php
$id = $_GET['id'];
$body .= '<h1>Manage Your Calendar of Events</h1>';
if($error != ''){ $body .= '<p class="error">'.$error.'</p>'; }
if($msg != ''){ $body .= '<p class="success">'.$msg.'</p>'; }
$pageFile = '/?cmd=eventCalendar';//so i can reuse this code... it's a bitch to rewrite
if($show == 'list'){
	$page = clean($_GET['p']);
	if($page == ''){
		$page = 1;
	}
	$prev = $page-1;
	$next = $page+1;
	//default page, list all events
	$query = "SELECT id,title FROM eventCalendar";
	if(!$result = mysql_query($query)){
		die(mysql_error());
	}
	$count = mysql_num_rows($result);
	
	$show = 10;
	$pageCount = ceil($count/$show);
	
	$start = ($page-1) * $show;
	
	$query = $query." ORDER BY startDate ASC LIMIT $start,$show";
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
	$body .= '<ul class="page-list">';
	while($record = mysql_fetch_assoc($result)){
		$body .= '
		<li><a href="'.$pageFile.'&action=edit&id='.$record['id'].'">'.$record['title'].'</a></li>
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
	
	$body .= $wysiwyg.'
	<h2>Add an Event</h2>
	<form class="site" action="'.$pageFile.'" method="post" enctype="multipart/form-data">
		<label>Title:<br />
		<input type="text" name="title" class="text" value="'.$_POST['title'].'" /></label>
		<label class="radio" style="width: 300px;">Start Date: <input type="date" name="startDate" value="'.cal_date($_POST['startDate']).'" /></label>
		<label class="radio" style="width: 300px;">End Date: <input type="date" name="endDate" value="'.cal_date($_POST['endDate']).'" /></label>
		<label>Time: <input type="text" name="time" class="short-text" value="'.$_POST['time'].'" /></label>
		<label>Content:<br />
		<textarea name="pageContent" id="pageContent">'.stripslashes($_POST['pageContent']).'</textarea></label>
		<label style=" display:inline-block; width: 400px;">Is this event active?</label> <label class="radio">yes <input type="radio" name="active" '; if($_POST['active'] != '0'){ $body .= 'checked ';} $body .= 'value="1" /></label> <label class="radio">no <input type="radio" name="active" '; if($_POST['active'] == '0'){ $body .= 'checked ';} $body .= 'value="0" /></label>
		<label>URL: <span class="small">(must begin with http:// I.E. http://google.com)</span><br />
		<input type="text" name="url" class="text" value="'.$_POST['url'].'" /></label>
		<label id="file">PDF or Word Doc to be associated with post:<br />
		<input type="file" id="file" name="file" /></label>';
	$body .= '
		<input type="hidden" name="action" value="add" />
		<input type="hidden" name="id" value="'.$_POST['id'].'" />
		<input type="image" src="/images/button-submit.gif" class="button" />
	</form>';
	
}elseif($show == 'edit'){
	
	$query = "SELECT * FROM eventCalendar WHERE id='$id'";
	if(!$result = mysql_query($query)){
		die($query.mysql_error());
	}
	$record = mysql_fetch_assoc($result);
	
	$body .= $wysiwyg.'
    <p class="prev"><a href="'.$pageFile.'">Return to Previous Page</a></p>
	<h3>Edit an Event</h3>
	<form class="site" action="'.$pageFile.'" method="post" enctype="multipart/form-data">
		<label>Title:<br />
		<input type="text" name="title" class="text" value="'.$record['title'].'" /></label>
		<label class="radio" style="width: 300px;">Start Date: <input type="date" name="startDate" value="'.cal_date($record['startDate']).'" /></label>
		<label class="radio" style="width: 300px;">End Date: <input type="date" name="endDate" value="'.cal_date($record['endDate']).'" /></label></label>
		<label>Time: <input type="text" name="time" class="short-text" value="'.$record['time'].'" /></label>
		<label>Content:<br />
		<textarea name="pageContent" id="pageContent">'.$record['text'].'</textarea></label>
		<label style=" display:inline-block; width: 400px;">Is this event active?</label> <label class="radio">yes <input type="radio" name="active" '; if($record['active'] != '0'){ $body .= 'checked ';} $body .= 'value="1" /></label> <label class="radio">no <input type="radio" name="active" '; if($record['active'] == '0'){ $body .= 'checked ';} $body .= 'value="0" /></label>
		<label>URL: <span class="small">(must begin with http:// I.E. http://google.com)</span><br />
		<input type="text" name="url" class="text" value="'.$record['url'].'" /></label>';
	$body .= '
		<input type="hidden" name="action" value="edit" />
		<input type="hidden" name="id" value="'.$record['id'].'" />
		<label>PDF or Word Doc to be associated with post: <span class="small">(leave blank to keep the file the same)</span><br />
		<input type="file" id="file" name="file" /></label><label class="radio" style="width: 200px;">'; if($record['file'] != ''){ $body.='<a class="small" onClick="return confirm(\'Are you sure you want to delete this file?\');" href="'.$pageFile.'&action=deleteFile&id='.$record['id'].'">Delete the file currently associated with this event';} $body.='</label>
		<input type="image" src="/images/button-submit.gif" class="button" />
	</form>
	<form name="deleteSite" id="deleteSite" action="'.$pageFile.'" onSubmit="return confirm(\'Are you sure you want to delete this page?\');" method="post">
		<input type="hidden" name="id" value="'.$record['id'].'"/>
		<input type="hidden" name="action" value="delete" />
		<input type="image" src="/images/button-delete.gif" class="button" id="page-delete" />
	</form>
	';
	
}else{
	$redirect = TRUE;
}
if($redirect){
	header('Location: '.$pageFile);
}
echo $body;


//GET THE WYSISYG 
// Include the CKEditor class.
include_once "editor/ckeditor.php";

// Create a class instance.
$CKEditor = new CKEditor();

// Path to the CKEditor directory.
$CKEditor->basePath = '/editor/';

$CKEditor->config['filebrowserBrowseUrl'] = '/editor/filemanager/browser/default/browser.html?Connector=/editor/filemanager/connectors/php/connector.php';
$CKEditor->config['filebrowserImageBrowseUrl'] = '/editor/filemanager/browser/default/browser.html?Type=Image&Connector=/editor/filemanager/connectors/php/connector.php';
$CKEditor->config['filebrowserWindowWidth'] = '800';
$CKEditor->config['filebrowserWindowHeight'] = '600';

//settings
$CKEditor->config['toolbar'] = array(
	array( 'Source' ),
	array( 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt' ),
	array( 'Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat' ),
	'/',
	array( 'Bold','Italic','Underline','Strike','-','Subscript','Superscript' ),
	array( 'NumberedList','BulletedList','-','Outdent','Indent' ),
	array( 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock' ),
	array( 'Link','Unlink','Anchor' ),
	'/',
	array( 'Image','Table','HorizontalRule','SpecialChar' ),
	array( 'Format','Font','FontSize' ),
	array( 'TextColor' )
);

$CKEditor->config['skin'] = 'office2003';

$CKEditor->config['height'] = 400;

// Replace a textarea element with an id (or name) of "textarea_id".
$CKEditor->replace("pageContent");

?>
<script>
	$(":date").dateinput({
		
		//turn on month/year selector
		selectors: true, 
	
		// this is displayed to the user
		format: 'mm/dd/yyyy',
	});
</script>