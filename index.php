<?php
	require('includes/config.php'); // Contains all the site variables and php craps to make the pretty urls work.
	//if($pageType != 'search'){unset($_GET);} // unset get variables, for security reasons unless it's on one the pages that uses them. // forget that... I like gets
	include( $pageFile );
	require('includes/page-end.php'); // closes database.
?>