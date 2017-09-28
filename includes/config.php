<?php // Thanks Grant for the initial framework for this system. http://grantgingell.com

if ($_GET['r'] != 'false' && $_POST['r'] != 'false') {//special GET to keep the page from redirecting
    include('mobile-detect.php');
    $detect = new Mobile_Detect;

    // 1. Check for mobile environment.
    if ($detect->isMobile()) {
        $isMobile = true;
    }

    // 2. Check for tablet device.
    if ($detect->isTablet()) {
        $isTablet = true;
    }

    // 3. Check for any mobile device, excluding tablets.
    if ($detect->isMobile() && !$detect->isTablet()) {
        $isPhone = true;
    }

    // 4. Keep the value in $_SESSION for later use
    //    and for optimizing the speed of the code.
    if (!$_SESSION['isMobile']) {
        $_SESSION['isMobile'] = $detect->isMobile();
    }

    /*	// 5. Redirect the user to your mobile version of the site.
        if($detect->isMobile()){
            header('http://m.yoursite.com', true, 301);
        }*/
}

//ini_set('display_errors', 'On');
$site_url = 'http://spinecenterbr.com/'; // used for absolute links.

$img_dir = '/images/'; // image directory.
$template = 'template'; // Name of the directory containing the sites design files, I.E. sylesheets, home.php, support.php, template images, etc...
$template_dir = '/'.$template.'/';

// $site_url_404 = 'http://www.georgekelley.net/404.php';
$site_name = 'The Spine Center at Bone & Joint Clinic of Baton Rouge'; // Site name 70 char max

# CHEAT SHEET
# PAGE CONTENT = $pageContent
# SITE TITLE = $siteTitle
# PAGE TITLE = $pageTitle
# SITE NAVIGATION = include('includes/nav.php');

require('db_connect.php');

//include('../../functions.php');
include('functions.php');

// Connect to MySQL server
if (!($database = mysql_connect($host, $db_user, $db_pass))) {
    die("Could not connect to database" . mysql_error());
}

// open database
if (!mysql_select_db($db, $database)) {
    die("Could not open database" . mysql_error());
}

$url =  $_SERVER['REQUEST_URI'];
//$url = filter_var($url, FILTER_SANITIZE_STRING); // Convert HTML tags into a buggery oblivion.
$c_url = str_replace('..', '', $url); // Remove .. folder jump to prevent d-baggery.

$r_url = explode("/", $c_url); // Create an array from the requested url (which is string).
array_shift($r_url); // Shift array left, as the first element is blank.
$cont = $r_url[0]; // This is the Controller; The main nav lists these.
$item = $r_url[1]; // This is the Item of content. Optional A number of a photo, specific piece in a portfolio, etc.

//create array of allowable page controllers from the database
$pages = [];
$controller_query = "SELECT controller FROM pageTable WHERE parent IS NULL"; // create query to get all page controllers from the database
$controller_auth = mysql_query($controller_query, $database) or die(mysql_error()); // execute query

while ($row=mysql_fetch_array($controller_auth)) {
    array_push($pages, $row['controller']); // push results to array
}

$is404 = true;

//$pages = array('home', 'about', 'physicians', 'specialties', 'sports-medicine', 'services', 'patient-center', 'faq', 'glossary', 'contact', 'directions', 'enewsletter', 'video', 'success', 'error', ''/*<-- this last '' is important... otherwise home page request will be rejected*/); // This could be pulled from a databse. They're the main pages for the site.

// Special case where no controller is specified:
if ($cont == 'home') {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: /");
    die();
}
if ($cont == '' || $cont[0] == '?') {
    $cont = 'home';
}
if ($item == 'view') {
    $item = '';
}

//moving user around for SEO-sake...
if (substr($url, -1)!='/' && $cont != 'home' && $item != 'sitemap.xml' && $cont[0] != '?' && $item[0] != '?' && substr($item, -4) != '.pdf') {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: ".$url."/");
    die();
}

// Check if the requested Controller is in the list of allowed pages:
if (in_array($cont, $pages)) {//Set up a 404 content bit.
    $is404 = false;
}

//print_r($pages);

//create array of allowable page controllers from the database
$subpages = [];
$controller_query = "SELECT controller FROM pageTable WHERE parent IS NOT NULL"; // create query to get all page controllers from the database
$controller_auth = mysql_query($controller_query, $database) or die(mysql_error()); // execute query

