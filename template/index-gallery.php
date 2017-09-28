<?php 

$query = "SELECT * FROM pageTable WHERE parent='$cont' ORDER BY navOrder ASC";

if(!$result = mysql_query($query)){
	die(mysql_error());
}
$body .= '<div class="page-gallery">';
while($record = mysql_fetch_assoc($result)){
	
	$body .='
	<div id="'.$record['controller'].'" class="index-result col res-13 centered ph-12"><br />
		<a href="/'.$cont.'/'.$record['controller'].'/">
		<img alt="Featured Image" src="/images/uploads/'.$record['featuredImage'].'" alt="'.$record['title'].'" />
		<h3>'.$record['title'].'</h3></a>
	</div>';
}
$body .= '</div>';

?>

<?php include('support-full-width.php'); ?>