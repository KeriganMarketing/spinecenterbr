<script type="text/javascript">
	$(function(){
		// Tabs
		$('#tabs').tabs();
		$(".sortable").sortable();
	});
</script>
<div id="photo-gallery">
<h1>Manage Your Portfolio</h1>

    <?php
    if($allowed){	
        if($_GET['action'] == 'edit' && is_numeric($_GET['id'])){
            $id = clean($_GET['id']);
            $query = "SELECT * FROM portfolio WHERE id='$id' LIMIT 1";
            $result = mysql_query($query);
            $record = mysql_fetch_assoc($result);
            
            $body .= '
                <p class="prev"><a href="'.$pageName.'">Return to Previous Page</a></p>
                <h3>Edit an Image</h3>
                <form id="image" action="'.$pageName.'" name="add" method="post" enctype="multipart/form-data">
                    <label id="file">Upload a New Image <span class="small"><br />(must be a .jpg, leave blank to keep the photo the same. Maximum file size of 6MB.)</span><br />
                    <input title="Browse your computer for a new photo." type="file" class="text" name="imagefile" id="filename" /></label>
            
                    <label id="title">Name<br />
                    <input type="text" class="text" name="name" title="Please enter the name of the entry." value="'.$record['name'].'" /></label>
                    
					<label>Description:<br />
                    <textarea id="description" title="Enter the description for the item." type="text" class="text" name="description" >'.stripslashes($record['description']).'</textarea></label>
										
					<label>Link / URL:<br />
                    <input type="text" class="text" name="link" title="Please enter the link/url for the entry." value="'.$record['link'].'" /></label>
					
					<label style=" display:inline-block; width: 400px;">Is this post active?</label> <label class="radio">yes <input type="radio" name="active" '; if($record['active'] != '0'){ $body .= 'checked ';} $body .= 'value="1" /></label> <label class="radio">no <input type="radio" name="active" '; if($record['active'] == '0'){ $body .= 'checked ';} $body .= 'value="0" /></label>';
                    
                    if($hasCategories){
                        $body .= '<label>Category:<br /><select class="dd" name="category">';
                        foreach($categories as $key => $value){
                            $body .= '<option value="'.$key.'"'; if($key == $record['category']){$body .=' selected'; } $body .= ' >'.$value.'&nbsp;</option>'."\r\n";
                        }
                        $body .= '</select></label>';
                    }
                    
                    $body .= '
                    <input title="Update the photo." class="submit" type="submit" value="Update" />
                    <input type="hidden" name="cmd" value="editAction" />
                    <input type="hidden" name="ID" value="'.$record['id'].'" />
                </form>
                <h3 class="clear">Current Image:</h3>
                <img class="current-image" src="'.$site.'/images/uploads/portfolio/'.$record['img'].'"/>
            ';
        }else{
            $body .= '
            <div id="tabs">
            <ul>
                <li><a href="#manage">Manage Portfolio</a></li>
                <li><a href="#add">Add a New Portfolio Item</a></li>';
                if($hasCategories){
                    $body .= '<li><a href="#categories">Manage Your Categories</a></li>';
                }
            $body .= '</ul>';
            $body .= '<div id="manage">';
            if($hasCategories){
                $body .= '
                <form name="catSwitch">
                <label>Filter by Category:
                <input type="hidden" name="cmd" value="portfolio" />
                <select name="category" class="dd" onchange="document.catSwitch.submit()">
                <option value="">View all&nbsp;</option>
                ';
                foreach($categories as $key=>$value){
                    $body .= '<option value="'.$key.'"'; if($_GET['category'] == $key){ $body .= ' selected'; } $body .= '>'.$value.'&nbsp;</option>'."\r\n";
                }
                $body .= '
                </select></label>
                </form>
                ';
            }
            
            //start sortOrder form
            
			
			if($hasCategories && !($_GET['category'] && is_numeric($_GET['category']))){
				$catQuery = "SELECT * FROM portfolioCategories ORDER BY sortOrder ASC";
				$catResult = mysql_query($catQuery);
            	while($catRecord = mysql_fetch_assoc($catResult)){
					$catID = $catRecord['id'];
					$catSort = $catRecord['id'];					
					$query = "SELECT * FROM portfolio WHERE category=$catSort ORDER BY sortOrder ASC";
										
					$body .= '<h3>'.$catRecord['name'].'</h3>
						     <form id="sortorder'.$catID.'" action="'.$pageName.'" name="sortOrder'.$catID.'" method="post" enctype="multipart/form-data">
							 <ul class="sortable">';
	
					$result = mysql_query($query);
					while($record = mysql_fetch_assoc($result)){
						$body .= '
							<li class="gallery-block">';
							$body .='<label class="sortorder"><input title="Once finished, click \'Update Sort Order\' below." type="hidden" class="sortorder" name="sortorder[]" value="'.$record['id'].'"></label><p class="title">'.crop($record['name'],40).'</p>
								<div class="image"><img src="'.$site.'/images/uploads/portfolio/'.$record['img'].'" /></div>
								<p class="description'; if($hasCategories){ $body .= ' short'; } $body .= '">'.crop(stripslashes($record['description']),120).'</p>
								<a class="submit" href="'.$pageName.'&action=edit&id='.$record['id'].'">Edit</a> <a class="delete" onClick="return confirm(\'Are you sure you want to delete this photo?\');" href="'.$pageName.'&action=delete&id='.$record['id'].'">Delete</a>
							</li>
						';
					}
					$body .= '</ul>';
					if(mysql_num_rows($result)<1){
					$body .= '<p>You do not have any portfolio items in this category.</p>';
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
					$query = "SELECT * FROM portfolio WHERE category=$cat ORDER BY";
				}else{
					$query = "SELECT * FROM portfolio ORDER BY";
				}
				if($hasCategories){
					$query .= " category,";
				}
				$query .= " sortOrder ASC";
				
				$body .= '<ul class="sortable">';
				//$body .= '<ul>';
				$result = mysql_query($query);
				while($record = mysql_fetch_assoc($result)){
					$body .= '
						<li class="gallery-block">';
						if($hasCategories){
							$body .= '<p class="title"> '.crop($categories[$record['category']],35).'</p>';
						}
						$body .='<label class="sortorder"><input title="Once finished, click \'Update Sort Order\' at bottom of page." type="hidden" class="sortorder" name="sortorder[]" value="'.$record['id'].'"></label><p class="title">'.crop($record['name'],40).'</p>
							<div class="image"><img src="'.$site.'/images/uploads/portfolio/'.$record['img'].'" /></div>
							<a class="submit" href="'.$pageName.'&action=edit&id='.$record['id'].'">Edit</a> <a class="delete" onClick="return confirm(\'Are you sure you want to delete this photo?\');" href="'.$pageName.'&action=delete&id='.$record['id'].'">Delete</a>
						</li>
					';
				}
				$body .= '</ul>';
			
				if(mysql_num_rows($result)<1){
					$body .= '<p>You do not have any Portfolio Items</p>';
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
                <form id="image" action="'.$pageName.'" name="add" method="post" enctype="multipart/form-data">
                    <label id="file">Upload an Image <span class="small">(must be a .jpg. Maximum file size of 6MB.)</span><br />
                    <input title="Browse your computer for a new photo." type="file" class="text" name="imagefile" id="filename" /></label>
            
                    <label id="title">Title:<br />
                    <input title="Please enter the name of the photo." type="text" class="text" name="name" value="'.$_POST['name'].'" /></label>
                    
                    <label>Description:<br />
                    <textarea id="description" title="Enter the description for the item." type="text" class="text" name="description" >'.stripslashes($_POST['description']).'</textarea></label>
					<label>Link / URL:<br />
                    <input type="text" class="text" name="link" title="Please enter the link/url for the entry." value="'.$_POST['link'].'" /></label>
					
					<label style=" display:inline-block; width: 400px;">Is this item active?</label> <label class="radio">yes <input type="radio" name="active" '; if($_POST['active'] != '0'){ $body .= 'checked ';} $body .= 'value="1" /></label> <label class="radio">no <input type="radio" name="active" '; if($_POST['active'] == '0'){ $body .= 'checked ';} $body .= 'value="0" /></label>';
                    
                    if($hasCategories){
                        $body .= '<label>Category:<br /><select class="dd" name="category">';
                        foreach($categories as $key => $value){
                            $body .= '<option value="'.$key.'"'; if($key == $_POST['category']){$body .=' selected'; } $body .= ' >'.$value.'&nbsp;</option>'."\r\n";
                        }
                        $body .= '</select></label>';
                    }
                    
                    $body .= '<input title="Create the photo." class="submit" type="submit" value="Submit" />
                    <input type="hidden" name="cmd" value="editAction" />
                </form>
            ';
            $body .= '</div>';
            if($hasCategories){
				$body .= '<div id="categories">';
				$query = "SELECT * FROM portfolioCategories";
				$result = mysql_query($query);
				$body .= '
					<h3>Add a Category</h3>
					<form action="'.$pageName.'" method="post">
						<label>Category: <input type="text" class="mid-text" name="name" value="'.$_POST['name'].'"/><input type="hidden" name="cmd" value="categories">
						<input type="submit" class="submit side-button" value="Add">
						</label>
					</form>
					<h3>Edit/Sort Current Categories</h3>
					<p>Drag and drop to change sort order.</p>
					<ul>
				';
				while($record = mysql_fetch_assoc($result)){
					$body .= '
					<li class="sortable" style="padding:2px; background:#eaeaea; margin:1px; border:1px solid #CCC;"><form action="'.$pageName.'" method="post">
						<label><input type="text" class="mid-text" name="name" value="'.$record['name'].'"/><input type="hidden" name="cmd" value="categories"><input type="hidden" name="id" value="'.$record['id'].'">
						<input type="submit" class="submit side-button" value="Update">
						<a class="delete" onClick="return confirm(\'Are you sure you want to delete this category?\');" href="'.$pageName.'&action=deleteCategory&id='.$record['id'].'">Delete</a>
						</label>
					</form></li>
					';
				}
				$body .= '</ul></div>';
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

//GET THE WYSISYG 
	?>
    
    </div>
    <script>
    CKEDITOR.replace( 'description', {
    	uiColor: '#eaeaea',
		skin: 'moonocolor_v1.1',
		toolbar: [
			[ 'Source', '-', 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt' ],
			[ 'Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat' ],
			'/',
			[ 'Bold','Italic','Underline','Strike','-','Subscript','Superscript' ],
			[ 'NumberedList','BulletedList','-','Outdent','Indent' ],
			[ 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock' ],
			[ 'Link','Unlink','Anchor' ],
			'/',
			[ 'Image','Table','HorizontalRule','SpecialChar' ],
			[ 'Format','Font','FontSize' ]
		],
		
		<?php //if($siteID =='14' || $siteID =='5'){?>
		filebrowserBrowseUrl: '/filemanager/index.php?filemanager=1&site=|<?php echo $siteID; ?>|',
		<?php //} ?>

    });
</script>