<script type="text/javascript">
	$(function(){

		// Tabs
		$('#tabs').tabs();
		
		$(".sortable").sortable();

	});
</script>

<?php echo '<!--'; print_r($_POST); echo '-->'; ?>

<div id="photo-gallery">
<h1>Manage Your Video Gallery</h1>
<?php
if($allowed){
	
	if($_GET['action'] == 'edit' && is_numeric($_GET['id'])){
		$id = clean($_GET['id']);
		$query = "SELECT * FROM videos WHERE id='$id' LIMIT 1";
		$result = mysql_query($query);
		$record = mysql_fetch_assoc($result);
		
		$body .= '
            <p class="prev"><a href="'.$pageName.'">Return to Previous Page</a></p>
			<h3>Edit a Video</h3>
			<form id="video" action="'.$pageName.'" name="add" method="post" enctype="multipart/form-data">
				<label>YouTube Code:<span class="small">(i.e.http://www.youtube.com/watch?v=<strong>*this*</strong> )</span><br />
				<input class="text" type="text" value="'.$record['url'].'" name="url" /></label>
				<label>Title:<br />
				<input class="text" type="text" value="'.$record['title'].'" name="title" /></label>
				<label>Description:<br />
				<textarea name="description" class="textarea">'.$record['description'].'</textarea></label>
				<label>Runtime:<span class="small">(MM:SS)</span><br />
				<input type="text" class="short-text" value="'.$record['time'].'" name="time" /></label>';
				
				if($hasCategories){
					$body .= '<label>Category:<br /><select class="dd" name="category">';
					foreach($categories as $key => $value){
						$body .= '<option value="'.$key.'"'; if($key == $record['category']){$body .=' selected'; } $body .= ' >'.$value.'&nbsp;</option>'."\r\n";
					}
					$body .= '</select></label>';
				}
					
				$body .= '<input type="hidden" value="process" name="cmd" />
				<input title="Update the video." class="button" type="image" src="/images/button-update.gif" value="Update" />
				<input type="hidden" name="cmd" value="editAction" />
				<input type="hidden" name="id" value="'.$record['id'].'" />
			</form>
			<h3 class="clear">Current Video</h3>
			<iframe width="560" height="349" src="http://www.youtube.com/embed/'.$record['url'].'" frameborder="0" allowfullscreen></iframe>
		';
	}else{
			$body .= '<div id="tabs">
            <ul>
                <li><a href="#manage">Manage Existing Videos</a></li>
                <li><a href="#add">Add a New Video</a></li>';
                if($hasCategories){
                    $body .= '<li><a href="#categories">Manage Your Categories</a></li>';
                }
            $body .= '</ul>';
            $body .= '<div id="manage">';
            if($hasCategories){
                $body .= '
                <form name="catSwitch">
                <label>Filter by Category:
                <input type="hidden" name="cmd" value="videoGallery" />
                <select name="category" class="dd" onchange="document.catSwitch.submit()">
                <option value="">View all&nbsp;</option>
                ';
                foreach($categories as $key=>$value){
                    $body .= '<option value="'.$key.'"'; if($_GET['category'] == $key){ $body .= ' selected'; } $body .= '>'.$value.'&nbsp;</option>'."\r\n";
                }
                $body .= '
                </select></label>
                </form>
                <p>Drag and drop the videos and click "Update Sort Order" to change the order they display in within their category.</p>
                ';
            }else{
				$body .= '<p>Drag and drop the videos and click "Update Sort Order" to change the order they display in.</p>';
			}
            
            //start sortOrder form
            
			
			if($hasCategories && !($_GET['category'] && is_numeric($_GET['category']))){
				$catQuery = "SELECT * FROM videoCategories ORDER BY sortOrder ASC";
				$catResult = mysql_query($catQuery);
            	while($catRecord = mysql_fetch_assoc($catResult)){
					$catID = $catRecord['id'];
					$catSort = $catRecord['id'];					
					$query = "SELECT * FROM videos WHERE category=$catSort ORDER BY navOrder ASC";
										
					$body .= '<h3>'.$catRecord['name'].'</h3>
						     <form id="sortorder'.$catID.'" action="'.$pageName.'" name="sortOrder'.$catID.'" method="post" enctype="multipart/form-data">
							 <ul class="sortable">';
	
					$result = mysql_query($query);
					while($record = mysql_fetch_assoc($result)){
						$body .= '
							<li class="gallery-block">
							<p class="featured">'; if($record['featured'] == 1){ $body.='<strong>FEATURED</strong>'; }else{ $body.='<a href="'.$pageName.'&action=feature&id='.$record['id'].'">Make featured</a>'; } $body .='</p>';
							$body .='<label class="sortorder"><input title="Once finished, click \'Update Sort Order\' below." type="hidden" class="sortorder" name="sortorder[]" value="'.$record['id'].'"></label><p class="title">'.crop($record['title'],40).'</p>
								<div class="video"><img src="http://i.ytimg.com/vi/'.$record['url'].'/0.jpg" /></div>
								<p class="caption short">'.crop($record['description'],55).'</p>
								<a class="submit" href="'.$pageName.'&action=edit&id='.$record['id'].'">Edit</a> <a class="delete" onClick="return confirm(\'Are you sure you want to delete this video?\');" href="'.$pageName.'&action=delete&id='.$record['id'].'">Delete</a>
							</li>
						';
					}
					$body .= '</ul>';
					if(mysql_num_rows($result)<1){
					$body .= '<p>You do not have any photos in this category.</p>';
					}else{
						$body .= '
						<div class="clear"></div>
						<input title="The images will be reordered in ascending order." type="submit" class="submit" value="Update Sort Order" />
						<input type="hidden" name="catsort" value="'.$catID.'" />
						<input type="hidden" name="cmd" value="sortOrder" />
						</form>
						<div class="clear"></div>
						';
					}
				}
            }else{
				$body .= '<form id="sortorder" action="'.$pageName.'" name="navOrder" method="post" enctype="multipart/form-data">';
				if($_GET['category'] && is_numeric($_GET['category'])){
					$cat = ($_GET['category']);
					$query = "SELECT * FROM videos WHERE category=$cat ORDER BY";
				}else{
					$query = "SELECT * FROM videos ORDER BY";
				}
				if($hasCategories){
					$query .= " category,";
				}
				$query .= " navOrder ASC";
				
				$body .= '<ul class="sortable">';
				//$body .= '<ul>';
				$result = mysql_query($query);
				while($record = mysql_fetch_assoc($result)){
					$body .= '
						<li class="gallery-block">
						<p class="featured">'; if($record['featured'] == 1){ $body.='<strong>FEATURED</strong>'; }else{ $body.='<a href="'.$pageName.'&action=feature&id='.$record['id'].'">Make featured</a>'; } $body .='</p>';
						
						if($hasCategories){
							$body .= '<p class="title"> '.crop($categories[$record['category']],35).'</p>';
						}
						$body .='<label class="sortorder"><input title="Once finished, click \'Update Sort Order\' at bottom of page." type="hidden" class="sortorder" name="sortorder[]" value="'.$record['id'].'"></label><p class="title">'.crop($record['title'],40).'</p>
							<div class="video"><img src="http://i.ytimg.com/vi/'.$record['url'].'/0.jpg" /></div>
							<p class="caption short">'.crop($record['description'],55).'</p>
							<a class="submit" href="'.$pageName.'&action=edit&id='.$record['id'].'">Edit</a> <a class="delete" onClick="return confirm(\'Are you sure you want to delete this video?\');" href="'.$pageName.'&action=delete&id='.$record['id'].'">Delete</a>
						</li>
					';
				}
				$body .= '</ul>';
			
				if(mysql_num_rows($result)<1){
					$body .= '<p>You do not have any Videos</p>';
				}else{
					$body .= '
					<div class="clear"></div>
					<input title="The images will be reordered in ascending order." type="submit" class="submit" value="Update Sort Order" />
					<input type="hidden" name="cmd" value="sortOrder" />
					';
				}
				
			}
            //end sort form
            $body .= '</form>';
            $body .= '</div>';
            
            $body .= '<div id="add">';
			$body .= '
			<h3>Add a Video</h3>
			<form id="image" action="'.$pageName.'" name="add" method="post" enctype="multipart/form-data">
				<label>YouTube Code:<span class="small">(i.e.http://www.youtube.com/watch?v=<strong>*this*</strong> )</span><br />
				<input class="text" type="text" value="'.$_POST['url'].'" name="url" /></label>
				<label>Title:<br />
				<input class="text" type="text" value="'.$_POST['title'].'" name="title" /></label>
				<label>Description:<br />
				<textarea name="description" class="textarea">'.$_POST['description'].'</textarea></label>
				<label>Runtime:<span class="small">(MM:SS)</span><br />
				<input type="text" class="short-text" value="'.$_POST['time'].'" name="time" /></label>';
				
				if($hasCategories){
					$body .= '<label>Category:<br /><select class="dd" name="category">';
					foreach($categories as $key => $value){
						$body .= '<option value="'.$key.'"'; if($key == $_POST['category']){$body .=' selected'; } $body .= ' >'.$value.'&nbsp;</option>'."\r\n";
					}
					$body .= '</select></label>';
				}
					
				$body .= '<input type="hidden" value="process" name="cmd" />
				<input class="submit" type="submit" value="Add" />
				<input type="hidden" name="cmd" value="editAction" />
			</form>
			</div>
		';
		if($hasCategories){
			$body .= '<div id="categories">';
			$query = "SELECT * FROM videoCategories";
			$result = mysql_query($query);
			$body .= '
				<h3>Add a Category</h3>
				<form action="'.$pageName.'" method="post">
					<label>Category: <input type="text" class="mid-text" name="name" value="'.$_POST['name'].'"/><input type="hidden" name="cmd" value="categories">
					<input type="submit" class="submit side-button" value="Add">
					</label>
				</form>
				<h3>Edit Current Categories</h3>
			';
			while($record = mysql_fetch_assoc($result)){
				$body .= '
				<form action="'.$pageName.'" method="post">
					<label><input type="text" class="mid-text" name="name" value="'.$record['name'].'"/><input type="hidden" name="cmd" value="categories"><input type="hidden" name="id" value="'.$record['id'].'">
					<input type="submit" class="submit side-button" value="Update">
					<a class="delete" onClick="return confirm(\'Are you sure you want to delete this category?\');" href="'.$pageName.'&action=deleteCategory&id='.$record['id'].'">Delete</a>
					</label>
				</form>
				';
			}
			$body .= '</div>';
		}
	}
	
}else{
	$body .= '<p>You have no CMS pages on your site, please <a href="/?cmd='.$cmsPages[0].'">click here</a> to continue.</p>';
}

if($error != ''){
	echo '<p class="error">'.$error.'</p>';
}
if($msg != ''){
	echo '<p class="success">'.$msg.'</p>';
}
echo $body;
?>

<!-- javascript coding -->
<script>
// execute your scripts when the DOM is ready. this is a good habit
$(function() {
$("#navOrder :input").tooltip({

	// place tooltip
	position: "bottom",

	// a little tweaking of the position
	offset: [10, -210],

	// use the built-in fadeIn/fadeOut effect
	effect: "fade",

	// custom opacity setting
	opacity: 0.8

});
});
</script>
</div>