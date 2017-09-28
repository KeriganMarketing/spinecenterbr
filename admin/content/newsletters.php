<script type="text/javascript">
	$(function(){
		// Tabs
		$('#tabs').tabs();
		$("#datePosted").datepicker({ dateFormat: 'yy-mm-dd' }).val();
		//$(".sortable").sortable();
	});
</script>
<div id="photo-gallery">


<div id="pages">
<?php
//ini_set('display_errors', 'On');
if($allowed){
	if($_GET['action'] == 'edit' && is_numeric($_GET['id'])){
		$body .= '<p class="prev"><a href="'.$pageBase.'">Return to Previous Page</a></p>';
		$body .= '<h3>Edit a Newsletter</h3>';
		
		$id = clean($_GET['id']);
		$query = "SELECT * FROM newsletters WHERE id='$id' LIMIT 1";
		$result = mysql_query($query);
		$record = mysql_fetch_assoc($result);
		
		$body .= '
			<form action="'.$pageBase.'" method="post" enctype="multipart/form-data">';
			
				$body .= '<label>Newsletter Name:<br />
				<input type="text" class="text" name="name" value="'.$record['name'].'"/></label>';
				
				if($isOnline){
					$body .= '<label>Website / Url: <span class="small">Optional. If external website, begin with "http://" (optional)</span>
				<input type="text" class="text" name="url" value="'.$record['url'].'"/></label>';
				}
				
				$body .= '<label>Date Posted:<br />
				<input id="datePosted" type="text" name="datePosted" class="date" value="'.$record['datePosted'].'"/></label>
				';
				
				if($isPrinted){
					$body .= '
					<label id="file">Upload Newsletter File <span class="small">(must be a PDF or Word doc. Maximum file size of 6MB.)</span><br />
					<input title="Browse your computer for a new photo." type="file" name="image" id="filename" /></label>
					<label>Current Newsletter: <a href="'.$site.'/images/uploads/newsletters/'.$record['image'].'"  target="_blank" >Click to open current pdf</a></label><br>
					';
				}

                $body .= '
                <label>Description:</label>
                <textarea name="info">'.stripslashes($record['info']).'</textarea>
				<label style="display:inline-block; width: 250px;">Is this Newsletter active?</label> <label class="radio">yes <input title="Is this Newsletter active?" type="radio" name="active"'; if($record['active'] != '0'){ $body .= 'checked '; } $body .= 'value="1" /></label> <label class="radio">no <input title="Is this client active?" type="radio" name="active"'; if($record['active'] == '0'){ $body .= 'checked '; } $body .= 'value="0" /></label>';
				

				if($hasCategories){
					$body .= '
					<label>Category:</label>
					<select class="dd" name="category" >';
					foreach($categories as $key => $value){
						$body .= '
						<option value="'.$key.'"'; if($record['category'] == $key){ $body .= ' selected'; } $body .='>'.$value.'</option>'."\r\n";
					}
					$body .= '
					</select>';
				}
				$body .= '
				<div class="clear"></div>
				<input type="hidden" name="action" value="process" />
				<input type="hidden" name="id" value="'.$id.'" />
                <input type="submit" class="submit" value="Submit" />
			</form>
			<a onclick="return confirm(\'Are you sure you want to delete ' . $record['name'] . '?\')" href="'.$pageBase.'&action=delete&item=client&id='.$record['id'].'" class="delete button-fix">Delete</a>
		';

		

	
	}elseif($_GET['action'] == 'category' && is_numeric($_GET['id'])){
		$body .= '<p class="prev"><a href="'.$pageBase.'">Return to Previous Page</a></p>';
		$body .= '<h3>Edit a Category</h3>';
		
		$id = clean($_GET['id']);
		$query = "SELECT * FROM newsletterCategories WHERE id='$id' LIMIT 1";
		$result = mysql_query($query);
		$record = mysql_fetch_assoc($result);
		
		$body .= '
			<form action="'.$pageBase.'" method="post" enctype="multipart/form-data">
				<label>Name:<br />
				<input type="text" class="text" name="cat_name" value="'.$record['name'].'"/></label>
				<label style="display:inline-block; width: 250px;">Is this category active?</label> <label class="radio">yes <input title="Is this client active?" type="radio" name="active"'; if($record['active'] != '0'){ $body .= 'checked '; } $body .= 'value="1" /></label> <label class="radio">no <input title="Is this client active?" type="radio" name="active"'; if($record['active'] == '0'){ $body .= 'checked '; } $body .= 'value="0" /></label>
				<div class="clear"></div>
				<input type="hidden" name="action" value="category" />
				<input type="hidden" name="id" value="'.$id.'" />
                <input type="submit" class="submit" value="Submit" />
			</form>
			<a onclick="return confirm(\'Are you sure you want to delete ' . $record['name'] . '? This will also delete any newsletters in this category.\')" href="'.$pageBase.'&action=delete&item=category&id='.$record['id'].'" title="Delete This Category" class="delete button-fix">Delete</a>
		';
	
	}else{
		//show newsletters
		$query = "SELECT * FROM newsletters ORDER BY datePosted DESC, name";
		$result = mysql_query($query);
		
		$body .= '
		<div id="tabs">
            <ul>
				<li><a href="#add">Add a Newsletter</a></li>
                <li><a href="#manage">Manage Existing Newsletters</a></li>';
                
            $body .= '</ul>';
            $body .= '<div id="manage">
		<p class="directions">Below your current newsletters are listed by category. The disabled newsletters / categories will have strikethroughs. Use the icons to the right to edit, disable or delete any item.</p>
		<ul class="page-list">
		';
		
		$currentCat = '';
		$displayedCats = array();
		while($record = mysql_fetch_assoc($result)){
			if($hasCategories && $currentCat != $record['category']){
				$q = "SELECT active FROM newsletterCategories WHERE id='".$record['category']."'";
				$res = mysql_query($q);
				$rec = mysql_fetch_assoc($res);
				
				$active = $rec['active'];
				$body .= '<li style="background: none;"><strong'; if($active == '0'){ $body .= ' class="inactive"'; } $body .= '>'.$categories[$record['category']].'</strong>';
				
				$body .=' <a onclick="return confirm(\'Are you sure you want to delete ' . $categories[$record['category']] . '?\')" class="icon" href="'.$pageBase.'&action=delete&item=category&id='.$record['category'].'" title="Delete This Category"><img style="margin-right: 13px;" width="15" class="icon" src="/images/icon_trash.png" alt="Delete" /></a>';
				
				if($active == '1'){
					$body .= ' <a onclick="return confirm(\'Are you sure you want to diasable ' . $categories[$record['category']] . '?\')" class="icon" href="'.$pageBase.'&action=disable&item=category&id='.$record['category'].'" title="Disable This Category"><img width="15" class="icon" src="/images/icon_disable.png" alt="Disable" /></a>';
				}else{
					$body .= ' <a onclick="return confirm(\'Are you sure you want to enable ' . $categories[$record['category']] . '?\')" class="icon" href="'.$pageBase.'&action=enable&item=category&id='.$record['category'].'" title="Enable This Category"><img width="15" class="icon" src="/images/icon_enable.png" alt="Enable" /></a>';
				}
				
				$body .=' <a class="icon" href="'.$pageBase.'&action=category&id='.$record['category'].'" title="Edit This Category"><img width="15" class="icon" src="/images/icon_edit.png" alt="Edit" /></a>';
				
				$body .= '</li>';
				$currentCat = $record['category'];
				array_push($displayedCats,$record['category']);
			}
			$item_active = $record['active'];
			
						
				$body .= '<li><a class="mid'; if($active == '0' || $item_active == '0'){ $body .= ' inactive'; } $body .= '" title="Edit this client" href="'.$pageBase.'&action=edit&id='.$record['id'].'">'.$record['name'].'</a>';
			
				$body .=' <a onclick="return confirm(\'Are you sure you want to delete ' . $record['name'] . '?\')" class="icon" href="'.$pageBase.'&action=delete&item=client&id='.$record['id'].'" title="Delete This Client"><img width="15" class="icon" src="/images/icon_trash.png" alt="Delete" /></a>';
					
				if($item_active == '1'){
					$body .= ' <a onclick="return confirm(\'Are you sure you want to disable ' . $record['name'] . '?\')" class="icon" href="'.$pageBase.'&action=disable&item=client&id='.$record['id'].'" title="Disable This Client"><img width="15" class="icon" src="/images/icon_disable.png" alt="Disable" /></a>';
				}else{
					$body .= ' <a onclick="return confirm(\'Are you sure you want to enable ' . $record['name'] . '?\')" class="icon" href="'.$pageBase.'&action=enable&item=client&id='.$record['id'].'" title="Enable This Client"><img width="15" class="icon" src="/images/icon_enable.png" alt="Enable" /></a>';
				}
				
				$body .=' <a class="icon" href="'.$pageBase.'&action=clietn&id='.$record['id'].'" title="Edit This Client"><img width="15" class="icon" src="/images/icon_edit.png" alt="Edit" /></a>';
			
			$body .= '</li>'."\r\n";
		}
		//hack to show cats without content
		$q = "SELECT id, name, active FROM newsletterCategories ORDER BY name";
		$res = mysql_query($q);
		while($rec = mysql_fetch_assoc($res)){
			if(!in_array($rec['id'],$displayedCats)){
				$active = $rec['active'];
				
				$body .= '<p><strong'; if($active == '0'){ $body .= ' class="inactive"'; } $body .= '>'.$rec['name'].'</strong>';
				
				$body .=' <a onclick="return confirm(\'Are you sure you want to delete ' . $rec['name'] . '?\')" class="icon" href="'.$pageBase.'&action=delete&item=category&id='.$rec['id'].'" title="Delete This Category"><img style="margin-right: 13px;" width="15" class="icon" src="/images/icon_trash.png" alt="Delete" /></a>';
				
				if($active == '1'){
					$body .= ' <a onclick="return confirm(\'Are you sure you want to diasable ' . $rec['name'] . '?\')" class="icon" href="'.$pageBase.'&action=disable&item=category&id='.$rec['id'].'" title="Disable This Category"><img width="15" class="icon" src="/images/icon_disable.png" alt="Disable" /></a>';
				}else{
					$body .= ' <a onclick="return confirm(\'Are you sure you want to enable ' . $rec['name'] . '?\')" class="icon" href="'.$pageBase.'&action=enable&item=category&id='.$rec['id'].'" title="Enable This Category"><img width="15" class="icon" src="/images/icon_enable.png" alt="Enable" /></a>';
				}
			
				$body .=' <a class="icon" href="'.$pageBase.'&action=category&id='.$rec['id'].'" title="Edit This Category"><img width="15" class="icon" src="/images/icon_edit.png" alt="Edit" /></a>';
			}
		}
		
		$body .= '</ul>';
		$body .= '<hr />';
		if($hasCategories){
			$body .= '<h3>Add a Category</h3>';
			
			$id = clean($_GET['id']);
			
			$body .= '
				<form action="'.$pageBase.'" method="post" enctype="multipart/form-data">
					<label>Name:<br />
					<input type="text" class="text" name="cat_name" value="'.$_POST['cat_name'].'"/></label>
					<label style="display:inline-block; width: 250px;">Is this category active?</label> <label class="radio">yes <input title="Is this client active?" type="radio" name="active"'; if($_POST['active'] != '0'){ $body .= 'checked '; } $body .= 'value="1" /></label> <label class="radio">no <input title="Is this category active?" type="radio" name="active"'; if($_POST['active'] == '0'){ $body .= 'checked '; } $body .= 'value="0" /></label>
					<div class="clear"></div>
					<input type="hidden" name="action" value="category" />
					<input type="submit" class="submit" value="Submit" style="margin-bottom: 10px;" />
				</form>
			';
			$body .= '<hr />';
		}
		$body .= '</div><div id="add">';
		
		$id = clean($_GET['id']);
		
		$body .= '
			<form action="'.$pageBase.'" method="post" enctype="multipart/form-data">';
						
				$body .= '<label>Newsletter Name:<br />
				<input type="text" class="text" name="name" value="'.$_POST['name'].'"/></label>';
				
				if($isOnline){
					$body .= '<label>Url / Website: <span class="small">If external website, begin with "http://"</span><br />
				<input type="text" class="text" name="url" value="'.$_POST['url'].'"/></label>';
				}
				
				$body .= '<label>Date Posted:<br />
				<input id="datePosted" type="text" name="datePosted" class="date" value="'.$defaultDate.'"/></label>
				';
				
				if($isPrinted){
					$body .= '
					<label id="file">Upload Newsletter File <span class="small">(must be a PDF or Word doc. Maximum file size of 6MB.)</span><br />
					<input title="Browse your computer for a new photo." type="file" name="image" id="filename" /></label><br>
					';
				}
							
                $body .= '<label>Descrition:</label>
                <textarea name="info">'.stripslashes($_POST['info']).'</textarea>
				<label style="display:inline-block; width: 250px;">Is this Newsletter active?</label> <label class="radio">yes <input title="Is this client active?" type="radio" name="active"'; if($_POST['active'] != '0'){ $body .= 'checked '; } $body .= 'value="1" /></label> <label class="radio">no <input title="Is this Newsletter active?" type="radio" name="active"'; if($_POST['active'] == '0'){ $body .= 'checked '; } $body .= 'value="0" /></label>';
				

				if($hasCategories){
					$body .= '
					<label>Category:</label>
					<select class="dd" name="category" >';
					foreach($categories as $key => $value){
						$body .= '
						<option value="'.$key.'"'; 
						if($_POST['category'] == $key){ $body .= ' selected'; } 
						$body .='>'.$value.'</option>'."\r\n";
					}
					$body .= '
					</select>';
				}
				$body .= '
				<div class="clear"></div>
				<input type="hidden" name="action" value="process" />
                <input type="submit" class="submit" value="Submit" />
			</form></div>
		';
	}
	echo $body;
	
}else{
	echo '<p>You have no newsletters for your site, please <a href="/">click here</a> to continue.</p>';
}

	//GET THE WYSISYG 
	?>
    </div>
    <script>
    CKEDITOR.replace( 'info', {
		filebrowserBrowseUrl: '/filemanager/index.php?filemanager=1&site=|<?php echo $siteID; ?>|',
    });
	</script>
    

</div>