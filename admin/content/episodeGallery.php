<div id="photo-gallery">
<h1>Manage Your Episode Gallery</h1>
<?php
if($allowed){
	
	if($_GET['action'] == 'edit' && is_numeric($_GET['id'])){
		$ID = clean($_GET['id']);
		$query = "SELECT * FROM videos WHERE ID='$ID' LIMIT 1";
		$result = mysql_query($query);
		$record = mysql_fetch_assoc($result);
		$ytURL = parse_gets($record['URL']);
		
		$body .= '
            <p class="prev"><a href="'.$pageName.'">Return to Previous Page</a></p>
			<h3>Edit a Episode</h3>
			<form id="video" action="'.$pageName.'" name="add" method="post" enctype="multipart/form-data">
				<label>Episode Number:<br />
				<input class="tiny-text" type="text" value="'.$record['episodeNumber'].'" name="episodeNumber" /></label>
				<label>Episode Name:<br />
				<input class="text" type="text" value="'.$record['episodeName'].'" name="episodeName" /></label>
				<label>YouTube URL:<span class="small">(i.e.http://www.youtube.com/watch?v=nH4k-eaD )</span><br />
				<input class="text" type="text" value="'.$record['URL'].'" name="URL" /></label>
				<label>Synopsis:<br />
				<textarea name="synopsis" class="textarea">'.$record['synopsis'].'</textarea></label>
				<label>Original Air Date:<br />
				<input type="text" class="short-text" value="'.$record['originalAir'].'" name="originalAir" /></label>
				<label>Next Air Date:<br />
				<input type="text" class="short-text" value="'.$record['reAir'].'" name="reAir" /></label>
				<input type="hidden" value="process" name="cmd" />
				<input class="submit" type="submit" value="Update" />
				<input type="hidden" name="cmd" value="editAction" />
				<input type="hidden" name="ID" value="'.$record['ID'].'" />
			</form>
			<h3 class="clear">Current Video</h3>
			<iframe width="560" height="349" src="http://www.youtube.com/embed/'.$ytURL['v'].'" frameborder="0" allowfullscreen></iframe>
		';
	}else{
		$body .= '
			<h3>Add a Episode</h3>
			<form id="image" action="'.$pageName.'" name="add" method="post" enctype="multipart/form-data">
				<label>Episode Number:<br />
				<input class="tiny-text" type="text" value="'.$_POST['episodeNumber'].'" name="episodeNumber" /></label>
				<label>Episode Name:<br />
				<input class="text" type="text" value="'.$_POST['episodeName'].'" name="episodeName" /></label>
				<label>YouTube URL:<span class="small">(i.e.http://www.youtube.com/watch?v=nH4k-eaD )</span><br />
				<input class="text" type="text" value="'.$_POST['URL'].'" name="URL" /></label>
				<label>Synopsis:<br />
				<textarea name="synopsis" class="textarea">'.$_POST['synopsis'].'</textarea></label>
				<label>Original Air Date:<br />
				<input type="text" class="short-text" value="'.$_POST['originalAir'].'" name="originalAir" /></label>
				<label>Next Air Date:<br />
				<input type="text" class="short-text" value="'.$_POST['reAir'].'" name="reAir" /></label>
				<input type="hidden" value="process" name="cmd" />
				<input title="Add a Episode." class="submit" type="submit" value="Add" />
				<input type="hidden" name="cmd" value="editAction" />
			</form>
			<h3>Manage Existing Videos</h3>
		';
		//start navOrder form
		
		$query = "SELECT * FROM videos WHERE home='0' ORDER BY episodeNumber ASC";
		if(!$result = mysql_query($query)){
			die(mysql_error());
		}
		while($record = mysql_fetch_assoc($result)){
			$ytURL = parse_gets($record['URL']);
			$body .= '
				<div class="gallery-block">
					<label class="sortorder">Episode #'.$record['episodeNumber'].'</label><p class="title">'.crop($record['episodeName'],25).'</p><input type="hidden" name="ID[]" value="'.$record['ID'].'">
					<div class="video"><img src="http://i.ytimg.com/vi/'.$ytURL['v'].'/0.jpg" /></div>
					<p class="caption">'.crop($record['synopsis'],100).'</p>
					<a class="submit edit" href="'.$pageName.'&action=edit&id='.$record['ID'].'">Edit</a> <a class="delete" onClick="return confirm(\'Are you sure you want to delete this video?\');" href="'.$pageName.'&action=delete&id='.$record['ID'].'">Delete</a>
				</div>
			';
		}
		if(mysql_num_rows($result)<1){
			$body .= '<p>You do not have any videos</p>';
		}else{
			$body .= '
			<div class="clear"></div>
			';
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