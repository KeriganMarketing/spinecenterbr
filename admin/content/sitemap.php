<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php 
	$query = "SELECT controller FROM pageTable WHERE parent is NULL AND inNav='1' ORDER BY `navOrder` ASC";
	
	if(!$result = mysql_query($query)){
		print("Could not execute nav query! <br />");
		die(mysql_error());
	}
	while($topNav = mysql_fetch_assoc($result)){
	
	echo'
	<url>
      <loc>'.$site_url.$topNav['controller'].'</loc>
	</url>';
	$subQuery = "SELECT controller FROM pageTable WHERE parent='".$topNav['controller']."' AND inNav='1' ORDER BY `navOrder` ASC";
		if($subResult = mysql_query($subQuery)){
			while($subNav = mysql_fetch_assoc($subResult)){	echo'
			<url>
			  <loc>'.$site_url.$subNav['controller'].'</loc>
			</url>';
			}
		}
	}
?>
</urlset> 