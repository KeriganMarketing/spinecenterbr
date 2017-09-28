<?php
$galleryBase = '/education-resources/videos-and-interviews/';

//Video Gallery
$videoGallery .= '<div id="video-gallery">';


$query1 = "SELECT * FROM videoCategories ORDER BY sortOrder";
$result1 = mysql_query($query1);
while($record1 = mysql_fetch_assoc($result1)){
	
	
	$videoGallery .='<h2>Meet Our Doctors</h2>';
	$query = "SELECT * FROM videos WHERE category=4 ORDER BY navOrder";
	$result = mysql_query($query);

	$i = 1;
	
	$videoGallery .= '<div class="row">';
	while($record = mysql_fetch_assoc($result)){
		
		if(substr($record['url'], 0, 2) == 'A_' ){
			$videoGallery .= '
				<div class="col res-12 tab-12 ph-1 video">
					<a data-lightbox-gallery="gallery" data-lightbox-type="inline" href="#'.$record['url'].'" class="lightbox" title="'.$record['title'].'">
					<div id="'.$record['url'].'" style="display:none;"></div>
					<p>'.$record['title'].'</p>
					<div class="image-crop" style="background:url(\'/images/viewmedica/'.$record['url'].'.jpg\') center no-repeat; "><div class="play-button"></div></div></a>
					<script type="text/javascript" src="https://www.swarminteractive.com/js/vm.js"></script>
		<script type="text/javascript">client="6053"; openthis="'.$record['url'].'"; vm_open();</script>
				</div>
				
		';
				$i++;
		
		}else{
			$videoGallery .= '
				<div class="col res-12 tab-12 ph-1 video">
					<a data-lightbox-gallery="gallery" href="https://www.youtube.com/watch?v='.$record['url'].'?autoplay=1" class="lightbox" title="'.$record['title'].'">
					<p>'.$record['title'].'</p>
					<div class="image-crop" style="background:url(\'http://i.ytimg.com/vi/'.$record['url'].'/0.jpg\') center no-repeat; "><div class="play-button"></div></div></a>
				</div> 
				';
				$i++;
			
		}/*end if*/
	
	}/*end while*/
	
	$videoGallery .= '
	<div class="clear"></div>
	<br><br>
	</div>
	';


    $videoGallery .='<h2>Non-Surgical Procedures</h2>';
	$query = "SELECT * FROM videos WHERE category=2 ORDER BY navOrder";
	$result = mysql_query($query);

	$i = 1;
	
	$videoGallery .= '<div class="row">';
	while($record = mysql_fetch_assoc($result)){
		
		if(substr($record['url'], 0, 2) == 'A_' ){
			$videoGallery .= '
				<div class="col res-12 tab-12 ph-1 video">
					<a data-lightbox-gallery="gallery" data-lightbox-type="inline" href="#'.$record['url'].'" class="lightbox" title="'.$record['title'].'">
					<div id="'.$record['url'].'" style="display:none;"></div>
					<p>'.$record['title'].'</p>
					<div class="image-crop" style="background:url(\'/images/viewmedica/'.$record['url'].'.jpg\') center no-repeat; "><div class="play-button"></div></div></a>
					<script type="text/javascript" src="https://www.swarminteractive.com/js/vm.js"></script>
		<script type="text/javascript">client="6053"; openthis="'.$record['url'].'"; vm_open();</script>
				</div>
				
		';
				$i++;
		
		}else{
			$videoGallery .= '
				<div class="col res-12 tab-12 ph-1 video">
					<a data-lightbox-gallery="gallery" href="https://www.youtube.com/watch?v='.$record['url'].'?autoplay=1" class="lightbox" title="'.$record['title'].'">
					<p>'.$record['title'].'</p>
					<div class="image-crop" style="background:url(\'http://i.ytimg.com/vi/'.$record['url'].'/0.jpg\') center no-repeat; "><div class="play-button"></div></div></a>
				</div> 
				';
				$i++;
			
		}/*end if*/
		
	}/*end while*/
	
	$videoGallery .= '
	<div class="clear"></div>
	<br><br>
	</div>
	';

	$videoGallery .='<h2>Surgical Procedures</h2>';
	$query = "SELECT * FROM videos WHERE category=3 ORDER BY navOrder";
	$result = mysql_query($query);

	$i = 1;
	
	$videoGallery .= '<div class="row">';
	while($record = mysql_fetch_assoc($result)){
		
		if(substr($record['url'], 0, 2) == 'A_' ){
			$videoGallery .= '
				<div class="col res-12 tab-12 ph-1 video">
					<a data-lightbox-gallery="gallery" data-lightbox-type="inline" href="#'.$record['url'].'" class="lightbox" title="'.$record['title'].'">
					<div id="'.$record['url'].'" style="display:none;"></div>
					<p>'.$record['title'].'</p>
					<div class="image-crop" style="background:url(\'/images/viewmedica/'.$record['url'].'.jpg\') center no-repeat; "><div class="play-button"></div></div></a>
					<script type="text/javascript" src="https://www.swarminteractive.com/js/vm.js"></script>
		<script type="text/javascript">client="6053"; openthis="'.$record['url'].'"; vm_open();</script>
				</div>
				
		';
				$i++;
		
		}else{
			$videoGallery .= '
				<div class="col res-12 tab-12 ph-1 video">
					<a data-lightbox-gallery="gallery" href="https://www.youtube.com/watch?v='.$record['url'].'?autoplay=1" class="lightbox" title="'.$record['title'].'">
					<p>'.$record['title'].'</p>
					<div class="image-crop" style="background:url(\'http://i.ytimg.com/vi/'.$record['url'].'/0.jpg\') center no-repeat; "><div class="play-button"></div></div></a>
				</div> 
				';
				$i++;
			
		}/*end if*/
		
	}/*end while*/
	break;
	$videoGallery .= '
	<div class="clear"></div>
	<br><br>
	</div>
	';


}/*end while*/

$videoGallery .= '
</div>

';




$body = $videoGallery;
include('support.php');
?>