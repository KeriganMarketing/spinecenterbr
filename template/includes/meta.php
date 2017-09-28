<?php
	//do the meta thing
	$meta = explode('[/split/]',$record['meta']);
	
	if($item == 'spine-articles'){
		echo '
		<meta name="description" property="og:description" content="'.$metaDesc.'" />
		<meta property="og:title" content="'.$headline.'" />
		<meta property="og:url" content="'.$postLink.'" />';
		if($imageUrl !=''){
			echo '<meta property="og:image" content="'.$imageUrl.'" />';
		}
	}else{
		if($meta[0] != '' ){
			$metaDesc = $meta[0];
			echo '<meta name="description" content="'.$metaDesc.'" />';
		} 
	}
	
	if($meta[1] != '' ){
		$metaKeys = $meta[1];
		echo '<meta name="keywords" content="'.$metaKeys.'"  />';
	} 
?>