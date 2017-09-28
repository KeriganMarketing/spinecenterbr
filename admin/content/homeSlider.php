<div id="photo-gallery">
<script type="text/javascript">
	$(function(){
		$('#tabs').tabs();
		$("#sortable").sortable();
	});
</script>
<?php
		
if($allowed){
	if($_GET['action'] == 'edit' && is_numeric($_GET['id'])){
		$id = clean($_GET['id']);
		$query = "SELECT * FROM home_slideshow WHERE id='$id' LIMIT 1";
		$result = mysql_query($query);
		$record = mysql_fetch_assoc($result);
		
		$body .= '
		<div id="tabs">
            <ul>
                <li><a href="#manage">Edit a Slide</a></li>
			</ul>
			<div id="edit">
            <p class="prev"><a href="'.$pageName.'">Return to Previous Page</a></p>
			
			<form id="image" action="'.$pageName.'" name="add" method="post" enctype="multipart/form-data">
				<label id="file">Upload a New Image: <span class="small"><br />(must be a .jpg or .png, leave blank to keep the photo the same. Maximum file size of 6MB.)</span><br />
				<input title="Browse your computer for a new photo." type="file" class="text" name="imagefile" id="filename" /></label><br />';
				
				if($hasTitle){
				$body .= '<label>Title: <span class="small">(press Shift+Enter to make a single line break)</span><br />
				<textarea id="title" name="title" title="Please enter the title of the slide.">'.stripslashes($record['title']).'</textarea></label>';
				}
				
				if($hasName){
				$body .= '<label>Title: <br />
				<input type="text" id="name" name="shorttitle" title="This is the text on the Read more button" value="'.$record['shorttitle'].'" style="width:200px;padding:5px;font-size:14px;" /></label>';
				}
				
				if($hasCopy){
					$body .= '<label id="caption">Caption: <span class="small">(press Shift+Enter to make a single line break)</span><br />
				<textarea id="copy" name="copy" title="Please enter the caption for the slide.">'.stripslashes($record['copy']).'</textarea></label>
				';
				}
				
				if($hasLink){
					$body .= '<label id="link">Link: <span class="small">( Start with http:// for external websites. For pages within your site,  use this format: "/parent_page/sub_page" OPTIONAL )</span><br /><input type="text" id="link" name="link" title="Should this slide link somewhere?" value="'.$record['link'].'" style="width:98%;padding:5px;font-size:14px;" /></label>';
				}
				
				if($hasLinkText){
					$body .= '<label id="linktext">Link Text: <span class="small">( Text inside the button, usually Learn More, Read More, etc )</span><br /><input type="text" id="linktext" name="linktext" title="This is the text on the Read more button" value="'.$record['linktext'].'" style="width:200px;padding:5px;font-size:14px;" /></label>';
				}
				
				$body .= '<br />
<input title="Update the slide." class="submit" type="submit" value="Update" />
				<input type="hidden" name="cmd" value="editAction" />
				<input type="hidden" name="id" value="'.$record['id'].'" />
			</form>
			<h3 class="clear">Current Image</h3>
			<img class="current-image" src="'.$site.'/images/slider/'.$record['img'].'"/>
			</div>
		';
	}else{
		$body .= '
            <div id="tabs">
            <ul>
                <li><a href="#manage">Manage Existing Slides</a></li>
                <li><a href="#add">Add a New Slide</a></li>
			</ul>
			';
			
		$body .= '
			<div id="add">
			<form id="image" action="'.$pageName.'" name="add" method="post" enctype="multipart/form-data">
				<label id="file">Upload an Image: <span class="small">(must be a .jpg or .png. Maximum file size of 6MB.)</span><br />
				<input title="Browse your computer for a new photo." type="file" class="text" name="imagefile" id="filename" /></label><br />';
		
				if($hasTitle){
					$body .= '<label>Title: <span class="small">(press Shift+Enter to make a single line break)</span><br />
					<textarea id="title" name="title" title="Please enter the title of the slide.">'.stripslashes($_POST['title']).'</textarea></label>';
				}
				
				if($hasName){
					$body .= '<label>Title: <br />
					<input type="text" id="name" name="shortTitle" title="This is the text on the Read more button" value="'.$_POST['shorttitle'].'" style="width:200px;padding:5px;font-size:14px;" /></label>';
				}
				
				if($hasCopy){
					$body .= '<label id="caption">Caption: <span class="small">(press Shift+Enter to make a single line break)</span><br />
					<textarea id="copy" name="copy" title="Please enter the caption for the slide.">'.stripslashes($_POST['copy']).'</textarea></label>';
				}
				
				if($hasLink){
					$body .= '<label id="caption">Link: <span class="small">( Start with http:// for external websites. For pages within your site,  use this format: "/parent_page/sub_page" OPTIONAL )</span><br />
					<input type="text" id="link" name="link" title="Should this lside link somewhere?" value="'.$_POST['link'].'" style="width:98%;padding:5px;font-size:14px;"  /></label>';
				}
				
				if($hasLinkText){
					$body .= '<label id="caption">Link Text: <span class="small">( Text inside the button, usually Learn More, Read More, etc  )</span><br /><input type="text" id="linktext" name="linktext" title="This is the text on the Read more button" value="'.$_POST['linktext'].'" style="width:200px;padding:5px;font-size:14px;" /></label>';
				}
				
				$body .= '<br />
<input title="Create the slide." class="submit" type="submit" value="Submit" />
				<input type="hidden" name="cmd" value="editAction" />
			</form>
			</div>
			
		';
		
		//start sortOrder form
		$body .= '<div id="manage">
		<p>Drag and drop to change their order.</p>
		<form id="sortorder" action="'.$pageName.'" name="navOrder" method="post" enctype="multipart/form-data">';
		$body .= '<ul id="sortable">';
		//$body .= '<ul>';
		$query = "SELECT * FROM home_slideshow ORDER BY sortOrder";
		$result = mysql_query($query);
		while($record = mysql_fetch_assoc($result)){
			$body .= '
				<li class="gallery-block" style="padding-bottom:46px;">';
				if($hasCategories){
					$body .= '<p class="title"> '.crop($categories[$record['category']],16).'</p>';
				}
				$body .='<label class="sortorder"><input type="hidden" class="sortorder" name="sortorder[]" value="'.$record['id'].'"></label><p class="title">'.crop(strip_tags($record['title']),18).'</p><input type="hidden" name="id[]" value="'.$record['id'].'">
					<div class="image"><img src="'.$site.'/images/slider/'.$record['img'].'" /></div>
					<p class="caption">'.crop(strip_tags($record['copy']),70).' <span style="color:#6491B8; font-size:11px;">'.crop(strip_tags($record['link']),35).'</span></p>
					
					<a class="submit" href="'.$pageName.'&action=edit&id='.$record['id'].'">Edit</a> <a class="delete" onClick="return confirm(\'Are you sure you want to delete this photo?\');" href="'.$pageName.'&action=delete&id='.$record['id'].'">Delete</a>
				</li>
			';
		}
		if(mysql_num_rows($result)<1){
			$body .= '<p>You do not have any Slides</p>';
		}else{
			$body .= '
			<div class="clear"></div>
			<input type="submit" class="submit" value="Update Sort Order" />
			<input type="hidden" name="cmd" value="sortOrder" />
			';
		}
		//end sort form
		$body .= '</form></div></div>';
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
		//GET THE WYSISYG 

	?>
    </div>
    <script>
	<?php if($hasTitle){ ?>
    CKEDITOR.replace( 'title', {
		height: '80',
		toolbar: [
			[ 'Source', '-', 'Cut','Copy','Paste','PasteText','PasteFromWord', 'SpellChecker', 'Scayt' ],
			[ 'Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat' ],
			'/',
			[ 'Format','FontSize' ]
		]
    });
	<?php } if($hasCopy){?>
	CKEDITOR.replace( 'copy', {
		height: '150',
		toolbar: [
			[ 'Source', '-', 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt' ],
			[ 'Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat' ],
			'/',
			[ 'Bold','Italic','Underline','Strike','-','Subscript','Superscript' ],
			[ 'NumberedList','BulletedList' ],
			[ 'Link','Unlink','Anchor','Format','FontSize' ]
		]
    });
	<?php } ?>
	</script>

<!-- javascript coding -->
<script>
// execute your scripts when the DOM is ready. this is a good habit
$(function() {
$("#sortorder :input").tooltip({

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