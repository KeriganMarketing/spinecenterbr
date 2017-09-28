<?php
$cmd = str_replace('..','',$_GET['cmd']); //make sure to rid that pesky file jumps stuff

$file = 'popups/'.$cmd.'.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>KMA CMS Help</title>
<link href="popup.css" rel="stylesheet" type="text/css" />
</head>

<body>
<h1>Kerigan Marketing Associates CMS Help</h1>
<?php 
if(file_exists($file)){
	include($file);
	echo '<p>If the information above does not help, please contact our support at <a href="mailto:support@kerigan.com">support@kerigan.com</a> or call 850-229-4562.</p>';
}else{ 
	echo '<p>We do not currently have a help document available for this page of the CMS.</p><p>Please contact our support at <a href="mailto:support@kerigan.com">support@kerigan.com</a> or 850-229-4562 for assistance.</p>';
}
?>
</body>
</html>