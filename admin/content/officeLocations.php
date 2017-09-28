<script type="text/javascript">
	$(function(){
		// Tabs
		$('#tabs').tabs();
		$("#sortable").sortable();
	});
</script>
<div id="photo-gallery">


<div id="pages">
<?php
//ini_set('display_errors', 'On');

if($allowed){
	if($_GET['action'] == 'edit' && is_numeric($_GET['id'])){
		$body .= '<p class="prev"><a href="'.$pageBase.'">Return to Previous Page</a></p>';
		$body .= '<h3>Edit a Location</h3>';
		
		$id = clean($_GET['id']);
		$query = "SELECT * FROM officeLocations WHERE id='$id' LIMIT 1";
		$result = mysql_query($query);
		$record = mysql_fetch_assoc($result);
		
		$body .= '
			<form action="'.$pageBase.'" method="post" enctype="multipart/form-data">
				<label>Name:<br />
				<input type="text" class="text" name="name" value="'.$record['name'].'"/></label>';
				
				$body .= '<label>Address:<br />
				<input type="text" class="text" name="address" value="'.$record['address'].'"/></label>';
				
				$body .= '<label>Address 2:<br />
				<input type="text" class="text" name="address2" value="'.$record['address2'].'"/></label>';
				
				$body .= '<label>Phone: <span class="small">(as displayed on website)</span><br />
				<input type="text" class="text" name="phone" value="'.$record['phone'].'"/></label>';
				
				$body .= '<label>Fax: <span class="small">(as displayed on website)</span><br />
				<input type="text" class="text" name="fax" value="'.$record['fax'].'"/></label>';
				
				$body .= '<label>Url / Link: <span class="small">If external website, begin with "http://"</span><br />
				<input type="text" class="text" name="link" value="'.$record['link'].'"/></label>';
				
                $body .= '
				<label style="display:inline-block; width: 250px;">Is this office active?</label> <label class="radio">yes <input title="Is this office active?" type="radio" name="active"'; if($record['active'] != '0'){ $body .= 'checked '; } $body .= 'value="1" /></label> <label class="radio">no <input title="Is this office active?" type="radio" name="active"'; if($record['active'] == '0'){ $body .= 'checked '; } $body .= 'value="0" /></label>';
				
				if($hasCName){
					$body .= '<label>Contact Name: <br />
					<input type="text" class="text" name="contactName" value="'.$record['contactName'].'"/></label>';
				}
				
				if($hasCEmail){
					$body .= '<label>Contact Email Address: <span class="small">(e.g. you@domain.com)</span><br />
					<input type="text" class="text" name="contactEmail" value="'.$record['contactEmail'].'"/></label>';
				}
				
				if($hasCName2){
					$body .= '<label>Contact 2 Name: <br />
					<input type="text" class="text" name="contactName2" value="'.$record['contactName2'].'"/></label>';
				}
				
				if($hasCEmail2){
					$body .= '<label>Contact 2 Email Address: <span class="small">(e.g. you@domain.com)</span><br />
					<input type="text" class="text" name="contactEmail2" value="'.$record['contactEmail2'].'"/></label>';
				}
				
				if($hasDescription){
                $body .= '<label>Description:</label>
                <textarea name="description">'.stripslashes($record['description']).'</textarea>';
				}
				
				if($hasGoogleMap){
					$body .= '<label>Google Map code:</label>
                	<textarea name="googleMap">'.$record['googleMap'].'</textarea>';
				}
				if($hasDirections){
					$body .= '<label>Link to Directions: <br />
					<input type="text" class="text" name="directions" value="'.$record['directions'].'"/></label>';
				}
				
				if($hasImage){
					$body .= '
					<label id="file">Image <span class="small">(must be a .jpg. Maximum file size of 6MB.)</span><br />
					<input title="Browse your computer for a new photo." type="file" name="image" id="filename" /></label>
					';
				}
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
			<a onclick="return confirm(\'Are you sure you want to delete ' . $record['name'] . '?\')" href="'.$pageBase.'&action=delete&item=office&id='.$record['id'].'" class="delete button-fix">Delete</a>
		';
		if($hasImage){
			$body .= '
			<h2>Current Photo:</h2>
			<img src="'.$site.'/images/offices/'.$record['img'].'" />
			';
		}
	
	}elseif($_GET['action'] == 'category' && is_numeric($_GET['id'])){
		$body .= '<p class="prev"><a href="'.$pageBase.'">Return to Previous Page</a></p>';
		$body .= '<h3>Edit a Category</h3>';
		
		$id = clean($_GET['id']);
		$query = "SELECT * FROM officeCategories WHERE id='$id' LIMIT 1";
		$result = mysql_query($query);
		$record = mysql_fetch_assoc($result);
		
		$body .= '
			<form action="'.$pageBase.'" method="post" enctype="multipart/form-data">
				<label>Name:<br />
				<input type="text" class="text" name="cat_name" value="'.$record['name'].'"/></label>
				<label style="display:inline-block; width: 250px;">Is this category active?</label> <label class="radio">yes <input title="Is this office active?" type="radio" name="active"'; if($record['active'] != '0'){ $body .= 'checked '; } $body .= 'value="1" /></label> <label class="radio">no <input title="Is this office active?" type="radio" name="active"'; if($record['active'] == '0'){ $body .= 'checked '; } $body .= 'value="0" /></label>
				<div class="clear"></div>
				<input type="hidden" name="action" value="category" />
				<input type="hidden" name="id" value="'.$id.'" />
                <input type="submit" class="submit" value="Submit" />
			</form>
			<a onclick="return confirm(\'Are you sure you want to delete ' . $record['name'] . '? This will also delete any offices in this category.\')" href="'.$pageBase.'&action=delete&item=category&id='.$record['id'].'" title="Delete This Category" class="delete button-fix">Delete</a>
		';
	
	}else{
		
		if($hasCategories){
		//show offices with categories
		$query = "SELECT name, id, category, active FROM officeLocations ORDER BY category, sortOrder";
		} else { $query = "SELECT name, id, active FROM officeLocations ORDER BY sortOrder"; }
		$result = mysql_query($query);
		
		$body .= '
		<div id="tabs">
            <ul>
                <li><a href="#manage">Manage Existing Offices</a></li>
                <li><a href="#add">Add an Office</a></li>
				<li><a href="#sort">Update Office Order</a></li>';
            $body .= '</ul>';
            $body .= '<div id="manage">
		<p class="directions">Below your current offices are listed by category. The disabled offices / categories will have strikethroughs. Use the icons to the right to edit, disable or delete any item.</p>
		<ul class="page-list">
		';
		
		$currentCat = '';
		$displayedCats = array();
		while($record = mysql_fetch_assoc($result)){
			if($hasCategories && $currentCat != $record['category']){
				$q = "SELECT active FROM officeCategories WHERE id='".$record['category']."'";
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
			$body .= '<li><a class="mid'; if($active == '0' || $item_active == '0'){ $body .= ' inactive'; } $body .= '" title="Edit this office" href="'.$pageBase.'&action=edit&id='.$record['id'].'">'.$record['name'].'</a>';
				
			$body .=' <a onclick="return confirm(\'Are you sure you want to delete ' . $record['name'] . '?\')" class="icon" href="'.$pageBase.'&action=delete&item=office&id='.$record['id'].'" title="Delete This office"><img width="15" class="icon" src="/images/icon_trash.png" alt="Delete" /></a>';
				
			if($item_active == '1'){
				$body .= ' <a onclick="return confirm(\'Are you sure you want to disable ' . $record['name'] . '?\')" class="icon" href="'.$pageBase.'&action=disable&item=office&id='.$record['id'].'" title="Disable This office"><img width="15" class="icon" src="/images/icon_disable.png" alt="Disable" /></a>';
			}else{
				$body .= ' <a onclick="return confirm(\'Are you sure you want to enable ' . $record['name'] . '?\')" class="icon" href="'.$pageBase.'&action=enable&item=office&id='.$record['id'].'" title="Enable This office"><img width="15" class="icon" src="/images/icon_enable.png" alt="Enable" /></a>';
			}
			
			$body .=' <a class="icon" href="'.$pageBase.'&action=clietn&id='.$record['id'].'" title="Edit This office"><img width="15" class="icon" src="/images/icon_edit.png" alt="Edit" /></a>';
			
			$body .= '</li>'."\r\n";
		}
		if($hasCategories){
			//hack to show cats without content
			$q = "SELECT id, name, active, sortOrder FROM officeCategories ORDER BY name";
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
					<label style="display:inline-block; width: 250px;">Is this category active?</label> <label class="radio">yes <input title="Is this office active?" type="radio" name="active"'; if($_POST['active'] != '0'){ $body .= 'checked '; } $body .= 'value="1" /></label> <label class="radio">no <input title="Is this category active?" type="radio" name="active"'; if($_POST['active'] == '0'){ $body .= 'checked '; } $body .= 'value="0" /></label>
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
			<form action="'.$pageBase.'" method="post" enctype="multipart/form-data">
				<label>Name:<br />
				<input type="text" class="text" name="name" value="'.$_POST['name'].'"/></label>';
				
				$body .= '<label>Address:<br />
				<input type="text" class="text" name="address" value="'.$_POST['address'].'"/></label>';
				
				$body .= '<label>Address 2:<br />
				<input type="text" class="text" name="address2" value="'.$_POST['address2'].'"/></label>';
				
				$body .= '<label>Phone: <span class="small">(as displayed on website)</span><br />
				<input type="text" class="text" name="phone" value="'.$_POST['phone'].'"/></label>';
				
				$body .= '<label>Fax: <span class="small">(as displayed on website)</span><br />
				<input type="text" class="text" name="fax" value="'.$_POST['fax'].'"/></label>';
				
				$body .= '<label>Url / Link: <span class="small">If external website, begin with "http://"</span><br />
				<input type="text" class="text" name="link" value="'.$_POST['link'].'"/></label>';
				
				$body .= '<label style="display:inline-block; width: 250px;">Is this office active?</label> <label class="radio">yes <input title="Is this office active?" type="radio" name="active"'; if($_POST['active'] != '0'){ $body .= 'checked '; } $body .= 'value="1" /></label> <label class="radio">no <input title="Is this office active?" type="radio" name="active"'; if($_POST['active'] == '0'){ $body .= 'checked '; } $body .= 'value="0" /></label>';
				
				if($hasCName){
					$body .= '<label>Contact Name: <br />
					<input type="text" class="text" name="contactName" value="'.$_POST['contactName'].'"/></label>';
				}
				
				if($hasCEmail){
					$body .= '<label>Contact Email Address: <span class="small">(e.g. you@domain.com)</span><br />
					<input type="text" class="text" name="contactEmail" value="'.$_POST['contactEmail'].'"/></label>';
				}
				
				if($hasCName2){
					$body .= '<label>Contact 2 Name: <br />
					<input type="text" class="text" name="contactName2" value="'.$_POST['contactName2'].'"/></label>';
				}
				
				if($hasCEmail2){
					$body .= '<label>Contact 2 Email Address: <span class="small">(e.g. you@domain.com)</span><br />
					<input type="text" class="text" name="contactEmail2" value="'.$_POST['contactEmail2'].'"/></label>';
				}
				
				if($hasDescription){
                $body .= '<label>Description:</label>
                <textarea name="description">'.stripslashes($_POST['description']).'</textarea>';
				}
				
				if($hasImage){
					$body .= '
					<label id="file">Image <span class="small">(must be a .jpg. Maximum file size of 6MB.)</span><br />
					<input title="Browse your computer for a new photo." type="file" name="image" id="filename" /></label>
					';
				}
				
				if($hasGoogleMap){
					$body .= '<label>Google Map code:</label>
                	<textarea name="googleMap">'.$_POST['googleMap'].'</textarea>';
				}
				if($hasDirections){
					$body .= '<label>Link to Directions: <br />
					<input type="text" class="text" name="directions" value="'.$_POST['directions'].'"/></label>';
				}

				if($hasCategories){
					$body .= '
					<label>Category:</label>
					<select class="dd" name="category" >';
					foreach($categories as $key => $value){
						$body .= '
						<option value="'.$key.'"'; if($_POST['category'] == $key){ $body .= ' selected'; } $body .='>'.$value.'</option>'."\r\n";
					}
					$body .= '
					</select>';
				}
				$body .= '
				<div class="clear"></div>
				<input type="hidden" name="action" value="process" />
                <input type="submit" class="submit" value="Submit" />
				</form></div>
				
				<div id="sort">
				<p>Click and drag an office location to change thier order.</p>';

				if($hasCategories){
					$q = "SELECT id, name FROM officeCategories WHERE active='1''";
					$res = mysql_query($q);
					while($rec = mysql_fetch_assoc($res)){
						$officeCat = $rec['id'];
						$body .= '<p>'.$rec['name'].'</p>
						<form id="sortorder'.$officeCat.'" action="'.$pageBase.'" name="sortOrder'.$officeCat.'" method="post" enctype="multipart/form-data">
						<ul class="page-list navorder" id="sortable">';
						$sortQuery = "SELECT id, name, category, sortOrder FROM officeLocations WHERE category='$officeCat' ORDER BY sortOrder ASC";
						$sortResult = mysql_query($sortQuery);
						while($sort = mysql_fetch_assoc($sortResult)){
							$body .= '<li><label class="sortorder"><input title="Once finished, click \'Update Sort Order\' below." type="hidden" class="sortorder" name="sortorder[]" value="'.$sort['id'].'"></label>'.$sort['name'].'</li>';
						}
						$body .= '</ul>';
						
						$body .= '
						<div class="clear"></div>
						<input title="Offices will be reordered in ascending order." type="submit" class="submit" value="Update Sort Order" />
						<input type="hidden" name="catsort" value="'.$officeCat.'" />
						<input type="hidden" name="cmd" value="sortOrder" />
						</form>
						<div class="clear"></div>';
						
					}
						
				} else {
					$body .= '
					<form id="sortorder" action="'.$pageBase.'" name="sortOrder" method="post" enctype="multipart/form-data">
					<ul class="page-list navorder" id="sortable">';
					$sortQuery = "SELECT id, name, sortOrder FROM officeLocations ORDER BY sortOrder ASC";
					$sortResult = mysql_query($sortQuery);
					while($sort = mysql_fetch_assoc($sortResult)){
						$body .= '<li><label class="sortorder"><input title="Once finished, click \'Update Sort Order\' below." type="hidden" class="sortorder" name="sortorder[]" value="'.$sort['id'].'"></label>'.$sort['name'].'</li>';
					}
					$body .= '</ul>';
					
					$body .= '
					<div class="clear"></div>
					<input title="Offices will be reordered in ascending order." type="submit" class="submit" value="Update Sort Order" />
					<input type="hidden" name="cmd" value="sortOrder" />
					</form>';
					
				}
					
			
		///////////////////////////////
		
			$body .= '</ul>';
		
		$body .= '</div>';
		
	}
	echo $body;
	
}else{
	echo '<p>You have no offices for your site, please <a href="/">click here</a> to continue.</p>';
}

	//GET THE WYSISYG 
	?>
    </div>
    <script>
    CKEDITOR.replace( 'description', {
		
		<?php //if($siteID =='14' || $siteID =='5'){?>
		filebrowserBrowseUrl: '/filemanager/index.php?filemanager=1&site=|<?php echo $siteID; ?>|',
		<?php //} ?>

    });
	</script>
    
<script>
	$(":date").dateinput({
		
		//turn on month/year selector
		selectors: true, 
	
		// this is displayed to the user
		format: 'mm/dd/yyyy',
	});
</script>
</div>