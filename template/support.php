<?php 
//ini_set('display_errors', 'On');

$sidebar = '';

if($item != '' ){ $parentName = $parent_title; } else { $parentName = $pageName; }

//get subpage array
$query = "SELECT controller, title FROM pageTable WHERE parent='$cont' ORDER BY navOrder ASC";
	
if(!$result = mysql_query($query)){
	die(mysql_error());
}
$index = '<div id="page-links"><p class="index-title">'.$parentName.'</p><ul class="pagelinks">';
while($record = mysql_fetch_assoc($result)){
	$index .= '<li><a class="pagelink';
	if($record['controller'] == $item){ $index .=' active'; }
	$index .='" href="/'.$cont.'/'.$record['controller'].'">'.$record['title'].'</a></li>';
}
$index .= '</ul></div>';

$sidebar .= $index;

$sidebar .= '<p id="appt-button"><a href="'.get_link('5','page').'"><img src="/images/uploads/'.get_section('12').'"></a></p>';
if($pageID != 21){
$sidebar .= '<p id="survey-button"><a href="'.get_link('21','page').'"><img alt="Survey Button" id="survey-button" src="/images/uploads/'.get_section('14').'"></a></p>';
}

include('includes/header.php'); ?>
	<div id="minibar">
        <div class="container mobile-hide">
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
                <div id="copy-block" class="col res-34 ph-1 support">
                    <h1><?php if($headline != ''){ echo $headline; } else { echo $pageTitle; } ?></h1>
                    <?php echo $pageContent; 
                            if($body != ''){
                                echo $body;
                            }
                        ?>
                </div>

            </div>

<?php include('includes/footer.php'); ?>
