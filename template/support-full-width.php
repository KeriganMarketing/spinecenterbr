<?php 
//ini_set('display_errors', 'On');

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
                <div id="copy-block" class="col res-1 support mobile-pad">
                    <h1><?php if($headline != ''){ echo $headline; } else { echo $pageTitle; } ?></h1>
                    <?php echo $pageContent; 
                            if($body != ''){
                                echo $body;
                            }
                        ?>
                </div>
            </div>

<?php include('includes/footer.php'); ?>
