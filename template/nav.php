<ul id="nav" class="dropdown dropdown-horizontal home">
<?php

$query = "SELECT controller, title, dead FROM pageTable WHERE parent is NULL AND inNav='1' AND controller!='' ORDER BY navOrder ASC";

if (!($result = mysql_query($query))){
	print( "Could not execute nav query! <br />" );
	die( mysql_error() );
}
$i=1;
$count = mysql_num_rows($result);
/*while($topNav = mysql_fetch_assoc($result)){
	echo '<li id="'.$topNav['controller'].'" >'; 
	if($topNav['dead']!='1'){ 
		echo '<a id="'.$topNav['controller'].'" href="/'.$topNav['controller'].'/" class="topnav '; 
		if($cont == $topNav['controller']){ echo ' active'; } 
		echo '">'.$topNav['title'].'</a>';  
	} else {
		echo '<span id="'.$topNav['controller'].'" class="topnav '; 
		if($cont == $topNav['controller']){ echo ' active'; } 
		echo '">'.$topNav['title'].'</span>'; 
	}*/
	while($topNav = mysql_fetch_assoc($result)){
	echo '<li id="'.$topNav['controller'].'" >'; 
	if($topNav['dead']!='1'){ 
		echo '<a href="/'.$topNav['controller'].'/" class="topnav '; 
		if($cont == $topNav['controller']){ echo ' active'; } 
		echo '">'.$topNav['title'].'</a>';  
	} else {
		echo '<span class="topnav '; 
		if($cont == $topNav['controller']){ echo ' active'; } 
		echo '">'.$topNav['title'].'</span>'; 
	}
	
	$q = "SELECT controller, title, parent FROM pageTable WHERE parent='".$topNav['controller']."' AND inNav='1' ORDER BY navOrder ASC";
	if($res = mysql_query($q)) {
		if(mysql_num_rows($res) > 0){
			echo "\r\n".'<ul class="subnav '.$topNav['controller'].'">'."\r\n";
			while($subNav = mysql_fetch_assoc($res)){
				echo '<li class="subnav"><a href="/'.$subNav['parent'].'/'.$subNav['controller'].'/" class="subnav">'.$subNav['title'].'</a></li>'."\r\n";
				
			
			}
			echo '</ul>'."\r\n";
		}
	}
	echo '</li>'."\r\n";
	$i++;
}
?>
</ul>
