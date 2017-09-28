<?php 
//ini_set('display_errors', 'On');

if($pageID == 24){ $blogID = '2'; } //mccarthy
if($pageID == 26){ $blogID = '3'; } //harrod
if($pageID == 28){ $blogID = '4'; } //landry
if($pageID == 29){ $blogID = '1'; } //domangue
if($pageID == 30){ $blogID = '5'; } //bergeron
if($pageID == 31){ $blogID = '6'; } //jacomine
if($pageID == 32){ $blogID = '7'; } //jasper
if($pageID == 33){ $blogID = '8'; } //bozeman
if($pageID == 34){ $blogID = '9'; } //adaire

$sidebar = '';

//get subpage array
$query = "SELECT controller, title FROM pageTable WHERE parent='$cont' ORDER BY navOrder ASC";
	
if(!$result = mysql_query($query)){
	die(mysql_error());
}
$index = '<div id="page-links"><p class="index-title">'.$pageName.'</p><ul class="pagelinks">';
while($record = mysql_fetch_assoc($result)){
	$index .= '<li><a class="pagelink';
	if($record['controller'] == $item){ $index .=' active'; }
	$index .='" href="/'.$cont.'/'.$record['controller'].'">'.$record['title'].'</a></li>';
}
$index .= '</ul></div>';

$sidebar .= $index;

$articleQuery = "SELECT * FROM blogPosts WHERE active='1' AND category='$blogID' ORDER BY date DESC LIMIT 3";

$articles = '';
$i = 0;
$articleResult = mysql_query($articleQuery);
while($articleRecord = mysql_fetch_assoc($articleResult)){
	$articles .= '<p><a href="/education-resources/spine-articles/view/'.$articleRecord['controller'].'" >'.myTruncate($articleRecord['title'], 60, " ").'</a>
	<br>'.cal_date($articleRecord['date']).'</p>';
	$i++;
}

if($i > 0){
	$physicianArticles = '<div id="articles">
	<p class="physician-articles">Spine Articles by<br> '.$headline.'</p>
    '.$articles.'
	</div>';
}
		
include('includes/header.php'); ?>
	<div id="minibar">
        <div class="container" class="mobile-hide">
            <div class="col res-14 spacer">
            </div>
            <div id="breadcrumbs" class="col res-34">
                <?php echo get_breadcrumb($cont,$item,$pageName,$parent_title); ?>
            </div>
        </div>
    </div>
    <div id="mid" class="support">
    	<div class="container">
        	<div class="row">
                <div id="sidebar" class="col res-14 ph-1 mobile-hide">
                <?php if($sidebar!=''){ echo $sidebar; } ?>
                </div>
                <div id="copy-block" class="col res-34 support ph-1">
                    <div id="docPhoto" class="col res-13 ph-1">
                        <img alt="Physician Photo" src="/images/uploads/<?php echo $featuredImage; ?>" alt="<?php echo $pageName; ?>" />
                        <?php echo $physicianArticles; ?>
                    </div>
                	<div id="docBio" class="col res-23 ph-1">
                    <h1><?php if($headline != ''){ echo $headline; } else { echo $pageTitle; } ?></h1>
                    <?php echo $pageContent; 
                            if($body != ''){
                                echo $body;
                            }
                        ?>
                    </div>
                </div>
            </div>

<?php include('includes/footer.php'); ?>
