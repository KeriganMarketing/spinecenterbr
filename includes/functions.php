<?php

function myTruncate($string, $limit, $break=".", $pad="...") { // return with no change if string is shorter than $limit  
	if(strlen($string) <= $limit) return $string; // is $break present between $limit and the end of the string?  
	if(false !== ($breakpoint = strpos($string, $break, $limit))) { if($breakpoint < strlen($string) - 1) { $string = substr($string, 0, $breakpoint) . $pad; } } 
	return $string;
}

function strposOffset($search, $string, $offset, $pad) {
    /*** explode the string ***/
    $arr = explode($search, $string, $offset);
    /*** check the search is not out of bounds ***/
    switch( $offset )
    {
        case $offset == 0:
        return $search;
        break;
    
        case $offset > count($arr):
        return $search;
        break;

        default:
        return implode($search, $arr).$pad;
    }
}

function get_breadcrumb($currentCont,$currentItem,$currentName,$parentName){
	
	$string = '<a class="breadcrumb" href="/" >Home</a>';
	
	if($currentItem != ''){
		$currentPage = $currentCont;
		$query = "SELECT dead FROM pageTable WHERE controller='$currentPage' LIMIT 1";
		$result = mysql_query($query);
		$record = mysql_fetch_assoc($result);
	}
	
	if($currentItem == ''){
		$string .= ' &rsaquo; <span class="breadcrumb" >'.$currentName.'</a>';
	} else {
		if($record['dead'] == '1'){
			$string .= ' &rsaquo; <span class="breadcrumb" >'.$parentName.'</span>';
			$string .= ' &rsaquo; <span class="breadcrumb" >'.$currentName.'</span>'; 
		} else {
			$string .= ' &rsaquo; <a class="breadcrumb" href="/'.$currentCont.'/">'.$parentName.'</a>';
			$string .= ' &rsaquo; <span class="breadcrumb" >'.$currentName.'</span>'; 
		}
	}		
	
	return $string;              
}

function get_link($linkedID, $linktype){	

	if($linktype == 'page'){
		$query = "SELECT * FROM pageTable WHERE pageID='$linkedID' LIMIT 1";
		$result = mysql_query($query);
		$record = mysql_fetch_assoc($result);
		
		if($record['parent']!=''){
			$string = '/'.$record['parent'].'/'.$record['controller'].'/';
		}else{		
			$string = '/'.$record['controller'].'/';
		}
	} else {
		$string = '#';
	}
	
	return $string;
}

function get_index($content){
	
	$matches = preg_split("/[\s,]+/", $content);
	//print_r($matches);
	$output = '<ul>';
	foreach ($matches as $key => $value) {
		$needle = 'name=';
		if ( strpos( $needle, $value ) !== FALSE ) {
			$m = explode("/=/", $value);
			$output .= '<li><a href="'.$m[1].'">'.$m[1].'</a></li>';
			print_r($m);
		}
	}
	$output .= '</ul>';
	
    return $output;
}

