<?php 
ini_set('display_errors', 'Off');

$body = '';

if($pageID == 5){
	$body = '
	<div id="patient-center-buttons" class="row">
		<div class="col res-12 ph-1 button">
			<img id="survey-button" src="/images/uploads/'.get_section('15').'">
			<a class="cta box" href="'.get_link('17','page').'">Learn More</a>
		</div>
		<div class="col res-12 ph-1 button">
			<img id="survey-button" src="/images/uploads/'.get_section('16').'">
			<a class="cta box" href="'.get_link('21','page').'">Start Here</a>
		</div>
		<div id="testimonial" class="col res-12 ph-1 button">
			<div class="boxed-button"><div class="box-content testimonial">
			'.get_testimonial('0','random','115').'
			</div><a class="cta box" href="'.get_link('43','page').'">Read More Testimonials</a></div>
		</div>
		<div id="health-plans" class="col res-12 ph-1 button">
			<div class="boxed-button"><div class="box-content">
			'.get_section('17').'
			</div><a class="cta box" href="'.get_link('19','page').'">View List</a></div>
		</div>
		<div id="forms" class="col res-12 ph-1 button">
			<div class="boxed-button"><div class="box-content">
			'.get_section('18').'
			</div><a class="cta box" href="'.get_link('20','page').'">View All Forms</a></div>
		</div>
		<div id="articles" class="col res-12 ph-1 button">
			<div class="boxed-button"><div class="box-content">
			'.get_section('19').'
            </div><a class="cta box" href="'.get_link('37','page').'">Learn More</a></div>
		</div>
	</div>
	';
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
                <div id="copy-block" class="col res-23 support">
                    <h1><?php if($headline != ''){ echo $headline; } else { echo $pageTitle; } ?></h1>
                    <?php echo $pageContent; 
                            if($body != ''){
                                echo $body;
                            }
                        ?>
                </div>
                <div id="sidebar" class="col res-13 tab-1 ph-1">
                    <?php include('includes/appt-req.php'); ?>
                </div>
            </div>

<?php include('includes/footer.php'); ?>