while ($row = mysql_fetch_array($controller_auth)) {
    array_push($subpages, $row['controller']); // push results to array
}

// Check if the requested item is in the list of allowed pages:
if (in_array($item, $subpages) && $item != '') {//Set up a 404 content bit.
    $is404 = false;
}


// Query pageTable for contents of the page we are currently on.

if ($item != '') {// This is true when there is actually an item requested...
    $content_query = "SELECT * FROM pageTable WHERE controller='$item' AND parent='$cont'";
} else { // here we assume the user just wants to view the controller.
    $content_query = "SELECT * FROM pageTable WHERE controller='$cont' AND parent is NULL";
}

$result = mysql_query($content_query, $database); // Make sure we can make the connection
if (!($result)) {
    print("Could not execute query! <br />");
    die(mysql_error());
} else {
    $record = mysql_fetch_assoc($result); // fetch associated data, now we can get the contents of a column using the syntax $record['column']
}

//get the page title elements
if ($item != '') { // This is true when there is actually an item requested...
    $parentTitle = mysql_fetch_assoc(mysql_query("SELECT title, headline, featuredImage FROM pageTable WHERE controller='$cont' AND parent is NULL", $database));
    $parent_title = $parentTitle['title'];
    $page_name = $parent_title.': '.$record['title'];
} else { // here we assume the user just wants to view the controller.
    $page_name = $record['title'];
}

$pageName =  $record['title'];
$pageID = $record['pageID'];
$headline = $record['headline'];
$featuredImage =  $record['featuredImage'];

//Define any dead parents
$deadpages = [];
$dead_query = "SELECT controller FROM pageTable WHERE dead='1'"; // create query to get all page controllers from the database
$dead_auth = mysql_query($dead_query, $database) or die(mysql_error()); // execute query

while ($dead=mysql_fetch_array($dead_auth)) {
    array_push($deadpages, $dead['controller']); // push results to array
}
if ($item == '') {
    if (in_array($cont, $deadpages)) {
        $is404 = true;
    } elseif (in_array($cont, $pages)) {
        $is404 = false;
    }
} else {
    if (!(in_array($item, $subpages))) {
        $is404 = true;
    }
}
//if(!$is404){ echo 'valid!'; } else { echo '404!'; }

//Define the title bar title
if ($is404) {// 404 site title
    header("Status: 404 Not Found");
    header("HTTP/1.0 404 Not Found");
    $siteTitle = '404 Not Found | '.$site_name;
} elseif ($cont =='home') {
    $siteTitle = $site_name;
} else { // Regular site title
    $siteTitle = $page_name.' | '.$site_name; //shows the site title.
}

// Defint the page title
if ($is404) { // 404 page title
    $pageTitle = '404 Not Found';
    $headline = '404 Not Found';
} else { // Regular page title
    $pageTitle = $page_name; //shows the page title.
}

//let us know if the page is searchable
if ($pageRecord{'searchIndex'} == '1') {
    $isSearchable = true;
} elseif ($is404) {
    $isSearchable = false;
} else {
    $isSearchable = false;
}

// Defint the page content (HTML)

//if($record['pageContent'] == '')
//{ $is404 = TRUE; }// 404 if there is no page content

if ($is404) {
    $pageContent = '<p style="margin-left:50px;"><br><br><br><br><span style="font-size:30px;"> Oops... </span>The page you\'ve requested does not exist. <br><br><a style="color:#333;" href="/">Click here to return to home page.</a></p>';
} else {
    $pageContent = $record['pageContent'];
}

$pageType = $record['pageType'];
if ($is404) {
    $pageType = 'support';
}

// Define page file.
$pageFile = $template .'/'. $pageType. '.php';
// for dev purposes
//echo '<h3>Controller: '.$cont.'</h3><br>';
//echo '<h3> Item: '.$item.'</h3>';
//echo '<h2>C_l: '.$content_left.'</h2>';
//echo '<h2>C_r: '.$content_right.'</h2>';
//echo '<h2>C_r: '.$r_cont.'</h2>';
//echo '<h2>'.$url.'</h2>';
//echo '<h1>'.$site_title.'</h1><br>';
//echo 'Page Content: '.$pageContent.'<br>';
//print_r($pages);

//sitemap override
if ($cont == 'sitemap.xml' || $cont == 'sitemap') {
    include('sitemap.php');
    die();
}
