<?php 

$query = "SELECT * FROM pageTable WHERE parent='$cont' ORDER BY navOrder ASC";

if(!$result = mysql_query($query)){
	die(mysql_error());
}
$body .= '<div class="page-index">';
while($record = mysql_fetch_assoc($result)){
	
	$indexContent = $record['pageContent'];
	
		/*$body .='
		<div id="'.$record['controller'].'" class="index-result"><br />
		<div id="'.$record['controller'].'" class="index-result"><br />
		<a name="'.$record['controller'].'-link"></a>
		<h2>'.$record['title'].'</h2>
		'.$indexContent.'</div>
		<div class="clear"></div>';
		*/
		$body .='
		<div class="index-result"><br />
		<div class="index-result"><br />
		<a name="'.$record['controller'].'-link"></a>
		<h2>'.$record['title'].'</h2>
		'.$indexContent.'</div>
		<div class="clear"></div>';
		
		
}
$body .= '</div>';

?>

<?php include('support.php'); ?>