<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
<meta name="robots" content="NOODP">
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-MN4PCKB');</script>
<!-- End Google Tag Manager -->

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

</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MN4PCKB"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
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
