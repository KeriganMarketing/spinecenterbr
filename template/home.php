<?php include('includes/header.php'); ?>
	<div id="page-top">
    	<div class="container">
        	<div id="home-page-slider" class="col res-1" >
                <?php 
				$query = "SELECT * FROM home_slideshow ORDER BY sortOrder ASC";
				$result = mysql_query($query);
				$count = mysql_num_rows($result);
				$i = 1;
				while($record = mysql_fetch_assoc($result)){
					if(!empty($record['link'])){
						$slides .= '<a href="'.$record['link'].'"><img alt="Slider Image" src="/images/slider/'.$record['img'].'" title="#htmlcaption_'.$i.'" ></a>';
					}else{
						$slides .= '<img alt="Slider image" src="/images/slider/'.$record['img'].'" title="#htmlcaption_'.$i.'" >';
					};
					
					if($record['copy']!=''){
					$captions .= '
					<div id="htmlcaption_'.$i.'" class="nivo-html-caption">
						<div class="slide-copy-container id_'.$i.'">
						'.$record['copy'].'
						</div>
					</div>';
					}
				$i++;
				
				} 
				?>
				<div id="slider" class="nivoSlider">
					<?php echo $slides; ?>
				</div>
				<!--text for slider-->
				<?php echo $captions; ?>
			</div>
        </div>
    </div>
    <div id="mid" class="home">
    	<div class="container">
        	<div class="row">
                <div id="content-left" class="col res-23 tab-1 ph-1">
                    <div id="body-copy" class="col res-1 ph-1" >
                    <?php echo $pageContent; ?>
                    </div>
                    <div id="content-row2" class="col res-1 tab-1 ph-1">
                        <div id="doctor-buttons" class="col res-12 tab-1 ph-1 ph-center">
                            <a href="<?php echo get_link('24','page'); ?>">
                            <img src="/images/uploads/<?php echo get_section('1'); ?>" alt="Dr. Kevin P. McCarhty" />
                            </a>
                            <a href="<?php echo get_link('26','page'); ?>">
                            <img src="/images/uploads/<?php echo get_section('2'); ?>" alt="Dr. C. Chambliss Harrod" />
                            </a>
                        </div>
                        <div id="home-testimonial" class="col res-12 tab-1 testimonial ph-1">
                            <?php echo get_testimonial('0','random','275'); ?>
                            <a class="cta" href="<?php echo get_link('43','page'); ?>">Read More Testimonials</a>
                        </div>
                    </div>
                </div>
                <div id="content-right" class="col res-13 tab-1 ph-1">
                    <?php include('includes/appt-req.php'); ?>
                </div>
            </div>
            
<?php include('includes/footer.php'); ?>

