<?php
// DEBUG
ini_set('display_errors', 'Off');
//if($_POST){ print_r ($_POST); }

//CONFIG
$websiteUrl = 'http://spinecenterbr.com';
$postTable = 'blogPosts';
$responseTable = 'blogResponses';
$spamTable = 'blogSpam';
$blogBase = '/education-resources/spine-articles/view/'; //ie http://domain.com**$blogbase**controller needs preceding '/' and trailing '/'
//$category = '1';


$archiveBase = 'archive'; //post archive trigger. YOU CANNOT USE THIS TRIGGER AS A POST CONTROLLER
$article = mysql_real_escape_string($r_url['3']); // the third item in the url
$notificationEmail = 'support@kerigan.com'; // email to be notified about comments

function reformat($stripped_content){
	$stripped_content = str_replace(array('&nbsp;'),'',$stripped_content);
	$stripped_content = preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $stripped_content);
	$stripped_content = str_replace(array('&amp;nbsp;'),'',$stripped_content);
	return trim($stripped_content);
}	

//rss feed
if($article == 'rss.xml'){
	header('Content-type: text/xml');
	print trim('<?xml version="1.0" encoding="utf-8"?>
	<rss xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:wfw="http://wellformedweb.org/CommentAPI/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:sy="http://purl.org/rss/1.0/modules/syndication/" xmlns:slash="http://purl.org/rss/1.0/modules/slash/" version="2.0">
	<channel>
		<title>'.reformat($pageTitle).'</title>
		<atom:link href="'.$websiteUrl.$blogBase.'rss.xml" rel="self" type="application/rss+xml"/>
		<link>'.$websiteUrl.'</link>
		<language>en-US</language>
		<lastBuildDate>'.date("F j, Y, g:i a").'</lastBuildDate>
		');
	
	$query = "SELECT * FROM $postTable WHERE active=1 ORDER BY date DESC";
	$result = mysql_query($query,$database);
	while($record = mysql_fetch_assoc($result)){
		$content = $record['content'];
		
		//social share codes
		$articleurl = urlencode($websiteUrl.$blogBase.$article);
		$articleTitle = urlencode($record['title']);
		$articleContent = myTruncate(strip_tags($record['content']), 160, ' ', '...');
		$articleSource = urlencode($site_name);
		
		$social = '' . "\n";
		$social .= '<p>Share on Facebook: <a href="https://www.facebook.com/sharer/sharer.php?u='.$articleurl.'&t='.$articleTitle.'">https://www.facebook.com/sharer/sharer.php?u='.$articleurl.'&t='.$articleTitle.'</a><br>' . "\n";
		$social .= 'Share on Twitter: <a href="http://twitter.com/share?original_referer='.$articleurl.'">http://twitter.com/share?original_referer='.$articleurl.'</a><br>' . "\n";
		$social .= 'Share on LinkedIn: <a href="http://www.linkedin.com/shareArticle?mini=true&url='.$articleurl.'&title='.$articleTitle.'&summary='.$articleContent.'&source='.$articleSource.'">http://www.linkedin.com/shareArticle?mini=true&url='.$articleurl.'&title='.$articleTitle.'&summary='.$articleContent.'&source='.$articleSource.'</a></p>' . "\n";
		print '
			<item>
				<title>'.htmlspecialchars($record['title']).'</title>
				<link>'.$websiteUrl.$blogBase.$record['controller'].'</link>
				<id>newsid:'.$record['id'].'</id>
				<pubDate>'.date('r', strtotime($record['date'])).'</pubDate>
				<dc:creator>'.reformat($record['author']).'</dc:creator>
				<description><![CDATA[ '.reformat($content).$social.' ]]></description>
				<guid>'.$websiteUrl.$blogBase.$record['controller'].'</guid>
			</item>';
	}
	print '
	</channel>
</rss>';
	die();
	
}

$body = ''; //create the container for body
if($article != $archiveBase){
	if($_POST['control'] == 'formSubmit'){
		//define variables
		$articleId = clean($_POST['id']);
		$name = clean($_POST['name']);
		$email = clean($_POST['email']);
		$message = clean($_POST['message']);
		$title = clean($_POST['title']);
		
		if($_POST['commentedId']!='' && $_POST['commentedContent']!=''){
			$replytoID = clean($_POST['commentedId']);
			$replycomment = clean($_POST['commentedContent']);
		}
		
		//form checks
		$passCheck = TRUE;
		
		if($email == '') { 
			$error .='<li style="color:#ff0000;">Email address can\'t be blank.</li>'."\r\n";
			$passCheck = FALSE;
		}else{
			if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) { //check that email is in proper format
				$error .='<li style="color:#ff0000;">You\'ve provided an email address that is not properly formatted. Addresses must be formatted as follows: "user@domain.com"</li>'."\r\n";
				$passCheck = FALSE;
			} 
		}
		
		if($name == '') {
			$error .='<li>Name can\'t be blank.</li>';
			$passCheck = FALSE;
		}else{
			if(!ereg('[A-Za-z]', $name)) { //check that the name only contain letters
				$error .='<li style="color:#ff0000;">Your name can only contain uppercase and lowercase letters.</li>'."\r\n";
				$passCheck = FALSE;
			}
		}
		
		if($email == '') { 
			$error .='<li style="color:#ff0000;">You didn\'t type a message!</li>'."\r\n";
			$passCheck = FALSE;
		}
		
		if($passCheck){//passCheck is true so we process the submittion

			$to = $notificationEmail;
			$subject = 'Comment on your blog post, "'.$title.'"';
			
			// message
			if($replytoID && $replycomment){
				
				$emailmessage = '
				<html>
				<head>
				  <title>Someone has commented on a Blog Post</title>
				</head>
				<body>
				<br><p>'.$name.' has replied to a comment on your blog post, "'.$title.'"</p>
				  <p>Original Comment:<br>
				  '.$replycomment.'</p>
				  <p><strong>Email Address:</strong> '.$email.'<br>
				  <strong>Reply:</strong> '.$message.'</p>
				  <br>
				  <p><a href="https://keriganonline.com/admin/?cmd=blog" target="_blank" >Approve/Edit/Delete this comment</a></p>
				  <br>
				</body>
				</html>
				';
			
			}else{
			
				$emailmessage = '
				<html>
				<head>
				  <title>Someone has commented on a Blog Post</title>
				</head>
				<body>
				<br><p>'.$name.' has commented on your blog post, "'.$title.'"</p>
				  <p><strong>Email Address:</strong> '.$email.'<br>
				  <strong>Comment:</strong> '.$message.'</p>
				  <br>
				  <p><a href="https://keriganonline.com/admin/?cmd=blog" target="_blank" >Approve/Edit/Delete this comment</a></p>
				  <br>
				</body>
				</html>
				';
			
			}
	
			// To send HTML mail, the Content-type header must be set
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			
			// Additional headers
			$headers .= 'From: The Spine Center at Bone & Joint Clinic <noreply@spinecenterbr.com>' . "\r\n";
			
			// set the envelope (actual sender)
			$noreply = "-fnoreply@{$_SERVER['SERVER_NAME']}";
			
			if(mail($to, $subject, $emailmessage, $headers, $noreply )){
				$msg = '<span style="color:#248AD3;">We\'ve received your comment. Thanks!</span>';
			}else{
				$msg = 'There was an error processing your request.';
			}
			
			/// insert into db	
			$todaysdate=date('Y-m-d');	
			$IP = $_SERVER['REMOTE_ADDR'];
			$query = "INSERT INTO blogResponses SET IP='$IP', postID='$articleId', title='$title', content='$message', name='$name', email='$email', date='$todaysdate'";
			
			if($_POST['commentedId']!='' && $_POST['commentedContent'] != ''){
				$query .= ", replyID='$replytoID'"; 
				$replytoID = $_POST['commenton'];
			}
		
			
			if(mysql_query($query)){
				//print 'Posted';
			}else{
				die('there was an error adding the user. '.mysql_error());
			}
		
		}else{///passCheck failed. we tell the user why
			$msg = '<ul style="color:#ff0000;">'.$error.'</ul>';
		}
	}
	if($article != ''){
		$postQuery = "SELECT * FROM $postTable WHERE controller='$article' LIMIT 1";
		if($postResult = mysql_query($postQuery, $database)){
			if(mysql_num_rows($postResult) == '0'){
				$is404 = TRUE;
				$body .= '<p>The post you\'ve requested does not exist. <a href="'.$blogBase.$archiveBase.'">Click here to go to our archived posts</a>.';
			}else{
				$blogPost = mysql_fetch_assoc($postResult);
			}
		}else{
			die('There was an error communicating with the database. ERROR: '.mysql_error());
		}
		// they've specified a post they want to read
		//GEORGE DON'T FORGET THE 404 CHECKER
		if(!$is404){// there is a post to show
		
		
		//social share codes
			$articleurl = urlencode($websiteUrl.$blogBase.$article);
			$articleTitle = urlencode($blogPost['title']);
			$articleContent = myTruncate(strip_tags($blogPost['content']), 160, ' ', '...');
			$articleSource = urlencode($site_name);
			
			//$sb = social_bookmarks(1,1,1,1,1,1,1,0); //fb,twit,digg,del,goog,ms,stum,slash
			$sb = '<a href="https://www.facebook.com/sharer/sharer.php?u='.$articleurl.'&t='.$articleTitle.'" id="sb-facebook" target="_blank"><img height="20px" src="/gfx/facebook.gif" alt="Share on Facebook" /></a>' . "\n";
			$sb .= '<a href="http://twitter.com/share?original_referer='.$articleurl.'" id="sb-twitter" target="_blank"><img height="20px" src="/gfx/twitter.gif" alt="Share on Twitter" /></a>' . "\n";
			$sb .= '<a href="http://www.linkedin.com/shareArticle?mini=true&url='.$articleurl.'&title='.$articleTitle.'&summary='.$articleContent.'&source='.$articleSource.'" id="sb-myspace" target="_blank"><img height="20px" src="/gfx/linkedin.gif" alt="Share on LinkedIn" /></a>' . "\n";
			$sb .= '<a href="http://digg.com/submit?url='.$articleurl.'&title='.$articleTitle.'&bodytext='.$articleContent.'" id="sb-digg" target="_blank"><img height="20px" src="/gfx/digg.gif" alt="Digg" /></a>' . "\n";
			$sb .= '<a href="http://delicious.com/post/?url='.$articleurl.'&title='.$articleTitle.'&tags='.$articleContent.'" id="sb-delicious" target="_blank"><img height="20px" src="/gfx/delicious.gif" alt="Share on Del.icio.us" /></a>' . "\n";
			$sb .= '<a href="https://plus.google.com/share?url='.$articleurl.'" id="sb-google" target="_blank"><img height="20px" src="/gfx/google.gif" alt="Share on Google" /></a>' . "\n";
			$sb .= '<a href="http://stumbleupon.com/submit?url='.$articleurl.'&title='.$articleTitle.'&tagnames='.$articleTags.'" id="sb-stumbleupon" target="_blank"><img height="20px" src="/gfx/stumbleupon.gif" alt="Share on StumbleUpon" /></a>' . "\n";
			
			$rss = '<a style="text-decoration:none;" href="'.$websiteUrl.$blogBase.'rss.xml" id="rss-subscribe" target="_blank"><img height="20px" src="/gfx/rss.png">&nbsp; Subscribe to RSS feed</a>' . "\n";
			
			
			///// response form
			$postComment = '';
			$postComment .= '
			<div class="row" style="border:1px solid #CCC;">';
			$postComment .= '<form method="post" enctype="multipart/form-data" action="">
				<div class="col res-12 tab-12 ph-1" style="padding:10px;">
					<p>Message<br>
					<textarea id="comm" class="textarea" name="message" style="height:135px; width:100%;"></textarea></p>
				</div>
				<div class="col res-12 tab-12 ph-1" style="padding:10px;">
					<p style="padding-bottom:0;">Name<br>
					<input type="text" class="text" name="name" placeholder="Name" value="" style="width:100%" /></p>
					<p style="padding-bottom:0;">Email Address<br>
					<input type="text" class="text" name="email" placeholder="Email Address" value="" style="width:100%" /></p>
					<input type="submit" name="submit" class="cta small" value=" Submit Response ">
					<input type="hidden" name="id" value="'.$blogPost['id'].'" >
					<input type="hidden" name="title" value="'.$blogPost['title'].'" >';
					
					if($_POST['commenton']!='' && $_POST['commentoncontent'] != ''){
						$postComment .= '<input type="hidden" name="commentedId" value="'.$_POST['commenton'].'" >';
						$postComment .= '<input type="hidden" name="commentedContent" value="'.$_POST['commentoncontent'].'" >';
					}
						
			$postComment .= '<input type="text" class="control" value="formSubmit" style="position:absolute;top:-50px;left:-100px;" name="control">

				</div>
				<script>
					CKEDITOR.replace( \'comm\' );
				</script>
				<div class="clear"></div>
			</form>
			</div>
			';
						
			$titleTag = $blogPost['title'].' | The Spine Center at Bone & Joint Clinic of Baton Rouge';
			$metaDesc = strip_tags($blogPost['content']);
			$imageUrl = $websiteUrl.'images/blog/'.$blogPost['img'];
			$postLink = $websiteUrl.$blogBase.$blogPost['controller'].'/';
			
			$headline = $blogPost['title'];
			$body .= '
				<article class="col res-1 tab-1 ph-1 blog">
					<meta class="authorship">
				<p class="name">Posted by: '.$blogPost['author'].' <span class="datestamp">on '.cal_date($blogPost['date']).'</span></p>
					</meta>
				<p>'.$blogPost['content'].'</p>';
			
			/*if($blogPost['img'] != ''){
				$body .= '<br><img src="/images/blog/'.$blogPost['img'].'" style="max-width:590px;" alt="'.$blogPost['title'].'" ><br>';
			}*/
			if($blogPost['vid'] != ''){
				$body .= '<br><iframe width="590" height="320" src="//www.youtube.com/embed/'.$blogPost['vid'].'" frameborder="0" allowfullscreen></iframe><br>';
			}
			
				
			$body .='</article>
			
			<section class="row share" style="border-top:1px solid #CCC;padding:10px 0; margin-top:20px;">
			<div id="share-post" class="col res-23 tab-23 ph-1 boxed">
			<p><strong>Share this article:</strong> &nbsp;  '.$sb.'</p>
			</div>
			<div id="share-post-2" class="col res-13 tab-13 ph-1 boxed">
			<p>'.$rss.'</p>
			</div>
			</section>
			';
			
			
			
			// build responses
			
			
			$responseQuery = "SELECT * FROM $responseTable WHERE postID='".$blogPost['id']."' AND approved='1' AND replyID ='' ORDER BY id DESC";
			if(!$responseResult = mysql_query($responseQuery, $database)){
				die('There was an error communicating with the database ERROR: '.mysql_error());
			}
			//create while loop to show the post's response(s)
			
			if(mysql_num_rows($responseResult) > 0){
				$body .= '<div id="comments" class="boxed" style="border-top:1px solid #CCCCCC; padding-top:10px;">';
				$body .= '<h2>Comments:<a name="comments"></a></h2>';
				while($response = mysql_fetch_assoc($responseResult)){
					$body .= '
						<div class="row response">
							<p><span style="color:#467D8F; font-size:12px;"><strong>'.$response['name'].'</strong> on '.date('F j, Y', strtotime($response['date'])).':</span><br>
							'.str_replace('\n','<br>',$response['content']).'<br></p>
						
					';
					$replyQuery = "SELECT * FROM $responseTable WHERE postID='".$blogPost['id']."' AND approved='1' AND replyID ='".$response['id']."' ORDER BY id ASC";
					if(!$replyResult = mysql_query($replyQuery, $database)){
						die('There was an error communicating with the database ERROR: '.mysql_error());
					}
					if(mysql_num_rows($replyResult) > 0){
						while($reply = mysql_fetch_assoc($replyResult)){
						$body .= '
						<div class="response">
							<p><span style="color:#467D8F; font-size:12px;"><strong>'.$reply['name'].'</strong> on '.date('F j, Y', strtotime($reply['date'])).':</span><br>
							'.str_replace('\n','<br>',$reply['content']).'</p>
						</div>
						';
						}
					}
					/*$body .= '
						<div class="row">
							<form method="post" enctype="multipart/form-data" action="#reply">
							<div class="col res-1 tab-1 ph-1" >
								<input type="hidden" name="commenton" value="'.$response['id'].'" >
							</div>
							<div class="col res-1 tab-1 ph-1" >
								<input type="hidden" name="commentoncontent" value="'.$response['content'].'" >
								<input type="submit" name="submit" type="button" value="reply to comment" style="float:right;" class="cta">
							</div>
							</form>
						</div>
					</div>';*/
					
				}
				$body .= '</div>';
			}
			
			$fbTitle = $blogPost['title'];
			$fbDesc = strip_tags($blogPost['content']);
			
			/*$body .= '<div id="comment-post" class="row boxed" style="border-top:1px solid #CCCCCC; padding-top:10px;">';
			$body .='<h2>Leave a Comment:<a name="reply"></a> <span style="font-size:12px">(All fields required)</span></h2>';
			if($_POST['commenton']!='' && $_POST['commentoncontent'] != ''){
				$body .='
				
				<p>You are replying to:</p>
				
				<div class="response" style="border-top:1px solid rgba(50,50,50,.3);">
				<form method="post" enctype="multipart/form-data" action="#reply">
					<input type="submit" name="submit" class="cta small" value="clear" style="float:right;">
				</form>'.$_POST['commentoncontent'].'</div><br>';
			}*/
			//if($msg != ''){ $body .='<p>'.$msg.'</p>'; }
			//$body .= $postComment;
			//$body .='</div></section>';
	
		}
		$pageContent = '';
	}else{
		$article = $archiveBase;
	}
}
if($article == $archiveBase){
	// they want to see the post archive
	
	$page = mysql_real_escape_string($_GET['page']);
	//if(!is_numeric($page)){ $page = '';}
	if($page == '' || !isset($page)){$page = 1;}
	
	if($page != '1'){
	$titleTag = 'The Spine Center Blog | Page '.$page[0].'';
	}
	
	$show = '50'; //number of posts to show per page
	$countQuery = "SELECT id FROM $postTable active ='1'";
	$countResult = mysql_query($countQuery, $database);
	$count = mysql_num_rows($countResult);
	
	$ceil = ($count / $show);
	$pageCount = ceil($ceil);
	
	$archiveQuery = "SELECT * FROM $postTable WHERE active ='1' ORDER BY date DESC";
	//do math to work out how to limit the query to get the correct results
	$start = ($page - 1) * $show;
	
	$archiveQuery .= " LIMIT $start, $show";
	
	//$body .= $archiveQuery.'  '.$pageCount;
	
	$archiveResult = mysql_query($archiveQuery);
	$i = 2;
	while($archive = mysql_fetch_assoc($archiveResult)){
		
		$content = myTruncate(strip_tags($archive['content']), 130, ' ', '...');
		$body .= '
			<div class="archive-post row">';
				
				$body .= '<div class="archive-post-info col res-23 mobile-fill">
				<p class="archive-title"><a href="'.$blogBase.$archive['controller'].'/">'.$archive['title'].'</a></p>
				<p class="name">Posted on: <span class="datestamp">'.cal_date($archive['date']).'</span></p>
				<p>'.$content.' <a class="read-more" href="'.$blogBase.$archive['controller'].'/">Read&nbsp;More</a></p>
				</div>';
				
				if($archive['img']){
					$body .= '<div class="archive-post-image col res-13 mobile-fill">
						<img src="/images/blog/'.$archive['img'].'" class="archive-image" alt="'.$archive['title'].'" >
					</div>';
				}
				
				$body .= '<a class="archive-cover-link" href="'.$blogBase.$archive['controller'].'/"> </a>
			</div>
		';
	}
	
	//Display pagination
	if($pageCount > 1) {
		$body .= '<div id="pagination">';
		$body .= ' <a ';
		if($page != 1){
			$prev = $page - 1;
			$body .= ' class="prev" href="'.$blogBase.$archiveBase.'/?page='.$prev.'" ';
		}
		$body .= 'title="Previous Page">Prev</a> ';
		
		$i = 1;
		while($i <= $pageCount && $pageCount != 1){
			if($i != $page){$body .= ' <a title="Jump to page '.$i.'" class="num" href="'.$blogBase.$archiveBase.'/?page='.$i.'">';}
			if($i == $page){$body .= ' <a class="current" >';}
			$body .= $i;
			$body .= '</a> ';
			$i++;
		}
		
		$body .= ' <a ';
		if($page != $pageCount){
			$next = $page + 1;
			$body .= ' class="next" href="'.$blogBase.$archiveBase.'/?page='.$next.'" ';
		}
		$body .= 'title="Next Page">Next</a> ';
		$body .= '</div>';
	}
}
?>
<?php include('support.php'); ?>
