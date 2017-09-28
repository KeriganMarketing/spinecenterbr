<?php
//TESTIMONIALS PAGE By George Kelley (george@kerigan.com) FOR keriganonline.com
$tquery = "SELECT * FROM testimonials WHERE display='1' ORDER BY sortOrder ASC";
$tresult = mysql_query($tquery);

$body = '';

while($trecord = mysql_fetch_assoc($tresult)){
	$body .= '

		<div class="testimonial full" >
			<p class="quote full" ><span class="openquote">&#8220;</span>'.$trecord['content'].'&#8221;</p>
			<p class="author">&#8211;'.$trecord['author'].'<br><br></p>
		</div>

	';
}


include('support.php'); ?>