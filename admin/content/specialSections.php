<div id="pages">
	<script type="text/javascript">
        $(function(){
            $('#tabs').tabs();
        });
		
    </script>
    

<?php
if($allowed){
	if($_GET['action'] == 'edit' && is_numeric($_GET['id'])){
		$body .= '<p class="prev"><a href="'.$pageBase.'">Return to Previous Page</a></p>';
		$body .= '<h3>Edit a Shared Section</h3>';
		
		$id = clean($_GET['id']);
		$query = "SELECT * FROM specialSections WHERE ID='$id' LIMIT 1";
		$result = mysql_query($query);
		$record = mysql_fetch_assoc($result);
		
		$body .= '
			<form action="'.$pageBase.'" method="post" enctype="multipart/form-data" >
				<label>Name:<br />
				<input type="text" class="text" name="name" value="'.$record['name'].'"/></label>
				<input type="hidden" name="type" value ="'.$record['type'].'" />';
				
				
				if($record['type'] == 'text') {
				$body .= '<div id="type1" >
					<label>Text:</label>
					<input type="text" class="text" name="text" value="'.$record['content'].'"/></label>
				</div>';
				}elseif($record['type'] == 'htmltext'){
				$body .= '<div id="type2" >
					<label>Content:</label>
					<textarea name="pageContent">'.stripslashes($record['content']).'</textarea>
				</div>';
				}elseif($record['type'] == 'image'){
				$body .= '<div id="type3" >
					<label id="file">Image: <span class="small">(must be a jpg or png. Maximum file size of 6MB.)</span><br />
					<input title="Browse your computer for a new photo." type="file" name="image" id="filename" /></label>
					<p>Current Photo:</p>
					<img src="'.$site.'/images/uploads/'.$record['content'].'" style="max-width:740px;" />
				</div>';
				}elseif($record['type'] == 'link'){
				$body .= '<div id="type4" >
					<label>Link URL:</label>
					<input type="text" class="text" name="link[]" value="'.$record['content']['1'].'"/></label>
					<label>Link Text:</label>
					<input type="text" class="text" name="link[]" value="'.$record['content']['2'].'"/></label>
				</div>';
				}
				$body .= '<div class="clear"></div>
				<input type="hidden" name="action" value="process" />
				<input type="hidden" name="id" value="'.$id.'" />
                <input type="submit" class="submit" value="Submit" />
			</form>
			<a onclick="return confirm(\'Are you sure you want to delete this section?\')" href="'.$pageBase.'&action=delete&id='.$record['ID'].'" class="delete button-fix">Delete</a>
		';
	
	}else{
		//show clients
		$query = "SELECT * FROM specialSections";
		$result = mysql_query($query);
		
		$body .= '
		 <div id="tabs">
                <ul>
                    <li><a href="#manage">Manage Shared Sections</a></li>';
					if($isSuperUser){
                    $body .= '<li><a href="#add">Add a New Shared Section</a></li>';
					}
                $body .= '</ul>
                <div id="manage">
		<p class="directions">Below are editable sections of your website template usually shared in several places for easy management. Click on any item below to edit it.</p>
		<ul class="page-list">
		';
		
		while($record = mysql_fetch_assoc($result)){
			$body .= '<li><a title="Edit this shared section" href="'.$pageBase.'&action=edit&id='.$record['ID'].'">'.$record['name'].' (ID: '.$record['ID'].')</a></li>'."\r\n";
		}
		
		$body .= '</ul></div>';
		if($isSuperUser){
		$body .= '<div id="add">';
		
		$body .= '
			<form action="'.$pageBase.'" method="post" enctype="multipart/form-data">
			<div id="section-top" >
				<label>Name:<br />
				<input type="text" class="text" name="name" value="'.$_POST['name'].'"/></label>
				<label>Section type:</label> 
				<select name="type" title="What type of section is this?" class="dd" id="swap" onChange="swapsectiontype();">
					<option value="text" '; if($_POST['type'] == 'text'){ $body .= 'selected ';} $body .= '/>Plain Text</option> 
					<option value="htmltext" '; if($_POST['type'] == 'htmltext'){ $body .= 'selected ';} $body .= '/>HTML Text</option>
					<option value="image" '; if($_POST['type'] == 'image'){ $body .= 'selected ';} $body .= '/>Image</option> 
					<option value="link" '; if($_POST['type'] == 'type'){ $body .= 'selected ';} $body .= '/>Link</option></select></div>';
				
				/*$body .= '<div id="textdiv" >
					<label>Text:</label>
					<input type="text" class="text" name="text" value=""/></label>
				</div>';
				
				$body .= '<div id="htmltextdiv" >
					<label>Content:</label>
					<textarea name="pageContent"></textarea>
				</div>';
				
				$body .= '<div id="imagediv" >
					<label id="file">Image: <span class="small">(must be a jpg or png. Maximum file size of 6MB.)</span><br />
					<input title="Browse your computer for a new photo." type="file" name="image" id="filename" /></label>
				</div>';
				
				$body .= '<div id="linkdiv" >
					<label>Link URL:</label>
					<input type="text" class="text" name="link[]" value=""/></label>
					<label>Link Text:</label>
					<input type="text" class="text" name="link[]" value=""/></label>
				</div>';*/
				
				$body .= '
				<input type="hidden" name="action" value="process" />
                <input type="submit" class="submit" value="Submit" />
			</form>
			</div>
		';
		}
	}
	echo $body;
	
}else{
	echo '<p>You have no shared sections for your site.</p>';
}

	//GET THE WYSISYG 
	?>
    </div>
    <script>
    CKEDITOR.replace( 'pageContent', {
		
		filebrowserBrowseUrl: '/filemanager/index.php?filemanager=1&site=|<?php echo $siteID; ?>|',

    });
	</script>
    
</div>