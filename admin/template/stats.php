<?php
include('content/'.$pageInc);
include('includes/page-top.php');
?>
<div id="page-container" class="stats">
<?php
if($isSuperUser){
	//$siteArray = get_site_array(); //now located in config.php file
	//print_r($siteArray);
	$siteMenu = '<form style="display: block; text-align: right; margin: -23px 0 23px 0;" action="/?cmd='.$cont.'" method="post">'."\r\n";
	$siteMenu .='<select name="changeSiteID" class="dd" onChange="form.submit();">'."\r\n";
	foreach($siteArray as $key => $value){
		$siteMenu .= '<option value="'.$key.'" ';
		if($key == $_COOKIE['siteID']){
			$siteMenu .= 'selected ';
		}
		$siteMenu .= '>'.$value.'</option>'."\r\n";
	}
	$siteMenu .= '</select>'."\r\n";
	$siteMenu .= '<noscript><input style="" type="submit" value="Change" /></noscript>'."\r\n";
	$siteMenu .= '</form>'."\r\n";
	echo $siteMenu;
}
?>
<div id="stats">
    <div class="top-cap"></div><div class="body">
	<p class="stats-prev"><a href="/">Return to Previous Page</a></p>
    <p id="welcome">Welcome, <?php echo $currentUserName; ?></p>
<?php include('content/'.$pageFile); ?>
    </div><div class="bot-cap"></div>
</div>
<?php
include('includes/footer.php');
?>
</div>
