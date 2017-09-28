                <div class="row" id="bottom-row">
                    <div id="email-signup">
                        <div class="col res-13 mobile-fill">
                            <?php echo get_section('3'); ?>
                            <?php
                                if ( isset($_GET['lists']) && isset($_GET['codes']) ){
                                  print assemble_error_codes($_GET['lists'], $_GET['codes']);
                                } else { echo '<p style="text-align: right;">Receive our monthly eNewsletter.</p>'; }
                                
                                function assemble_error_codes($error_lists_string, $error_codes_string) {
                                    $legend = array (
                                        '0'       => "Please resubmit the subscription form.",
                                        '1'       => "This list is currently not accepting subscribers. This list has met its top number of allowed subscribers.",
                                        '2'       => "Your subscription request for this list could not be processed as you are missing required fields.",
                                        '3'       => "Thanks, but you are already on our list.",
                                        '4'       => "This e-mail address has been processed in the past to be subscribed, however your subscription was never confirmed.",
                                        '5'       => "This e-mail address cannot be added to list.",
                                        '6'       => "This e-mail address has been processed. Please check your email to confirm your subscription.",
                                        '7'       => "Thank you for subscribing!",
                                        '8'       => "E-mail address is invalid.",
                                        '9'       => "Subscription could not be processed since you did not select a list. Please select a list and try again.",
                                        '10'      => "This e-mail address has been processed. Please check your email to confirm your unsubscription.",
                                        '11'      => "This e-mail address has been unsubscribed from the list.",
                                        '12'      => "This e-mail address was not subscribed to the list.",
                                        '13'      => "Thank you for confirming your subscription.",
                                        '14'      => "Thank you for confirming your unsubscription.",
                                        '15'      => "Your changes have been saved.",
                                        '16'      => "Your subscription request for this list could not be processed as you must type your name.",
                                        '17'      => "This e-mail address is on the global exclusion list.",
                                        '18'      => "Please type the correct text that appears in the image.",
                                        '19'      => "Subscriber ID is invalid.",
                                        '20'      => "You are unable to be added to this list at this time.",
                                        '21'      => "Thanks, but you are already on our list.",
                                        '22'      => "This e-mail address could not be unsubscribed.",
                                        '23'      => "This subscriber does not exist.",
                                        '24'      => "The link to modify your account has been sent. Please check your email.",
                                        '25'      => "The image text you typed did not register. Please go back, reload the page, and try again.",
                                    );
                                
                                    $error_lists = explode(',', $error_lists_string);
                                    $error_codes = explode(',', $error_codes_string);
                                
                                    $message = "";
                                
                                    foreach ( $error_lists as $k => $listid ) {
                                        $code = ( isset($error_codes[$k]) ? (int)$error_codes[$k] : 0 );
                                        if ( isset($legend[$code]) ) {
                                            
                                            $message .= '<p style="text-align: right;"><strong>' . $legend[$code] . '</strong></p>';
                                        }
                                    }
                                
                                    return $message;
                                } ?>
                        </div>
                        <div id="newsletter-form" class="col res-13 mobile-fill">
                            <a name="subscribe"></a>
                            <form method="post" action="https://kmailer.kerigan.com/box.php">
                            <input id="emailaddress" name="email" value="" placeholder="Email Address" onClick="clearText(this);" onBlur="if(this.value==''){this.value='Email Address';}" type="text" />
                            <input type="hidden" name="field[]" />
                            <input type="hidden" name="nlbox[]" value="45" />	
                            <input type="hidden" name="funcml" value="add" />
                            <input type="hidden" name="p" value="1038" />
                            <input type="hidden" name="_charset" value="utf-8" /><input class="cta small" id="subscribe" type="submit" value="sign up" />
                            
                            </form>
                        </div>
                        <div class="col res-13 mobile-fill">
                            <img id="office-photo" src="/images/uploads/<?php echo get_section('4'); ?>" alt="office-photo" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="bot">
        <div class="container">
            <div class="row">
                <div id="col1" class="col res-14 mobile-fill">
                <?php
                
                $query = "SELECT controller, title, dead FROM pageTable WHERE parent is NULL AND inNav='1' AND (controller='spine-procedures' OR controller='education-resources') ORDER BY navOrder ASC";
                
                if (!($result = mysql_query($query))){
                    print( "Could not execute nav query! <br />" );
                    die( mysql_error() );
                }
                $i=1;
                $count = mysql_num_rows($result);
                /*while($topNav = mysql_fetch_assoc($result)){
                    echo '<ul id="footernav'.$i.'" class="footnav"><li id="'.$topNav['controller'].'" >'; 
                    if($topNav['dead']!=1){ 
                        echo '<a id="'.$topNav['controller'].'" href="/'.$topNav['controller'].'/" class="topnav '; 
                        if($cont == $topNav['controller']){ echo ' active'; } 
                        echo '">'.$topNav['title'].'</a>';  
                    } else {
                        echo '<span id="'.$topNav['controller'].'" class="topnav '; 
                        if($cont == $topNav['controller']){ echo ' active'; } 
                        echo '">'.$topNav['title'].'</span>'; 
                    }*/
                    while($topNav = mysql_fetch_assoc($result)){
                    echo '<ul class="footnav"><li>'; 
                    if($topNav['dead']!=1){ 
                        echo '<a href="/'.$topNav['controller'].'/" class="topnav '; 
                        if($cont == $topNav['controller']){ echo ' active'; } 
                        echo '">'.$topNav['title'].'</a>';  
                    } else {
                        echo '<span class="topnav '; 
                        if($cont == $topNav['controller']){ echo ' active'; } 
                        echo '">'.$topNav['title'].'</span>'; 
                    }
                    
                    $q = "SELECT controller, title, dead, parent FROM pageTable WHERE parent='".$topNav['controller']."' AND inNav='1' ORDER BY navOrder ASC";
                    if($res = mysql_query($q)) {
                        if(mysql_num_rows($res) > 0){
                            while($subNav = mysql_fetch_assoc($res)){
                                if($subNav['dead']!=1){
                                    echo '<li class="subnav"><a href="/'.$subNav['parent'].'/'.$subNav['controller'].'/" class="subnav">'.$subNav['title'].'</a></li>'."\r\n";	
                                }
                            }
                            echo '</ul>'."\r\n";
                        }
                    }
                    
                    $i++;
                }
                ?>
                </div>
                <div id="col2" class="col res-14 mobile-fill">
                <?php
                
                $query = "SELECT controller, title, dead FROM pageTable WHERE parent is NULL AND inNav='1' AND controller='our-team' ORDER BY navOrder ASC";
                
                if (!($result = mysql_query($query))){
                    print( "Could not execute nav query! <br />" );
                    die( mysql_error() );
                }
                $i=1;
                $count = mysql_num_rows($result);
                while($topNav = mysql_fetch_assoc($result)){
                    echo '<ul class="footnav"><li>'; 
                    if($topNav['dead']!=1){ 
                        echo '<a href="/'.$topNav['controller'].'/" class="topnav '; 
                        if($cont == $topNav['controller']){ echo ' active'; } 
                        echo '">'.$topNav['title'].'</a>';  
                    } else {
                        echo '<span class="topnav '; 
                        if($cont == $topNav['controller']){ echo ' active'; } 
                        echo '">'.$topNav['title'].'</span>'; 
                    }
                    

                    
                    $q = "SELECT controller, title, dead, parent FROM pageTable WHERE parent='".$topNav['controller']."' AND inNav='1' ORDER BY navOrder ASC";
                    if($res = mysql_query($q)) {
                        if(mysql_num_rows($res) > 0){
                            while($subNav = mysql_fetch_assoc($res)){
                                if($subNav['dead']!=1){
                                    echo '<li class="subnav"><a href="/'.$subNav['parent'].'/'.$subNav['controller'].'/" class="subnav">'.$subNav['title'].'</a></li>'."\r\n";	
                                }
                            }
                            echo '</ul>'."\r\n";
                        }
                    }
                    $i++;
                }
                ?>
                </div>
                <div id="col3" class="col res-14 mobile-fill">
                <?php
                
                $query = "SELECT controller, title, dead FROM pageTable WHERE parent is NULL AND inNav='1' AND (controller='referring-physicians' OR controller='patient-center') ORDER BY navOrder ASC";
                
                if (!($result = mysql_query($query))){
                    print( "Could not execute nav query! <br />" );
                    die( mysql_error() );
                }
                $i=1;
                $count = mysql_num_rows($result);
                while($topNav = mysql_fetch_assoc($result)){
                    echo '<ul class="footnav"><li>'; 
                    if($topNav['dead']!=1){ 
                        echo '<a href="/'.$topNav['controller'].'/" class="topnav '; 
                        if($cont == $topNav['controller']){ echo ' active'; } 
                        echo '">'.$topNav['title'].'</a>';  
                    } else {
                        echo '<span class="topnav '; 
                        if($cont == $topNav['controller']){ echo ' active'; } 
                        echo '">'.$topNav['title'].'</span>'; 
                    }
                    
                    $q = "SELECT controller, title, dead, parent FROM pageTable WHERE parent='".$topNav['controller']."' AND inNav='1' ORDER BY navOrder ASC";
                    if($res = mysql_query($q)) {
                        if(mysql_num_rows($res) > 0){
                            while($subNav = mysql_fetch_assoc($res)){
                                if($subNav['dead']!=1){
                                    echo '<li class="subnav"><a href="/'.$subNav['parent'].'/'.$subNav['controller'].'/" class="subnav">'.$subNav['title'].'</a></li>'."\r\n";	
                                }
                            }
                        }
                    echo '</ul>'."\r\n";
                    }
                    $i++;
                }
                ?>
                </div>
                <div id="col4" class="col res-14 mobile-fill">
                    <p class="footer-address"><?php echo get_section('5'); ?></p>
                    <p class="footer-phone"><span class="bigger"><?php if($isPhone){ echo '<a href="tel:+1 '.get_section('10').'" >'.get_section('10').'</a>'; } else { echo get_section('10'); } ?></span></p>
                    <p class="footer-email"><a href="mailto:<?php echo get_section('6'); ?>" ><?php echo get_section('6'); ?></a></p>
                    <p class="footer-directions"><a target="_blank" class="cta driving-directions" href="https://www.google.com/maps/dir//7301+Hennessy+Blvd,+Baton+Rouge,+LA+70808/@30.4225087,-91.0218501,10z/data=!4m13!1m4!3m3!1s0x8626a44180d86ca3:0x69f1d57d9c89bda1!2s7301+Hennessy+Blvd,+Baton+Rouge,+LA+70808!3b1!4m7!1m0!1m5!1m1!1s0x8626a44180d86ca3:0x69f1d57d9c89bda1!2m2!1d-91.1114681!2d30.4054379" >Driving Directions</a></p>
                    <p class="footer-fb"><a target="_blank" href="<?php echo get_section('8'); ?>" ><img src="/images/uploads/<?php echo get_section('7'); ?>" alt="Follow us on Facebook" id="fb-link" /></a></p>
                    <a href="http://www.theneuromedicalcenter.com/spine-hospital/" target="_blank"><img src="/images/thespinehospital.png" alt="The Spine Hospital" style="width:100%; max-width: 187px; margin: 20px 0;" ></a>
                </div>
            </div>
            <div class="row">
                <div class="col res-1" >
                <?php //echo get_section('9'); ?>
					<div class="clear"></div>
					<div id="copyright">
						<p>&copy;<?php echo date("Y"); ?> The Spine Center at Bone & Joint Clinic of Baton Rouge. All Rights Reserved.</p>
					</div>
					<div id="regulatory">
						<p>The Spine Center at Bone Bone & Joint Clinic of Baton Rouge complies with applicable Federal civil rights laws and does not discriminate on the basis of race, color, national origin, age, disability or sex. <a href="https://boneandjointclinicbr.com/downloads/nondiscrimination-notice_2016.pdf" target="_blank" >Click to view our notice.</a></p>
					</div>
                </div>
            </div>
			
        </div>
		
    </div>	
	
