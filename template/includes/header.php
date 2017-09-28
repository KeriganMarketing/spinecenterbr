<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
<meta name="robots" content="NOODP">
<?php include('meta.php'); ?>
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
<link rel="icon" href="/favicon.ico" type="image/x-icon">
<link rel="apple-touch-icon-precomposed" href="/images/apple-touch-icon.png" />

<link rel="alternate" type="application/rss+xml" title="KMA Newsroom Blog RSS Feed" href="/education-resources/spine-articles/view/rss.xml/" />

<title><?php 
if($record['vanTitle']){
	if($vanTitle == ''){
	$vanTitle = strip_tags($record['vanTitle']); 
	}
	echo $vanTitle;
} else {
	echo strip_tags($siteTitle); 
}
?></title>				
    					 																	
<link rel="stylesheet" href="/js/jquery-ui/jquery-ui.min.css">
<link rel="stylesheet" href="/js/nivo-slider/nivo-slider.css" type="text/css" />
<link rel="stylesheet" href="/js/nivo-lightbox/nivo-lightbox.css" type="text/css" />
<link rel="stylesheet" href="/js/nivo-lightbox/themes/default/default.css" type="text/css" />
<link rel="stylesheet" type="text/css" href="/style.css"/>

<script type="text/javascript">
var google_replace_number = "(225)766-0050";
(function(a,e,c,f,g,b,d){var h={ak:"965927547",cl:"qItHCIWk7GEQ-8TLzAM"};a[c]=a[c]||function(){(a[c].q=a[c].q||[]).push(arguments)};a[f]||(a[f]=h.ak);b=e.createElement(g);b.async=1;b.src="//www.gstatic.com/wcm/loader.js";d=e.getElementsByTagName(g)[0];d.parentNode.insertBefore(b,d);a._googWcmGet=function(b,d,e){a[c](2,b,h,d,null,new Date,e)}})(window,document,"_googWcmImpl","_googWcmAk","script");

  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-15421411-10', 'auto');
  ga('send', 'pageview');

</script>

</head>
<body>
<div id="site-container">
	<div id="top">
        <div class="container row">
            <div id="logo" class="col res-14 tab-12 left ph-35"><a href="/"><img alt="The Spine Center at Bone & Joint Clnic of Baton Rouge" src="/images/logo.png"></a></div>
            <div id="top-contact" class="col res-23 tab-12 right ph-25">
              <div id="mini-nav" class="mobile-hide"><a href="/">Home</a> | <a href="<?php echo get_link('6','page'); ?>">Contact</a></div>
              <div id="same-day-appts"><?php echo get_section('11'); ?> <span class="bigger ph-block"><?php if($isPhone){ echo '<a href="tel:+1 '.get_section('10').'" >'.get_section('10').'</a>'; } else { echo get_section('10'); } ?></span></div></div>
            <nav id="navigation-menu" class="nav-collapse col res-34 big-down"><?php include('nav.php'); ?></nav>
        </div>
    </div>
