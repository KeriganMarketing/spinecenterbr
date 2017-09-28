<?php
include('content/'.$pageInc);
include('includes/page-top.php');
include('includes/user-controls.php');
?>
<!-- <?php //echo 'POST: '; print_r($_POST); echo 'COOKIES: '; print_r($_COOKIE); echo 'FILES: '; print_r($_FILES); ?> -->
<div id="page-container">
<?php include('includes/nav.php'); ?>
<div id="content">

    <?php //if($modUpdate!=''){ echo '<p style="font-size:12px;margin:-25px 0 0 0;" align="right">Module updated: '.$modUpdate.'</p>'; } ?>
    <?php if(isset($prevLink)){
		echo '<a href="'.$prevLink.'" class="prevLink">Return to the previous page</a>';
	}?>   
    
    <?php include('content/'.$pageFile); ?>
    <div class="clear"></div>
</div>
<?php
include('includes/footer.php');
?>
</div>