</div>
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
<script src="/js/nivo-slider/jquery.nivo.slider.pack.js" type="text/javascript"></script>
<script src="/js/nivo-lightbox/nivo-lightbox.js"></script>
<script src="/responsive-nav/responsive-nav.js"></script>

<style type="text/css">
iframe, embed, div.player { z-index:1 !important;  }
</style>
<script>
  var nav = responsiveNav(".nav-collapse");
  var nav = responsiveNav(".nav-collapse", { // Selector
  animate: true, // Boolean: Use CSS3 transitions, true or false
  transition: 150, // Integer: Speed of the transition, in milliseconds
  label: "Menu", // String: Label for the navigation toggle
  insert: "before", // String: Insert the toggle before or after the navigation
  customToggle: "pull", // Selector: Specify the ID of a custom toggle
  closeOnNavClick: false, // Boolean: Close the navigation when one of the links are clicked
  openPos: "relative", // String: Position of the opened nav, relative or static
  navClass: "nav-collapse", // String: Default CSS class. If changed, you need to edit the CSS too!
  navActiveClass: "js-nav-active", // String: Class that is added to  element when nav is active
  jsClass: "js", // String: 'JS enabled' class which is added to  element
  init: function(){}, // Function: Init callback
  open: function(){}, // Function: Open callback
  close: function(){} // Function: Close callback
});
</script>

