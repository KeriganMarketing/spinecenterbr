<div id="pages">
	<script type="text/javascript">
        $(function(){
            $('#tabs').tabs();
			$("#sortable").sortable();
        });
    </script>

<?php
if($allowed){
	if($_GET['action'] == 'edit' && is_numeric($_GET['id'])){
		$body .= '<p class="prev"><a href="'.$pageBase.'">Return to Previous Page</a></p>';
		$body .= '<h3>Edit a Testimonial</h3>';
		
		$id = clean($_GET['id']);
		$query = "SELECT * FROM testimonials WHERE id='$id' LIMIT 1";
		$result = mysql_query($query);
		$record = mysql_fetch_assoc($result);
		
		$body .= '
			<form action="'.$pageBase.'" method="post" enctype="multipart/form-data">
				<label>Signature:<br />
				<input type="text" class="text" name="author" value="'.$record['author'].'"/></label>
				<label>Content:<span class="small">(to create line-breaks press SHIFT and ENTER. If you just use ENTER, the returns will not stay)</span></label>
                <textarea name="pageContent">'.$record['content'].'</textarea>
				<label style="display:inline-block; width: 250px;">Is this testimonial active?</label> <label class="radio">yes <input title="Is this client active?" type="radio" name="display"'; if($record['display'] != '0'){ $body .= 'checked '; } $body .= 'value="1" /></label> <label class="radio">no <input title="Is this client active?" type="radio" name="display"'; if($record['display'] == '0'){ $body .= 'checked '; } $body .= 'value="0" /></label>
				<div class="clear"></div>
				<input type="hidden" name="action" value="process" />
				<input type="hidden" name="id" value="'.$id.'" />
                <input type="submit" class="submit" value="Submit" />
			</form>
			<a onclick="return confirm(\'Are you sure you want to delete this testimonial?\')" href="'.$pageBase.'&action=delete&id='.$record['id'].'" class="delete button-fix">Delete</a>
		';
	
	}else{
		//show clients
		if($hasSorting){
			$query = "SELECT * FROM testimonials ORDER BY sortOrder";
		}else{
			$query = "SELECT * FROM testimonials";
		}
		$result = mysql_query($query);
		
		$body .= '
		 <div id="tabs">
                <ul>
                    <li><a href="#manage">Manage Existing Testimonials</a></li>';
					if($hasSorting){
                    	$body .= '<li><a href="#order">Update Order</a></li>';
					}
                    $body .= '<li><a href="#add">Add a New Testimonial</a></li>
                </ul>
                <div id="manage">
		<p class="directions">Below your current testimonials. The disabled will have strikethroughs. Click on any item below to edit it.</p>
		<ul class="page-list">
		';
		
		while($record = mysql_fetch_assoc($result)){
			$item_active = $record['display'];
			$body .= '<li><a class="'; if($item_active == '0'){ $body .= 'inactive'; } $body .= '" title="Edit this testimonial" href="'.$pageBase.'&action=edit&id='.$record['id'].'">'.$record['author'].'</a></li>'."\r\n";
		}
		
		$body .= '</ul></div>';
		
		
		if($hasSorting){
		$body .= '<div id="order">
			<p class="directions">Drag and drop the testimonials and then click the "Sort Testimonials" button to reorder them.</p>
			<form action="'.$pageBase.'" method="post" enctype="multipart/form-data">
			<ul class="page-list navorder" id="sortable">';
			$iquery = "SELECT * FROM testimonials ORDER BY sortOrder";
			$iresult = mysql_query($iquery);
			while($item = mysql_fetch_assoc($iresult)){
				$item_active = $item['display'];
				$item_content = $item['content'];
				$item_author = $item['author'];
				$item_id = $item['id'];
				$body .= '<li class="ui-state-default " ';
				if($item_active != '1'){
					$body .= 'style="text-decoration:line-through"';
				}
				$body .= ' >'.$item_author.' <span style="font-size:10px;">"'.crop($item_content,50).'"</span><input type="hidden" name="sortOrder[]" value="'.$item_id.'" /></li>'."\r\n";
			}
	
		$body .= '</ul>
		<input type="hidden" name="action" value="sortOrder" />
        <input type="submit" class="submit" value=" Sort Testimonials " />
		</form></div>';
		}
		
		
		$body .= '<div id="add">';
		
		$body .= '
			<form action="'.$pageBase.'" method="post" enctype="multipart/form-data">
				<label>Signature:<br />
				<input type="text" class="text" name="author" value="'.$_POST['author'].'"/></label>
                <label>Content:<span class="small">(to create line-breaks press SHIFT and ENTER. If you just use ENTER, the returns will not stay)</span></label>
                <textarea name="pageContent">'.stripslashes($_POST['pageContent']).'</textarea>
				<label style="display:inline-block; width: 250px;">Is this client active?</label> <label class="radio">yes <input title="Is this client active?" type="radio" name="display"'; if($_POST['display'] != '0'){ $body .= 'checked '; } $body .= 'value="1" /></label> <label class="radio">no <input title="Is this client active?" type="radio" name="display"'; if($_POST['display'] == '0'){ $body .= 'checked '; } $body .= 'value="0" /></label>
				<div class="clear"></div>
				<input type="hidden" name="action" value="process" />
                <input type="submit" class="submit" value="Submit" />
			</form>
			</div>
		';
	}
	echo $body;
	
}else{
	echo '<p>You have no testimonials for your site, please <a href="/">click here</a> to continue.</p>';
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