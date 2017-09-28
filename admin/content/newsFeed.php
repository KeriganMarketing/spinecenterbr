<script type="text/javascript">
	$(function(){

		// Tabs
		$('#tabs').tabs();
		$("#datePosted").datepicker();
		$("#dateExp").datepicker();
	});
</script>
<?php
$id = $_GET['id'];
$body .= '<h1>Manage Your News Feed</h1>';
if($error != ''){ $body .= '<p class="error">'.$error.'</p>'; }
if($msg != ''){ $body .= '<p class="success">'.$msg.'</p>'; }
$pageFile = '/?cmd=newsFeed';//so i can reuse this code
if($show == 'list'){
	$page = clean($_GET['p']);
	if($page == ''){
		$page = 1;
	}
	$prev = $page-1;
	$next = $page+1;
	//default page, list all events
	$query = "SELECT id,title,date FROM newsFeed";
	if(!$result = mysql_query($query)){
		die(mysql_error());
	}
	$count = mysql_num_rows($result);
	
	$show = 10;
	$pageCount = ceil($count/$show);
	
	$start = ($page-1) * $show;
	
	$query = $query." ORDER BY date DESC LIMIT $start,$show";
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
				$pagination .= ' <a href="'.$pageFile.'&p='.$i.'">';
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
	
	$body .= '<div id="tabs">
		<ul>
			<li><a href="#add">Add News Post</a></li>
			<li><a href="#manage">Manage Existing News Posts</a></li>
		</ul>
		<div id="manage">';
		
	$body .= '<ul class="page-list">';
	while($record = mysql_fetch_assoc($result)){
		$body .= '
		<li><a href="'.$pageFile.'&action=edit&id='.$record['id'].'">'.$record['title'].' - '.cal_date($record['date']).'</a></li>
		';
	}
	$body .= '</ul>';
	
	$body = $body.$pagination.'</div>';
	
	//get todays date and preload it into the date fields
	if($_POST['date'] == '' && $_POST['exp'] == ''){
		$date = date("Y/m/d");
		$_POST['date'] = $date;
		$_POST['exp'] = $date;
	}
	if($_POST['urlText'] == ''){
		$_POST['urlText'] = 'Click for more information';
	}
	
	$body .= '
	<div id="add">
	<form class="site" action="'.$pageFile.'" method="post" enctype="multipart/form-data">
		<label>Title:<br />
		<input type="text" name="title" class="text" value="'.$_POST['title'].'" /></label>
		<label class="radio" style="width: 300px;">Post Date: <input id="datePosted" type="text" name="date" value="'.cal_date($_POST['date']).'" /></label>
		<label class="radio" style="width: 300px;">Expiration Date: <input id="dateExp" type="text" name="exp" value="'.cal_date($_POST['exp']).'" /></label>
		<label>Content:<br />
		<textarea name="pageContent" id="pageContent">'.stripslashes($_POST['pageContent']).'</textarea></label>
		<label>Web Site: <span class="small">(must begin with http:// I.E. http://google.com)</span><br />
		<input type="text" name="url" class="text" value="'.$_POST['url'].'" /></label>
		<label>Text associated with the link: <br />
		<input type="text" name="urlText" class="text" value="'.$_POST['urlText'].'" /></label>
		<label id="file">PDF or Word Doc to be associated with post:<br />
		<input type="file" id="file" name="file" /></label>
		<input type="hidden" name="action" value="add" />
		<input type="submit" class="submit" value="Submit" />
	</form>';
	
}elseif($show == 'edit'){
	
	$query = "SELECT * FROM newsFeed WHERE id='$id'";
	if(!$result = mysql_query($query)){
		die($query.mysql_error());
	}
	$record = mysql_fetch_assoc($result);
	
	$body .= '
    <p class="prev"><a href="'.$pageFile.'">Return to Previous Page</a></p>
	<h3>Edit an Event</h3>
	<form class="site" action="'.$pageFile.'" method="post" enctype="multipart/form-data">
		<label>Title:<br />
		<input type="text" name="title" class="text" value="'.$record['title'].'" /></label>
		<label class="radio" style="width: 300px;">Post Date: <input id="datePosted" type="text" name="date" value="'.cal_date($record['date']).'" /></label>
		<label class="radio" style="width: 300px;">Expiration Date: <input id="dateExp" type="text" name="exp" value="'.cal_date($record['exp']).'" /></label>
		<label>Content:<br />
		<textarea name="pageContent" id="pageContent">'.stripslashes($record['content']).'</textarea></label>
		<label>Web Site: <span class="small">(must begin with http:// I.E. http://google.com)</span><br />
		<input type="text" name="url" class="text" value="'.$record['url'].'" /></label>
		<label>Text associated with the link: <br />
		<input type="text" name="urlText" class="text" value="'.$record['urlText'].'" /></label>
		<label id="file">PDF or Word Doc to be associated with post:<br />
		<input type="file" id="file" name="file" /></label><label class="radio" style="width: 200px;">'; if($record['file'] != ''){ $body.='<a class="small" onClick="return confirm(\'Are you sure you want to delete this file?\');" href="'.$pageFile.'&action=deleteFile&id='.$record['id'].'">Delete the file currently associated with this item';} $body.='</label>
		<input type="hidden" name="action" value="edit" />
		<input type="hidden" name="id" value="'.$record['id'].'"/>
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


//GET THE WYSISYG 
	?>
    </div>
    <script>
    CKEDITOR.replace( 'pageContent', {
		
		<?php //if($siteID =='14' || $siteID =='5'){?>
		filebrowserBrowseUrl: '/filemanager/index.php?filemanager=1&site=|<?php echo $siteID; ?>|',
		<?php //} ?>

    });

	$(":date").dateinput({
		
		//turn on month/year selector
		selectors: true, 
	
		// this is displayed to the user
		format: 'mm/dd/yyyy',
	});
</script>