function get_video($videoID, $hideTitle, $category, $image ){
	if(is_numeric($videoID)){
		$query = "SELECT * FROM videos WHERE id='$videoID' ORDER BY RAND() LIMIT 1";
		$result = mysql_query($query);
							
	}elseif($videoID == 'featured'){
		$query = "SELECT * FROM videos WHERE featured='1' LIMIT 1";
		$result = mysql_query($query);

	}elseif($videoID == 'all'){
		if($category != ''){
			$query = "SELECT * FROM videos WHERE category='$category' ORDER BY navOrder";
			$result = mysql_query($query);
			
		}else{
			$query = "SELECT * FROM videos ORDER BY navOrder";
			$result = mysql_query($query);
		}
	}elseif($videoID == 'random'){
		if($category != ''){
			$query = "SELECT * FROM videos WHERE category='$category' ORDER BY RAND() LIMIT 1";
			$result = mysql_query($query);
			
		}else{
			$query = "SELECT * FROM videos ORDER BY RAND() LIMIT 1";
			$result = mysql_query($query);
		}
	}
	$string = '';
	while($record = mysql_fetch_assoc($result)){
		if($image != '' && $image != 'none' && $image != 'list'){
			$videoImage = $image;
			if($image == 'youtube'){
				$videoImage = 'http://i.ytimg.com/vi/'.$record['url'].'/0.jpg';
			}
			
			if($videoID != ''){
				$string .= '
				<div class="video">
				<a href="http://www.youtube.com/v/'.$record['url'].'?autostart=true" class="fancyvideo" title="'.$record['description'].'">';
				if($hideTitle){ $string .= '<p>'.$record['title'].'</p>'; }
				$string .= '<div class="image-crop" style="background:url('.$videoImage.') center no-repeat; ">
					<div class="play-button"></div>
					</div></a>
				</div> ';
			}
				
		}
		if($image == 'none'){
			if($videoID != ''){
				$string .= '
				<div class="videolink">
				<a href="http://www.youtube.com/v/'.$record['url'].'?autostart=true" class="fancyvideo" title="'.$record['description'].'">';
				if($hideTitle){ $string .= '<p>'.$record['title'].'</p>'; }
				$string .= '</a>
				</div> ';
			}
			
		}
		
		if($image == 'list'){
			if($videoID != ''){
				$string .= '
				<li><a href="http://www.youtube.com/v/'.$record['url'].'?autostart=true" class="fancyvideo" title="'.$record['description'].'">';
				if($hideTitle){ $string .= $record['title']; }
				$string .= '</a></li>';
			}
			
		}
		
	
	}
	return $string;
}

function get_testimonial($testimonialID, $pullType, $limit){
	if($pullType == 'static'){
		$query = "SELECT * FROM testimonials WHERE id='$testimonialID' ORDER BY RAND() LIMIT 1";
		$result = mysql_query($query);
		$record = mysql_fetch_assoc($result);
		$strippedContent = strip_tags($record['content']);
		$quote = myTruncate($strippedContent, $limit, " ");
		$author = strip_tags($record['author']);			
	}elseif($pullType == 'random'){
		$query = "SELECT * FROM testimonials WHERE display='1' ORDER BY RAND() LIMIT 1";
		$result = mysql_query($query);
		$record = mysql_fetch_assoc($result);
		$strippedContent = strip_tags($record['content']);
		$quote = myTruncate($strippedContent, $limit, " ");
		$author = strip_tags($record['author']);	
	}
	$string = '<p class="quote"><span class="openquote">&#8220;</span>'.$quote.'<span class="closequote">&#8221;</span></p>
				<p class="author">&#8211; '.$author.'</p>';
	
	return $string;
}

function get_section($sectionID){	

	if(is_numeric($sectionID)){
		$query = "SELECT * FROM specialSections WHERE ID=$sectionID LIMIT 1";
		$result = mysql_query($query);
		$record = mysql_fetch_assoc($result);
			
		$string = $record['content'];

	} else {
		$string = 'invalid section call';
	}
	
	return $string;
}
	
function embed_files($input){
	$tagOne = "[file]";
	$tagTwo = "[/file]";
	
		while(strpos($input,$tagOne)){ 
			
			$startTagPos = strrpos($input, $tagOne);
			$endTagPos = strrpos($input, $tagTwo);
			$tagLength = $endTagPos - $startTagPos +7;
			$id = substr($input, $startTagPos+6);
			$id = explode($tagTwo, $id);
			$id = $id[0];
			
			$query = "SELECT * FROM files WHERE id='$id'";
			$result = mysql_query($query);
			$record = mysql_fetch_assoc($result);
			
			$replacement = '<a href="/downloads/'.$record['file'].'">'.$record['name'].'</a>';
			
			$input = (substr_replace($input, $replacement, $startTagPos, $tagLength));
		}
	return $input;
}

