<script type="text/javascript">
	$(function(){
		$('#tabs').tabs();
	});
</script>

<?php
$id = clean($_GET['id']);
$action = $_GET['action'];

//$body .= '<h1>Manage Your Downloadable forms</h1>';
if($error != ''){ $body .= '<p class="error">'.$error.'</p>'; }
if($msg != ''){ $body .= '<p class="success">'.$msg.'</p>'; }
$pageFile = '/?cmd=downloadableForms';//so i can reuse this code... it's a bitch to rewrite


	
if($_GET['action'] == 'categories' || $_GET['action'] == 'deleteCategory'){
	$query = "SELECT * FROM downloadCategories";
	$result = mysql_query($query);
		
	$body .= '
		<p class="prev"><a href="'.$pageFile.'">Return to Previous Page</a></p>
		<h3>Add a Category</h3>
		<form action="'.$pageFile.'&action=categories" method="post">
			<label>Category: <input type="text" class="mid-text" name="name" value="'.$_POST['name'].'"/><input type="hidden" name="action" value="category">
			<input type="submit" class="submit" value="Add">
			</label>
		</form>
		<h3>Edit Current Categories</h3>
	';
	while($record = mysql_fetch_assoc($result)){
		$body .= '
		<form action="'.$pageFile.'&action=categories" method="post">
			<label><input type="text" class="mid-text" name="name" value="'.$record['name'].'"/><input type="hidden" name="action" value="category"><input type="hidden" name="id" value="'.$record['id'].'">
			<input type="submit" class="submit" value="Update">
			<a class="delete" onClick="return confirm(\'Are you sure you want to delete this category?\');" href="'.$pageFile.'&action=deleteCategory&id='.$record['id'].'">Delete</a>
			</label>
		</form>
		';
	}

}elseif($id != ''){ //they want to see an item

	$query = "SELECT * FROM downloads WHERE id='$id' LIMIT 1";
	$result = mysql_query($query);
	$record = mysql_fetch_assoc($result);
	
		
	$body .= '
		<form action="'.$pageFile.'" method="post" enctype="multipart/form-data">
			<label>Title<br />
			<input type="text" class="text" name="title" value="'.$record['title'].'" /></label>
			<label>Description <span class="small">(optional)</span><br />
			<textarea name="description" class="textarea" id="pageContent">'.stripslashes($record['description']).'</textarea></label>';
			if($hasCategories){
				$body .= '<label>Category:<br /><select class="dd" name="category">';
				foreach($categories as $key => $value){
					$body .= '<option value="'.$key.'"'; if($key == $record['category']){$body .=' selected'; } $body .= ' >'.$value.'&nbsp;</option>'."\r\n";
				}
				$body .= '</select></label>';
			}
			$body .= '
			<label>File: <span class="small">(leave blank to keep the same) <a href="'.$site.'/downloads/'.$record['file'].'" target="_blank">view current file</a></span><br />
			<input type="file" name="file" /></label>
			<input type="hidden" name="id" value="'.$record['id'].'" />
			<input type="hidden" name="action" value="edit" />
			<input type="submit" class="submit" value="Submit" />
		</form>
		<form name="deleteSite" id="deleteSite" action="'.$pageFile.'" onSubmit="return confirm(\'Are you sure you want to delete this item?\');" method="post">
			<input type="hidden" name="id" value="'.$record['id'].'"/>
			<input type="hidden" name="action" value="delete" />
			<input type="submit" class="delete button-fix" value="Delete" id="page-delete" />
		</form>
	';
	
}elseif($action == 'sort'){
	
}else{
	
	$body .= '<div id="tabs">
		<ul>
			<li><a href="#add">Add New Download</a></li>
			<li><a href="#manage">Manage Existing Downloads</a></li>
		</ul>
		<div id="manage">';
		
	if($hasCategories){
		$body .= '<ul class="page-list"><li><a class="photoCategories" href="'.$pageFile.'&action=categories">Manage your categories</a></li></ul>';
	}
	
	
	$query = "SELECT * FROM downloads ORDER BY date DESC";
	$result = mysql_query($query);
	
	$body .= '<ul class="page-list">';
	while($record = mysql_fetch_assoc($result)){
		$body .= '
		<li><a class="mid" href="'.$pageFile.'&id='.$record['id'].'">'.crop($record['title'],35).' <span class="small">'.cal_date($record['date']).'</span></a></li>
		';
	}
	$body .= '</ul></div>';
	
	$body .= '
		<div id="add">
		<form action="'.$pageFile.'" method="post" enctype="multipart/form-data">
			<label>Title<br />
			<input type="text" class="text" name="title" value="'.$_POST['title'].'" /></label>
			<label>Description <span class="small">(optional)</span><br />
			<textarea name="description" class="textarea" id="pageContent">'.stripslashes($_POST['description']).'</textarea></label>';
			if($hasCategories){
				$body .= '<label>Category:<br /><select class="dd" name="category">';
				foreach($categories as $key => $value){
					$body .= '<option value="'.$key.'"'; if($key == $_POST['category']){$body .=' selected'; } $body .= ' >'.$value.'&nbsp;</option>'."\r\n";
				}
				$body .= '</select></label>';
			}
			
			$body .= '
			<label>File: <span class="small">(leave blank to keep the same)</span><br />
			<input type="file" name="file" /></label>
			<input type="hidden" name="action" value="add" />
			<input type="submit" class="submit" value="Submit" />
		</form>
		</div>
	';
}


echo $body;


?>

    <script>
    CKEDITOR.replace( 'pageContent', {
		
		filebrowserBrowseUrl: '/filemanager/index.php?filemanager=1&site=|<?php echo $siteID; ?>|',

    });
	</script>