<script type="text/javascript">
$(window).load(function() {
    $('#slider').nivoSlider({
    effect: 'fade',               // Specify sets like: 'fold,fade,sliceDown'
	slices: 15,                     // For slice animations
    animSpeed: 500,                 // Slide transition speed
    pauseTime: 4500,                // How long each slide will show
    startSlide: 0,                  // Set starting Slide (0 index)
    directionNav: false,             // Next & Prev navigation
    controlNav: false,               // 1,2,3... navigation
    controlNavThumbs: false,        // Use thumbnails for Control Nav
    manualAdvance: false,           // Force manual transitions
    prevText: '<',               // Prev directionNav text
    nextText: '>',               // Next directionNav text
    randomStart: false,             // Start on a random slide
    beforeChange: function(){},     // Triggers before a slide transition
    afterChange: function(){},         // Triggers after a slide transition
    slideshowEnd: function(){},     // Triggers after all slides have been shown
    lastSlide: function(){},         // Triggers when last slide is shown
    afterLoad: function(){}         // Triggers when slider has loaded
	});
});
</script>
<script>
$(document).ready(function(){
    $('.lightbox').nivoLightbox({
    effect: 'fade',                             // The effect to use when showing the lightbox
    theme: 'default',                           // The lightbox theme to use
    keyboardNav: true,                          // Enable/Disable keyboard navigation (left/right/escape)
    clickOverlayToClose: true,                  // If false clicking the "close" button will be the only way to close the lightbox
    onInit: function(){},                       // Callback when lightbox has loaded
    beforeShowLightbox: function(){},           // Callback before the lightbox is shown
    afterShowLightbox: function(lightbox){},    // Callback after the lightbox is shown
    beforeHideLightbox: function(){},           // Callback before the lightbox is hidden
    afterHideLightbox: function(){},            // Callback after the lightbox is hidden
    onPrev: function(element){},                // Callback when the lightbox gallery goes to previous item
    onNext: function(element){},                // Callback when the lightbox gallery goes to next item
    errorMessage: 'The requested content cannot be loaded. Please try again later.' // Error message when content can't be loaded
	});
});
</script>

<script type="text/javascript">

function clearText(content,text){
	if(content.value != ''){
		if(content.value == text){
			content.value = '';
		} 
	}
}

function replaceText(content,text){
	if(content.value == '' || content.value == text){
		content.value = text;
	} else { content.value = content.value; }
}

function toNext(content,maximum,to){
	if(content.value.length == maximum){
		document.getElementById(to).focus();
	}
}

$(function() {
	$( ".datepicker" ).datepicker();
});

$( ".datecal" ).datepicker({
      showOn: "both",
	  minDate: 0,
	  constrainInput: true,
	  beforeShowDay: false,
      buttonImage: "/images/calendar2.png",
      buttonImageOnly: false,
      buttonText: "Select date"
});

$(window).load(function() { 
    $('#slider').css({visibility: 'visible'});
});

</script>

</body>
</html>