function embed_forms($input, $post = '', $get = '', $errors = '', $landing = '', $error_msg = ''){
	$tagOne = "[form]";
	$tagTwo = "[/form]";
	
	if(strpos($input,$tagOne)){
			
		$startTagPos = strrpos($input, $tagOne);
		$endTagPos = strrpos($input, $tagTwo);
		$tagLength = $endTagPos - $startTagPos +7;
		$id = substr($input, $startTagPos+6);
		$id = explode($tagTwo, $id);
		$id = $id[0];
	
		$query = "SELECT * FROM forms WHERE id='$id' LIMIT 1";
		$result = mysql_query($query);
		$record = mysql_fetch_assoc($result);
		
		$instructions = $record['instructions'];
		$formContacts = $record['form_contacts'];
		$buttonText = $record['button_text'];
		
		if($instructions != ''){
			$output .= '
				<p class="cms-form-instructions">'.$instructions.'</p>
		<p><span style="font-size:12px;">Required fields are marked with an asterisk (<span class="req-ast">*</span>)</span></p>
			';
		}
		if(!$errors){
			$output .= '
				<form class="cms-form" id="form-'.$id.'" enctype="multipart/form-data" method="post" action="/'.$landing.'">
			';
			
		}else{
			$output .= '
				<p class="cms-form-instructions" style="color:red;">'.$error_msg.'</p>
				<form class="cms-form" id="form-'.$id.'" enctype="multipart/form-data" method="post" action="/'.$landing.'">
			';
		}
		
		//Get form contents
		$query = "SELECT * FROM form_questions WHERE fID='$id' ORDER BY sortOrder";
		$result = mysql_query($query);
		$i = 1;
		while($record = mysql_fetch_assoc($result)){
			$output .= '<p class="question text q'.$record['id'];
			if(in_array($record['id'],$errors)){
				$output .= ' highlight';
			}
			$output .= '">'.$record['question'].'';
			if($record['required'] == '1'){
				$output .= ' <span class="req-ast">*</span>';
			}
			
			$output .= '<br/>';
			if($record['type'] == 'text'){
				//$output .= '<input type="text" class="text" name="'.$record['id'].'" value="'.$post[$record['id']].'" />';
				if($record['errorReporting'] != '' ){
					if($record['errorReporting'] == 'captcha' ){
						$output .= '
						<div id="captcha-box" style="margin-top:5px;">
						<img id="captcha" src="/securimage/securimage_show.php" alt="CAPTCHA Image" style="border:1px solid #CCC;margin-bottom:-7px;" /> &nbsp; 
						<input type="text" class="text" name="captcha_code" size="10" maxlength="6" style="width:50%;" />
						</div>';
					}
					if($record['errorReporting'] == 'email' ){
						$output .= '<input ';
						if($isMobile){
							$output .= 'type="email" class="text"';
						} else { $output .= 'type="text" class="text"'; }
						$output .= ' name="'.$record['id'].'" value="'.$post[$record['id']].'" id="qi'.$record['id'].'" style="width:300px; max-width:100%;" />';
					}
					if($record['errorReporting'] == 'phone' ){
						$output .= '
						<input type="hidden" name="'.$record['id'].'[]" value="phonenumber" /> 
						<input type="text" class="text" name="'.$record['id'].'[]" onkeyup="toNext(this,3,\'ph2\')" value="" id="ph1" />-<input type="text" class="text" name="'.$record['id'].'[]" onkeyup="toNext(this,3,\'ph3\')" value="" id="ph2" />-<input type="text" class="text" name="'.$record['id'].'[]" maxlength="4" value="" id="ph3" /> <span class="q-note">(e.g. 850-123-4567)</span>';
					}
					if($record['errorReporting'] == 'date' ){
						$output .= '<input ';
						if($isMobile){
							$output .= 'type="date" class="text"';
						} else { $output .= 'type="text" class="text datepicker"'; }
						$output .= ' name="'.$record['id'].'" value="'.$post[$record['id']].'" id="qi'.$record['id'].'" /> <span class="q-note">(e.g. MM/DD/YYYY)</span>';
					}
					if($record['errorReporting'] == 'time' ){
						$output .= '<select name="'.$record['id'].'[]" id="qi'.$record['id'].'" class="dd">
							<option ';
							if($record['id'].'[0]' =='' || $record['id'].'[1]' == '8'){ $output .= 'selected="selected"'; } 
							$output .= ' value="8">8</option>
							<option ';
							if($record['id'].'[0]' == '9'){ $output .= 'selected="selected"'; } 
							$output .= ' value="9">9</option>
							<option ';
							if($record['id'].'[0]' == '10'){ $output .= 'selected="selected"'; } 
							$output .= ' value="10">10</option>
							<option ';
							if($record['id'].'[0]' == '11'){ $output .= 'selected="selected"'; } 
							$output .= ' value="11">11</option>
							<option ';
							if($record['id'].'[0]' == '12'){ $output .= 'selected="selected"'; } 
							$output .= ' value="12">12</option>
							<option ';
							if($record['id'].'[0]' == '1'){ $output .= 'selected="selected"'; } 
							$output .= ' value="1">1</option>
							<option ';
							if($record['id'].'[0]' == '2'){ $output .= 'selected="selected"'; } 
							$output .= ' value="2">2</option>
							<option ';
							if($record['id'].'[0]' == '3'){ $output .= 'selected="selected"'; } 
							$output .= ' value="3">3</option>
							<option ';
							if($record['id'].'[0]' == '4'){ $output .= 'selected="selected"'; } 
							$output .= ' value="4">4</option>
							<option ';
							if($record['id'].'[0]' == '5'){ $output .= 'selected="selected"'; } 
							$output .= ' value="5">5</option>
						</select>:<select name="'.$record['id'].'[]" id="qi'.$record['id'].'" class="dd">
							<option ';
							if($record['id'].'[1]' =='' || $record['id'].'[1]' == '00'){ $output .= 'selected="selected"'; } 
							$output .= ' value="00" >00</option>
							<option ';
							if($record['id'].'[1]' == '15'){ $output .= 'selected="selected"'; } 
							$output .= 'value="15">15</option>
							<option ';
							if($record['id'].'[1]' == '30'){ $output .= 'selected="selected"'; } 
							$output .= 'value="30">30</option>
							<option ';
							if($record['id'].'[1]' == '45'){ $output .= 'selected="selected"'; } 
							$output .= 'value="45">45</option>
						</select>
						<select name="'.$record['id'].'[]" id="qi'.$record['id'].'" class="dd">
							<option ';
							if($record['id'].'[2]' == '' || $record['id'].'[2]' == 'am'){ $output .= 'selected="selected"'; } 
							$output .= ' value="am">am</option>
							<option ';
							if($record['id'].'[2]' == 'pm'){ $output .= 'selected="selected"'; } 
							$output .= 'value="pm">pm</option>
					    </select>
						<span class="q-note">Mon thru Thurs: 8:00am - 4:30pm  / Fri: 8:00am - 12:00pm</span>';
					}
				}else{
					$output .= '<input type="text" class="text" name="'.$record['id'].'" value="'.$post[$record['id']].'"  id="qi'.$record['id'].'" style="width:300px; max-width:100%;" />';
				}
				$output .= '<br/>'."\r\n";
			}elseif($record['type'] == 'textarea'){
				$output .= '<textarea class="textarea" name="'.$record['id'].'"  style="width:'.$record['width'].'%; height:'.$record['height'].'px;" id="q'.$record['id'].'">'.$post[$record['id']].'</textarea><br/>'."\r\n";
			}elseif($record['type'] == 'radio'){
				$que = "SELECT * FROM form_answers WHERE qID='".$record['id']."' ORDER BY id ASC";
				$res = mysql_query($que);
				while($rec = mysql_fetch_assoc($res)){
					$output .= '<label class="radio">';
					if($record['id'] == '2'){
						$output .= '<span class="option-block">';
					}else{
						$output .= '<span class="option-inline">';
					}
					$output .= '<input type="radio" class="radio" name="'.$record['id'].'" value="'.$rec['id'].'"';
					if($rec['id'] == $post[$record['id']]){
						$output .= ' checked';
					}
					$output .= ' />&nbsp;'.$rec['answer'].'</span></label>'."\r\n";
				}
			}elseif($record['type'] == 'check'){
				$que = "SELECT * FROM form_answers WHERE qID='".$record['id']."' ORDER BY id ASC";
				$res = mysql_query($que);
				while($rec = mysql_fetch_assoc($res)){
					$output .= '<label class="radio"><input type="checkbox" class="check" name="'.$record['id'].'[]" value="'.$rec['id'].'"';
					if(is_array($post[$record['id']])){
						if(in_array($rec['id'],$post[$record['id']])){
							$output .= ' checked';
						}
					}
					$output .= ' />&nbsp;'.$rec['answer'].'</label><br/>'."\r\n";
				}
			}elseif($record['type'] == 'dropdown'){
				$output .= '<select class="dd" name="'.$record['id'].'">'."\r\n";
				$que = "SELECT * FROM form_answers WHERE qID='".$record['id']."' ORDER BY id ASC";
				$res = mysql_query($que);
				while($rec = mysql_fetch_assoc($res)){
					$output .= '<option value="'.$rec['id'].'"';
					if(in_array($rec['id'],$post[$record['id']])){
						$output .= ' selected';
					}
					$output .= '>'.$rec['answer'].'</option>'."\r\n";
				}
				$output .= '</select><br/>'."\r\n";
			}
			$output .= '</p>'."\r\n";
			$i++;
		}
		
		$output .= '<br />
			<input type="hidden" class="control" value="'.$id.'" name="formID">
			<input type="text" class="control" value="formSubmit" style="position:absolute;top:-50px;left:-100px;" name="control">
			<input type="submit" class="cta" value="'.$buttonText.'">
			</form>
			<br><br>
		';
		$input = (substr_replace($input, $output, $startTagPos, $tagLength));
		$input = embed_forms($input);
	}else{
		$input = $input;
	}
	return $input;
}

