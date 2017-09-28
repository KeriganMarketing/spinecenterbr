<?php
//turns off error reporting
//error_reporting(0);
ini_set('display_errors', 'On');
//connect to the site admin authentication database
require('db_connect.php');
include('functions.php');
$url =  $_SERVER['REQUEST_URI'];

/////////////////
//SESSION CHECK//
/////////////////
// Check for a cookie, if none got to login page
if(!isset($_COOKIE['session_id'])) {
	$isLoggedIn = FALSE;
}else{
	// Try to find a match in the database
	$GUID = mysql_real_escape_string($_COOKIE['session_id']);
	$query = "SELECT * FROM userTable WHERE GUID='$GUID'";
	//echo $query;
	$result = mysql_query($query);
	$record = mysql_fetch_assoc($result);
	//echo $record['user'];
	
	if(!$record) { // No match for guid
		$isLoggedIn = FALSE;
	}else{
		$isLoggedIn = TRUE;
		if($_COOKIE['remember'] != '1') {
			setcookie("session_id", $GUID, time()+240);
		}else{
			setcookie("session_id", $GUID);
		}
	}
}
if(!$isLoggedIn && $_GET['return'] == '') { // No match for guid
	if(isset($_POST['return'])){
		$url = $_POST['return'];
	}
	header('Location: /?return='.$url);
}

/////////////////////
//END SESSION CHECK//
/////////////////////


if(!$isLoggedIn){
	$templateFile = 'login.php';
}else{
	$templateFile = 'template.php';
	
	$query = "SELECT * FROM userTable WHERE GUID = '$GUID'";
	$result = mysql_query($query);
	$record = mysql_fetch_assoc($result);
	
	//user info
	$currentUser = $record['userID'];
	$currentUserName = $record['fName'];
	$currentUserFullName = $record['fName'].' '.$record['lName'];
	
	/////////////////
	//GET SITE INFO//
	/////////////////
	
	$siteID = 79;
	$filepath = '';
	
	//get DB info for site
	$siteQuery = "SELECT * FROM siteTable WHERE id = '$siteID'";
	$siteResult = mysql_query($siteQuery);
	if($siteResult){
		$siteRecord = mysql_fetch_assoc($siteResult);
	}else{
		$siteID = '';
	}
	////////////////////////////
	//FIGURE OUT SITE SPECFICS//
	////////////////////////////
	
	//array to check against allowed page types
	$pageTypes = array();
	if($siteRecord['pageTypes'] !=''){
		$pageTypes = explode(',',$siteRecord['pageTypes']);
		//print_r($pageTypes);
	}
	$site = $siteRecord['url'];
	$root = $siteRecord['root'];
	$siteRootFolder = $siteRecord['root'];
	//print_r($pageTypes);
//	$noPageSites = array(
//	);
	
	if($siteRecord['hasPages'] == '0'){
		$hasPages = FALSE;
	}else{
		$hasPages = TRUE;
	}
	
	if($siteRecord['hasUsers'] == '1'){
		$loginSystem = TRUE;
	}
	//list of page types that cannot be assigned as a pageType. IE page types that are specifc to feature box locations.
	$exemptPageTypes = array();
	
	//connect to site's specific database
	$db = $siteRecord['dbName'];
	$user = $siteRecord['dbUser'];
	$pass = $siteRecord['dbPass'];
	
	//load GA code
	$gacode = $siteRecord['GoogleAnalyticsCode'];
	
	//////////////////////
	//END SITE SPECIFICS//
	//////////////////////
}
//$url = filter_var($url, FILTER_SANITIZE_STRING); // Convert HTML tags into a buggery oblivion.
$c_url = str_replace('..', '', $url); // Remove .. folder jump to prevent d-baggery.

$r_url = explode("/", $c_url); // Create an array from the requested url (which is string).
array_shift($r_url); // Shift array left, as the first element is blank.
$cont = $_GET['cmd']; // This is the Controller; The main nav lists these.
$item = $_GET['action']; // This is the Item of content. Optional A number of a photo, specific piece in a portfolio, etc.

