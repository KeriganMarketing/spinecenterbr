<script type="text/javascript">
	$(function(){

		// Tabs
		$('#tabs').tabs();
		$("#datePosted").datepicker();
	});
	
	function popitup(url) {
		newwindow=window.open(url,"_blank","directories=no, status=no, width=1124, height=600, top=250, left=250, scrollbars=1");
		if (window.focus) {newwindow.focus()}
		return false;
	}

</script>
<?php
$id = $_GET['id'];
$articleId = $_GET['articleid'];
$body .= '<h1>Manage Your Blog</h1>';
if($error != ''){ $body .= '<p class="error">'.$error.'</p>'; }
if($msg != ''){ $body .= '<p class="success">'.$msg.'</p>'; }

if($_GET['action'] == 'edit'){
	
	$query = "SELECT * FROM blogPosts WHERE id='$id'";
	if(!$result = mysql_query($query)){
		$redirect = TRUE;
	}
	$record = mysql_fetch_assoc($result);
	
	$body .= $wysiwyg.'
	<p class="prev"><a href="'.$pageBase.'">Return to Previous Page</a></p>
	<div id="tabs">
		<ul>
			<li><a href="#edit">Editing "'.$record['title'].'"</a></li>
		</ul>
		<div id="edit">
	<form class="site" action="'.$pageBase.'" method="post" enctype="multipart/form-data">
		<label>Title:<br />
		<input type="text" name="title" class="text" value="'.$record['title'].'" /></label>
		<label>Shortlink: <br><p style="font-size:12px; font-weight:normal;">Required for the URL in the address bar. Only lowercase letters, numbers and <br>
hyphens allowed. No spaces, commas or punctuation.</p>
		<input type="text" name="controller" class="text" value="'.$record['controller'].'" /></label>
		<label>Date Posted:<br />
		<input id="datePosted" type="text" name="date" class="date" value="'.cal_date($record['date']).'"/></label><br>
		
		<div id="authorship">
		<label>Authorship Information <span style="font-size:12px; font-weight:normal;">optional</span></label>
		<label>Name: <input type="text" name="author" class="text" value="'.$record['author'].'" style="width:430px;" /></label>';
		if($hasRole){
		$body .='<label>Title: <input type="text" name="role" class="text" value="'.$record['role'].'" style="width:430px;" /></label>';
		}
		$body .='<label>Email: <input type="text" name="email" class="text" value="'.$record['email'].'" style="width:430px;" /></label><br>
		</div>';
		
		if($hasCategories){
			$body .= '
			<label>Category:<br />
			<select name="category" class="dd">
			';
			foreach($categories as $key=>$value){
				$body .= '<option value="'.$key.'"'; if($record['category'] == $key){ $body .= ' selected'; } $body .= '>'.$value.'&nbsp;</option>'."\r\n";
			}
			$body .= '
			</select></label>';
		}
		$body .= '<label>Content:<br />
		<textarea name="pageContent" id="pageContent">'.stripslashes($record['content']).'</textarea></label>
		<label style=" display:inline-block; width: 400px;">Is this post active?</label> <label class="radio">yes <input type="radio" name="active" '; if($record['active'] != '0'){ $body .= 'checked ';} $body .= 'value="1" /></label> <label class="radio">no <input type="radio" name="active" '; if($record['active'] == '0'){ $body .= 'checked ';} $body .= 'value="0" /></label>';
		
		if($hasImg){
			$body.='<br><br><label>Add or replace image associated with post?<br />
			<input type="file" id="image" name="image" /></label>'; 
			if($record['img'] != ''){ 
				$body.='<br>Current image:<br><img src="'.$site.'/images/blog/'.$record['img'].'" style="max-width:400px;"><br><a class="small" onClick="return confirm(\'Are you sure you want to delete this image?\');" href="'.$pageBase.'&action=deleteImage&id='.$record['id'].'">Delete the image currently associated with this post.</a><br>';
			} 
		}
		
		if($hasVid){
			$body.='<br><label>Add or replace YouTube video associated with post?<br /></label>
			<span style="font-size:13px;">Insert the code you received when uploading the video to YouTube:</span> <input type="text" class="text" id="vid" name="vid" style="width:200px;" /><br>'; 
			if($record['vid'] != ''){ 
				$body.='<br>Current video:<br>
				<iframe  style="width:400px" height="275" src="//www.youtube.com/embed/'.$record['vid'].'" frameborder="0" allowfullscreen></iframe>
				<br><a class="small" onClick="return confirm(\'Are you sure you want to remove this video?\');" href="'.$pageBase.'&action=deleteVideo&id='.$record['id'].'">Remove the video associated with this post.</a>';
			} 
		}
		
		if($hasTitleTag || $hasMeta){
			$body.='<br><br><div id="seo-stuff">
			<label>Search Engine Optimization Options <span style="font-size:12px; font-weight:normal;">optional</span></label>';
			if($hasTitleTag){
				$body .= '<label>Title Tag: <input type="text" name="titletag" class="text" value="'.$record['titleTag'].'" style="width:430px;" /></label>';
			}
			if($hasMeta){
				$body .= '<label>Meta Description:<br />
				<textarea name="metadescription" id="metaDescription" style="width:95%; padding:10px; height:100px;border:1px solid #cccccc;">'.$record['metaDescription'].'</textarea></label><br>';
			}
			$body.='</div>';
		}
		
		if($record['active'] == '0'){$status = 'Draft';}else{$status = 'Published';}
		
	$body .= '
		<div class="clear"></div>
		<input type="hidden" name="action" value="process" />
		<input type="hidden" name="id" value="'.$record['id'].'" />
		
		<div id="published-box" style="width:185px;height:280px;float:right;position:absolute;top:0;right:0;margin:65px 12px; 0 0;background:#F2F2F2;border:1px solid #cdcdcd;box-shadow: 0px 0px 3px rgba(0, 0, 0, 0.15);">
			<center>
			<p style="background:rgba(0,0,0,.1);padding:5px 0;"><strong>status:</strong> <em>'.$status.'</em></p>
			<input type="submit" class="submit" name="save" value=" Save as Draft " />
			<input type="submit" class="submit" name="publish" value=" Publish " /><br>
			<input type="button" class="submit" name="preview" formtarget="_blank" value=" Preview " onClick="popitup(\''.$site.'/preview/blog/'.$record['controller'].'/\');" />
			</center>
		</div>
		
	</form>
	<!--<form name="deleteSite" id="deleteSite" action="'.$pageBase.'" method="post" onSubmit="return confirm(\'Are you sure you want to delete this category?\');">
		<input type="hidden" name="id" value="'.$record['id'].'"/>
		<input type="hidden" name="action" value="delete" />
		<input type="submit" class="delete button-fix" value="Delete" />
	</form>-->
	</div>
	</div>
	';
	
}elseif($_GET['action'] == 'editcomment'){ ////////////////////edit comment
	
	$query = "SELECT * FROM blogResponses WHERE id='$id'";
	if(!$result = mysql_query($query)){
		$redirect = TRUE;
	}
	$record = mysql_fetch_assoc($result);
	
	$body .= '
	<p class="prev"><a href="'.$pageBase.'">Return to Previous Page</a></p>
	<h2>Edit a Comment</h2>
	<form class="site" action="'.$pageBase.'" method="post" enctype="multipart/form-data">
		<label>Name:<br />
		<input type="text" name="name" class="text" value="'.$record['name'].'" /></label>
		<label>Email:<br />
		<input type="text" name="email" class="text" value="'.$record['email'].'" /></label>';
		
		$body .= '<label>Content:<br />
		<textarea name="message" id="pageContent" style="width:100%; height:225px;">'.stripslashes($record['content']).'</textarea></label>
		<label style=" display:inline-block; width: 400px;">Is this comment active?</label> <label class="radio">yes <input type="radio" name="active" '; if($record['active'] != '0'){ $body .= 'checked ';} $body .= 'value="1" /></label> <label class="radio">no <input type="radio" name="active" '; if($record['active'] == '0'){ $body .= 'checked ';} $body .= 'value="0" /></label>';
		
	$body .= '
		<div class="clear"></div>
		<input type="hidden" name="action" value="processcomment" />
		<input type="hidden" name="id" value="'.$record['id'].'" />
		<input type="submit" class="submit" value="Submit" />
	</form>
	<form name="deleteComment" id="deleteSite" action="'.$pageBase.'" method="post" onSubmit="return confirm(\'Are you sure you want to delete this comment?\');">
		<input type="hidden" name="id" value="'.$record['id'].'"/>
		<input type="hidden" name="action" value="deletecomment" />
		<input type="submit" class="delete button-fix" value="Delete" />
	</form>
	';
	
}elseif($_GET['action'] == 'respondtocomment'){ ////////////////////POST response
	
	$query = "SELECT * FROM blogResponses WHERE id='$id'";
	if(!$result = mysql_query($query)){
		$redirect = TRUE;
	}
	$blogComment = mysql_fetch_assoc($result);
	
	$query2 = "SELECT * FROM blogPosts WHERE id='$articleId'";
	if(!$result2 = mysql_query($query2)){
		$redirect = TRUE;
	}
	$blogPost = mysql_fetch_assoc($result2);
	
	if($_POST['replyname'] == '' ){ $postName = $blogPost['author']; } 
	else{ $postName = $_POST['replyname']; }
	if($_POST['replyemail'] == '' ){ $postEmail = $blogPost['email']; } 
	else{ $postEmail = $_POST['replyemail']; }
	
	$body .= '
	<p class="prev"><a href="'.$pageBase.'">Return to Previous Page</a></p>
	<h2>Post a Response to Comment:</h2>
	<p>'.$blogComment['content'].'</p>
	<p>by: '.$blogComment['name'].'</p>
	<form class="site" action="'.$pageBase.'" method="post" enctype="multipart/form-data">
		<label>Name:<br />
		<input type="text" name="replyname" class="text" value="'.$postName.'" /></label>
		<label>Email:<br />
		<input type="text" name="replyemail" class="text" value="'.$postEmail.'" /></label>';
		
		$body .= '<label>Content:<br />
		<textarea name="replycomment" id="pageContent" style="width:100%; height:225px;">'.stripslashes($_POST['replycomment']).'</textarea></label>
		<label style=" display:inline-block; width: 400px;">Is this comment active?</label> <label class="radio">yes <input type="radio" name="replyactive" '; if($_POST['replyactive'] != '0'){ $body .= 'checked ';} $body .= 'value="1" /></label> <label class="radio">no <input type="radio" name="replyactive" '; if($_POST['replyactive'] == '0'){ $body .= 'checked ';} $body .= 'value="0" /></label>';
		
	$body .= '
		<div class="clear"></div>
		<input type="hidden" name="action" value="processreply" />
		<input type="hidden" name="date" value="'.date('Y-m-d').'"/>
		<input type="hidden" name="articleid" value="'.$blogPost['id'].'" />
		<input type="hidden" name="commentid" value="'.$blogComment['id'].'" />
		<input type="submit" class="submit" value="Post Response" />
	</form>
	';
	
}elseif($_GET['action'] == 'categories' || $_GET['action'] == 'deleteCategory'){ ////////////////////edit categories
	
	$query = "SELECT * FROM blogCategories";
	$result = mysql_query($query);
	$body .= '
		<p class="prev"><a href="'.$pageBase.'">Return to Previous Page</a></p>
		<h3>Add a Category</h3>
		<form action="'.$pageBase.'&action=categories" method="post">
			<label>Category: <input type="text" class="mid-text" name="name" value="'.$_POST['name'].'"/><input type="hidden" name="cmd" value="categories">
			<input type="submit" class="submit side-button" value="Add">
			</label>
		</form>
		<h3>Edit Current Categories</h3>
	';
	while($record = mysql_fetch_assoc($result)){
		$body .= '
		<form action="'.$pageBase.'&action=categories" method="post">
			<label><input type="text" class="mid-text" name="name" value="'.$record['name'].'"/><input type="hidden" name="cmd" value="categories"><input type="hidden" name="id" value="'.$record['id'].'">
			<input type="submit" class="submit side-button" value="Update">
			<a class="delete" onClick="return confirm(\'Are you sure you want to delete this category?\');" href="'.$pageBase.'&action=deleteCategory&id='.$record['id'].'">Delete</a>
			</label>
		</form>
		';
	}
	
}else{ //////////////////// show tabs
	
	$body .= '<div id="tabs">
		<ul>
			<li><a href="#add">Add a New Blog Post</a></li>
			<li><a href="#manage">Manage Existing Blog Posts</a></li>';
	if($hasComments){ $body .= '<li><a href="#comments">Manage Comments</a></li>'; }		
	$body .= '</ul>
		<div id="manage">';
		
	if($hasCategories){
		$body .= '<ul class="page-list"><li><a class="photoCategories" href="'.$pageBase.'&action=categories">Manage your categories</a></li></ul>';
	}
	
	$page = clean($_GET['p']);
	if($page == ''){
		$page = 1;
	}
	$prev = $page-1;
	$next = $page+1;
	//default page, list all events
	$query = "SELECT id,title,date,active FROM blogPosts";
	if($_GET['cat'] != ''){ $query .= " WHERE category='".clean($_GET['cat'])."'"; }
	if(!$result = mysql_query($query)){
		die(mysql_error());
	}
	$count = mysql_num_rows($result);
	
	$show = 20;
	$pageCount = ceil($count/$show);
	
	$start = ($page-1) * $show;
	
	$query = $query." ORDER BY date DESC LIMIT $start,$show";
	if(!$result = mysql_query($query)){
		die($query.mysql_error());
	}
	
	if($pageCount > 1){
		$pagination .= '<div class="pagination">';
		if($page != 1){
			$pagination .= ' <a href="'.$pageBase.'&p='. $prev .'">prev</a> ';
		}
		$i=1;
		while($i<=$pageCount){
			if($i != $page){
				$pagination .= ' <a href="'.$pageBase.'&p='.$i.'">';
			}
			$pagination .= $i;
			if($i != $page){
				$pagination .= '</a> ';
			}
			$i++;
		}
		if($page != $pageCount){
			$pagination .= ' <a href="'.$pageBase.'&p='. $next .'">next</a> ';
		}
		$pagination .= '</div>';
	}
	
	//filter by category
	if($hasCategories){
		$body .= '
		<form name="catSwitch">
		<label>Category:
		<input type="hidden" name="cmd" value="blog" />
		<select name="cat" class="dd" onchange="document.catSwitch.submit()">
		<option value="">View all&nbsp;</option>';
		foreach($categories as $key=>$value){
			$body .= '<option value="'.$key.'"'; if($_GET['cat'] == $key){ $body .= ' selected'; } $body .= '>'.$value.'&nbsp;</option>'."\r\n";
		}
		$body .= '
		</select></label>
		</form>
		
		';
	}
	
	$body .= '<ul class="page-list">';
	while($record = mysql_fetch_assoc($result)){
		$active = $record['active'];
		$body .= '<li><a class="mid'; if($active == '0'){ $body .= ' inactive'; } $body .= '" title="Edit this client" href="'.$pageBase.'&action=edit&id='.$record['id'].'">'.crop($record['title'], 33).' - <span class="small">'.cal_date($record['date']).'</span></a>';
			
		$body .=' <a onclick="return confirm(\'Are you sure you want to delete ' . $record['title'] . '?\')" class="icon" href="'.$pageBase.'&action=delete&item=post&id='.$record['id'].'" title="Delete This Post"><img width="15" class="icon" src="/images/icon_trash.png" alt="Delete" /></a>';
			
		if($active == '1'){
			$body .= ' <a onclick="return confirm(\'Are you sure you want to disable ' . $record['title'] . '?\')" class="icon" href="'.$pageBase.'&action=disable&item=post&id='.$record['id'].'" title="Disable This Post"><img width="15" class="icon" src="/images/icon_disable.png" alt="Disable" /></a>';
		}else{
			$body .= ' <a onclick="return confirm(\'Are you sure you want to enable ' . $record['title'] . '?\')" class="icon" href="'.$pageBase.'&action=enable&item=post&id='.$record['id'].'" title="Enable This Post"><img width="15" class="icon" src="/images/icon_enable.png" alt="Enable" /></a>';
		}
		
		$body .=' <a class="icon" href="'.$pageBase.'&action=edit&id='.$record['id'].'" title="Edit This Post"><img width="15" class="icon" src="/images/icon_edit.png" alt="Edit" /></a>';
		
		$body .= '</li>'."\r\n";
	}
	$body .= '</ul>';
	
	$body = $body.$pagination.'</div>';
	
	if($_POST['date'] == ''){
		$_POST['date'] = date("m/d/Y");
	}
	
	$body .= '
	<div id="add">
	<form class="site" action="'.$pageBase.'" method="post" enctype="multipart/form-data">
		
		<label>Title:<br />
		<input type="text" name="title" id="title" class="text" value="'.$_POST['title'].'" onKeyUp="showurl(this,\'urlexample\');" /></label>
		<p style="font-weight:normal;">The shortlink for the post will be: <input type="text" id="urlexample" name="urlexample" style="color:#06F; border:none; width:300px;" ></p>
		<!--<label>Shortlink: <br />
<span class="small">(Unique identifier for the page used in the URL / address bar.)</span><br />-->
        <input title="This will be how the page shows in the URL / address bar." type="hidden" name="controller" class="text" value="auto" /><!--</label>-->
		
		<label>Date Posted:<br />
		<input id="datePosted" type="text" name="date" class="date" value="'.$_POST['date'].'"/></label><br>
		
		<div id="authorship">
		<label>Authorship Information <span style="font-size:12px; font-weight:normal;">optional</span></label>
		<label>Name: <input type="text" name="author" class="text" value="'.$_POST['author'].'" style="width:430px;" /></label>';
		if($hasRole){
			$body .= '<label>Title: <input type="text" name="role" class="text" value="'.$_POST['role'].'" style="width:430px;" /></label>';
		}
		$body .= '<label>Email: <input type="text" name="email" class="text" value="'.$_POST['email'].'" style="width:430px;" /></label><br>
		</div>';
		
		if($hasCategories){
			$body .= '<label>Category:<br />
			<select name="category" class="dd">';
			foreach($categories as $key=>$value){
				$body .= '<option value="'.$key.'"'; if($_POST['category'] == $key){ $body .= ' selected'; }elseif($_GET['cat'] == $key){ $body .= ' selected'; } $body .= '>'.$value.'&nbsp;</option>'."\r\n";
			}
			$body .= '
			</select></label>';
		}
		$body .= '<label>Content:<br />
		<textarea name="pageContent" id="pageContent">'.stripslashes($_POST['content']).'</textarea></label>
		<label style=" display:inline-block; width: 400px;">Is this post active?</label> <label class="radio">yes <input type="radio" name="active" '; if($_POST['active'] != '0'){ $body .= 'checked ';} $body .= 'value="1" /></label> <label class="radio">no <input type="radio" name="active" '; if($_POST['active'] == '0'){ $body .= 'checked ';} $body .= 'value="0" /></label><br><br>';
		if($hasImg){
			$body.='<label>Image to be associated with post:<br />
			<input type="file" id="image" name="image" /></label>'; 
		}
		
		if($hasVid){
			$body.='<br><label>YouTube video associated with post:<br /></label>
			<span style="font-size:13px;">Insert the code you received when uploading the video to YouTube:</span> <input type="text" class="text" id="vid" name="vid" style="width:200px;" /><br>'; 
			
		}
		
		if($hasTitleTag || $hasMeta){
			$body.='<br><br><div id="seo-stuff">
			<label>Search Engine Optimization Options <span style="font-size:12px; font-weight:normal;">optional</span></label>';
			if($hasTitleTag){
				$body .= '<label>Title Tag: <input type="text" name="titletag" class="text" value="'.$_POST['titletag'].'" style="width:430px;" /></label>';
			}
			if($hasMeta){
				$body .= '<label>Meta Description:<br />
				<textarea name="metadescription" id="metaDescription" style="width:95%; padding:10px; height:100px;border:1px solid #cccccc;">'.$_POST['metadescription'].'</textarea></label><br>';
			}
			$body.='</div>';
		}
		
		
	$body .= '
		<div class="clear"></div>
		<input type="hidden" name="action" value="process" />
		
		<div id="published-box" style="width:185px;height:280px;float:right;position:absolute;top:0;right:0;margin:65px 12px; 0 0;background:#F2F2F2;border:1px solid #cdcdcd;box-shadow: 0px 0px 3px rgba(0, 0, 0, 0.15);">
			<center>
			<p style="background:rgba(0,0,0,.1);padding:5px 0;"><strong>status:</strong> <em style="color:red;">NOT SAVED</em></p>
			<input type="submit" class="submit" name="save" value=" Save as Draft " />
			<input type="submit" class="submit" name="publish" value=" Publish " /><br><br>
			<p>Save a draft to<br> preview post.</p>
			</center>
		</div>
		
	</form></div>';
	
	
	if($hasComments){ 
		$query = "SELECT * FROM blogResponses WHERE replyID='' ORDER BY id DESC";
		if(!$result = mysql_query($query)){
			die($query.mysql_error());
		}
		
		$body .= '<div id="comments">
		<p>Comments to your blog posts are listed below and are disabled by default. Use the icons at right to Edit, Enable/Disable or Delete them.</p>';
		$body .= '<ul class="page-list">';
		
		while($record = mysql_fetch_assoc($result)){
			$active = $record['approved'];
			
			$body .= '<li style="height:auto;">';
				
			$body .=' <a onclick="return confirm(\'Are you sure you want to delete this comment?\')" class="icon" href="'.$pageBase.'&action=delete&item=comment&id='.$record['id'].'" title="Delete This Comment"><img width="15" class="icon" src="/images/icon_trash.png" alt="Delete" /></a>';
				
			if($active == '1'){
				$body .= ' <a class="icon" href="'.$pageBase.'&action=disable&item=comment&id='.$record['id'].'" title="Disable This Comment"><img width="15" class="icon" src="/images/icon_disable.png" alt="Disable" /></a>';
			}else{
				$body .= ' <a class="icon" href="'.$pageBase.'&action=enable&item=comment&id='.$record['id'].'" title="Enable This Comment"><img width="15" class="icon" src="/images/icon_enable.png" alt="Enable" /></a>';
			}
			
			$body .=' <a class="icon" href="'.$pageBase.'&action=editcomment&id='.$record['id'].'" title="Edit This Comment"><img width="15" class="icon" src="/images/icon_edit.png" alt="Edit" /></a>';
			
			
			$postID = $record['postID'];
			$tquery = "SELECT title FROM blogPosts WHERE id='$postID' LIMIT 1";
			if(!$tresult = mysql_query($tquery)){
				die($tquery.mysql_error());
			}
			while($blogrecord = mysql_fetch_assoc($tresult)){
				$body .= '<span style="" class="mid'; if($active == '0'){ $body .= ' inactive'; } $body .= '" ><span class="small">'.date('m/d/Y', strtotime($record['date'])).'</span><br>'.$record['name'].' commented on "'.$blogrecord['title'].'" : '.crop($record['content'], 150).'</span>';
			}
			$body .='<p align="right"><a id="respondtocomment" href="'.$pageBase.'&action=respondtocomment&id='.$record['id'].'&articleid='.$record['postID'].'" title="Respond to this comment">Post a response</a></p>';
			
			
			$commentID = $record['id'];
			$rquery = "SELECT * FROM blogResponses WHERE replyID='$commentID' ORDER BY id";
			if(!$rresult = mysql_query($rquery)){
				die($rquery.mysql_error());
			}
			
			$body .= '<ul class="page-list">';

			while($replies = mysql_fetch_assoc($rresult)){
				$body .= '<li style="height:auto; border:1px solid rgba(50,50,50,.4); background:rgba(50,50,50,.2);">';
				
				$body .=' <a onclick="return confirm(\'Are you sure you want to delete this comment?\')" class="icon" href="'.$pageBase.'&action=delete&item=comment&id='.$replies['id'].'" title="Delete This Comment"><img width="15" class="icon" src="/images/icon_trash.png" alt="Delete" /></a>';
				
				if($active == '1'){
					if($replies['approved'] == '1'){
						$body .= ' <a class="icon" href="'.$pageBase.'&action=disable&item=comment&id='.$replies['id'].'" title="Disable This Comment"><img width="15" class="icon" src="/images/icon_disable.png" alt="Disable" /></a>';
					}else{
						$body .= ' <a class="icon" href="'.$pageBase.'&action=enable&item=comment&id='.$replies['id'].'" title="Enable This Comment"><img width="15" class="icon" src="/images/icon_enable.png" alt="Enable" /></a>';
					}
				}
				
				$body .=' <a class="icon" href="'.$pageBase.'&action=editcomment&id='.$replies['id'].'" title="Edit This Comment"><img width="15" class="icon" src="/images/icon_edit.png" alt="Edit" /></a>';
				
				$body .= '<span style="" class="mid'; if($replies['approved'] == '0'){ $body .= ' inactive'; } $body .= '" ><span class="small">'.date('m/d/Y', strtotime($replies['date'])).'</span><br>'.$replies['name'].' replied to comment: '.crop($replies['content'], 150).'</span>';
				
				$body .= '</li>'."\r\n";
				
			}

			$body .= '</ul>';

			
			$body .= '</li>'."\r\n";
		
		}
		$body .= '</ul>';
		$body .= '</div>';
	}
	
}
if($redirect){
	header('Location: '.$pageBase);
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