function social_bookmarks($fb,$twit,$digg,$del,$goog,$ms,$stum,$slash){
	//display the links/icons
	$sb = '';		
	$sb .= '<div style="float: right;">' . "\n"; //Open the containing div
	$sb .= 'Share:&nbsp;' . "\n";
	
	if($fb != '0'){//Facebook
		$sb .= '<a href="#" id="sb-facebook" target="_blank"><img height="20px" src="/gfx/facebook.gif" alt="Facebook" /></a>' . "\n";
	}
	if ($twit != '0'){//Twitter
		$sb .= '<a href="#" id="sb-twitter" target="_blank"><img height="20px" src="/gfx/twitter.gif" alt="Twitter" /></a>' . "\n";
	}
	if ($digg != '0'){//Digg
		$sb .= '<a href="#" id="sb-digg" target="_blank"><img height="20px" src="/gfx/digg.gif" alt="Digg" /></a>' . "\n";
	}
	if ($del != '0'){//Delicions
		$sb .= '<a href="#" id="sb-delicious" target="_blank"><img height="20px" src="/gfx/delicious.gif" alt="Del.icio.us" /></a>' . "\n";
	}
	if ($goog != '0'){//Google
		$sb .= '<a href="#" id="sb-google" target="_blank"><img height="20px" src="/gfx/google.gif" alt="Google" /></a>' . "\n";
	}
	if ($ms != '0'){//Myspace
		$sb .= '<a href="#" id="sb-myspace" target="_blank"><img height="20px" src="/gfx/myspace.gif" alt="MySpace" /></a>' . "\n";
	}
	if ($stum != '0'){//Stumble               {
		$sb .= '<a href="#" id="sb-stumbleupon" target="_blank"><img height="20px" src="/gfx/stumbleupon.gif" alt="StumbleUpon" /></a>' . "\n";
	}
	if ($slash != '0'){//Slash-dot
		$sb .= '<a href="#" id="sb-slashdot" target="_blank"><img height="20px" src="/gfx/slashdot.gif" alt="SlashDot" /></a>' . "\n";
	}	
	$sb .= '</div>';//end links/icons
	
	//begin javascript
	$sb .= 
	'<!-- Javascript to generate URLs and put them where they belong -->
	<script type="text/javascript">
		var m = document.getElementsByTagName(\'meta\');
		var desc = "";
		for(var i in m){
			if(m[i].name == "Description"){
				desc = m[i].content;
				break;
			}
		}';
	if($fb != '0'){//Facebook
		$sb .= 'var facebookurl = "http://www.facebook.com/sharer.php?u=" + window.location + "&t=" + document.title;
		document.getElementById(\'sb-facebook\').href = facebookurl;' . "\n";
	}
	if ($twit != '0'){//Twitter
		$sb .= 'var twitterurl = "http://twitter.com/share?original_referer=" + window.location;
		document.getElementById(\'sb-twitter\').href = twitterurl;' . "\n";
	}
	if ($digg != '0'){//Digg
		$sb .= 'var diggurl = "http://digg.com/submit?url=" + window.location + "&title=" + document.title+ "&bodytext=" + desc;
		document.getElementById(\'sb-digg\').href = diggurl;' . "\n";
	}
	if ($del != '0'){//Delicions
		$sb .= 'var deliciousurl = "http://delicious.com/post/?url=" + window.location + "&title=" + document.title + "&tags=" + desc;
		document.getElementById(\'sb-delicious\').href = deliciousurl;' . "\n";
	}
	if ($goog != '0'){//Google
		$sb .= 'var googleurl = "http://google.com/bookmarks/mark?op=add&bkmk=" + window.location + "&title=" + document.title +  "&annotation=" + desc;
		document.getElementById(\'sb-google\').href = googleurl;' . "\n";
	}
	if ($ms != '0'){//Myspace
		$sb .= 'var myspaceurl = "http://myspace.com/Modules/PostTo/Pages/?c=" + window.location + "&t=" + document.title;
		document.getElementById(\'sb-myspace\').href = myspaceurl;' . "\n";
	}
	if ($stum != '0'){//Stumble               {
		$sb .= 'var stumbleuponurl = "http://stumbleupon.com/submit?url=" + window.location + "&title=" + document.title + "&tagnames=" + desc;
		document.getElementById(\'sb-stumbleupon\').href = stumbleuponurl;' . "\n";
	}
	if ($slash != '0'){//Slash-dot
		$sb .= 'var slashdoturl = "http://slashdot.org/slashdot-it.pl?op=basic&url=" + window.location;
		document.getElementById(\'sb-slashdot\').href = slashdoturl;' . "\n";
	}
	$sb .= '</script>';//end javascript
	
	return $sb;
}

//cleanse form data
function clean($string){
	if(is_array($string)){
		$array = array();
		foreach($string as $key=>$value){
			if(!is_numeric($value)){
				$value = clean($value);
			}
			$array[$key] = $value;
		}
		$var = $array;
	}else{
		if(!is_numeric($string)){
			$string = mysql_real_escape_string($string);
		}
		$var = $string;
	}
	return $var;
}
//date manipulation
function cal_date($input){
	//input will be formatted YYYY/MM/DD
	$input = explode('/',$input);
	$y = $input[0];
	$m = $input[1];
	$d = $input[2];
	$date = $m.'/'.$d.'/'.$y;
	return $date;
}
function server_date($input){
	//input will be formatted YYYY/MM/DD
	$input = explode('/',$input);
	$m = $input[0];
	$d = $input[1];
	$y = $input[2];
	$date = $y.'/'.$m.'/'.$d;
	return $date;
}
function crop($string,$count,$tail='...'){
	if(strlen($string)<$count){
		$string = $string;
	}else{
		$string = substr($string,0,$count).$tail;
	}
	return $string;
}
//remove chars that aren't allowed in the cookie name
function clean_cookie($input){
	$output = str_replace('/','',str_replace('.','',str_replace(' ','',$input)));
	return $output;
}

//parse a url with get variable
function parse_gets($url){
	$url = explode('?',$url);
	$vars = explode('&',$url[1]);
	foreach($vars as $value){
		$explode = explode('=',$value);
		$new_vars[$explode[0]] = $explode[1];
	}
	return $new_vars;
}

//export array to csv
function getExcelData($data){
	$retval = "";
	if(is_array($data) && !empty($data)){
		$row = 0;
		foreach($data as $_data){
			if(is_array($_data) && !empty($_data)){
				if($row == 0){
					// write the column headers
					$retval = implode(",",array_keys(str_replace(',','',$_data)));
					$retval .= "\n";
				}
				//create a line of values for this row...
				$retval .= implode(",",array_values(str_replace(',','',$_data)));
				$retval .= "\n";
				//increment the row so we don't create headers all over again
				$row++;
			}
		}
	}
	return $retval;
}

function email_validator($email){
	if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email) || $email == ''){ //check that email is in proper format
		return false;
	}else{
		return true;
	}
}

?>