if($cont == ''){ $cont = 'pages'; }
$pages = array(
				'Manage the Pages of Your Site' => 'pages',
				'Manage Shared Sections' => 'specialSections',
				'Manage Your Calendar of Events' => 'eventCalendar',
				'Manage Your News Feed' => 'newsFeed',
				'Manage Your Photo Gallery' => 'photoGallery',
				'Manage Your Home Page Slideshow' => 'homeSlider',
				'Manage Your Newsletters' => 'newsletters',
				'Manage Your Forms' => 'forms',
				'Manage Your Contests' => 'contests',
				'Manage Your Video Gallery' => 'videoGallery',
				'Manage Your Episode Gallery' => 'episodeGallery',
				'Manage Your Blog' => 'blog',
				'Manage Your Downloadable Files' => 'downloadableForms',
				'Manage Your Portfolio' => 'portfolio',
				'Manage Your Clients' => 'clients',
				'Manage Your Products' => 'products',
				'Manage Your Office Locations' => 'officeLocations',
				'Manage Your Surveys' => 'survey',
				'Manage Your Availability' => 'availability',
				'&nbsp;Manage Your&nbsp; Testimonials' => 'testimonials',);
$specialPages = array(
				'Manage Forwarded URLs' => 'trackingURLs',
				'Manage Your Featured Video' => 'featuredVideo',
			 	'Manage Your Banner Ads' => 'bannerAds');
$globalPages = array('pages','stats');

//freaking overwrites
if($siteID == '42'){
	unset($pages['Manage Your Clients']);
	$pages['Manage Your Success Stories'] = 'clients';
	$functionName = 'Success Story';
}elseif($siteID == '69'){
	unset($pages['Manage Your Clients']);
	$pages['Manage Your Projects'] = 'clients';
	$functionName = 'Project';	
}elseif($siteID == '70'){
	unset($pages['Manage Your Clients']);
	$pages['Manage Your Products'] = 'clients';
	$functionName = 'Product';	
}elseif($siteID == '68'){
	unset($pages['Manage Your Clients']);
	$pages['Manage Your Team'] = 'clients';
	$functionName = 'Person';	
}elseif($siteID == '74'){
	unset($pages['Manage Your Clients']);
	$pages['Manage Your Recipes'] = 'clients';
	$functionName = 'Recipe';	
}else{
	$functionName = 'Client';	
}

//add stats page at end of list
$pages['View Your Web Statistics'] = 'stats';
//shift arrays if site has no pages
if(!$hasPages){
	//array_shift($pages);
	array_shift($globalPages);
}
//reset default to first non-system page type in array
if(!$hasPages && $cont == 'pages'){
	$cont = $pageTypes[0];
}
$exceptions = array('logout');
if($isSuperUser){
	array_push($exceptions,'super-user');
	$siteArray = get_site_array();
	$userArray = get_user_array();
}
/*if($cont == 'stats'){
	$templateFile = 'stats.php';
}*/

//now that this is all out of the way, we're going to make a master array of all the pages that need to show in the CMS navigation
$cmsPages = array('logout');
if($isSuperUser){
	array_push($cmsPages, 'super-user');
}
if(!$pageTypes){$pageTypes = array();}
foreach($pages as $key => $value){
	if(in_array($value,$pageTypes) || in_array($value,$globalPages)){
		array_push($cmsPages, $value);
	}
}
foreach($specialPages as $key => $value){
	if(in_array($value,$pageTypes) || in_array($value,$globalPages)){
		array_push($cmsPages, $value);
	}
}

if(in_array($cont,$cmsPages)){
	$pageFile = $cont.'.php';
	$pageInc = $cont.'.inc.php';
	$pageTitle = array_search($cont,$pages);
	if($pageTitle == ''){
		$pageTitle = array_search($cont,$specialPages);
	}
}elseif($templateFile != 'login.php'){
	$is404 = TRUE;
}else{
	$pageTitle = 'Log In';
}

if($is404){
	$templateFile = '404.php';
	$pageTitle = '404 - This Page Cannot Be Found.';
}

//SSL redirects
/*if($_SERVER['HTTPS'] != 'on' && $_COOKIE['secure'] != '0'){
	$url = 'https://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	header("Location: ".$url);
	die();
}
//noSSL redirects
if($_SERVER['HTTPS'] == 'on' && $_COOKIE['secure'] == '0'){
	$url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	header("Location: ".$url);
	die();
}*/

//echo $cont;
?>