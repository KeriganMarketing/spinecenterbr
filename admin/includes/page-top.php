<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; text/x-component; charset=utf-8" />
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<title><?php echo $pageTitle ?> :: Kerigan Marketing Associates | Web Site Management System</title>
<link rel="shortcut icon" href="https://keriganmarketing.com/favicon.ico" type="image/x-icon" />

<link rel="stylesheet" type="text/css" href="/css/jquery-ui-1.7.2.custom.css">
<link type="text/css" href="/css/custom-theme/jquery-ui-1.8.16.custom.css" rel="stylesheet" />	
<link rel="stylesheet" type="text/css" href="/style.css">
<script src="/js/scripts.js" type="text/javascript" language="javascript"></script>
<script src="/js/jquery.tools.min.js" type="text/javascript" language="javascript"></script>
<script src="/js/jquery-ui-1.7.2.custom.min.js" type="text/javascript" language="javascript"></script>
<script src="/js/password_strength_plugin.js" type="text/javascript" language="javascript"></script>

<script type="text/javascript" src="/js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="/js/jquery-ui-1.8.16.custom.min.js"></script>

<script type="text/javascript" src="/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="/editor/adapters/jquery.js"></script>
<script type="text/javascript">

	function count(counted,counter,maximum){					
		document.getElementById(counter).value = maximum - counted.value.length;
		if(document.getElementById(counter).value < 0){
			document.getElementById(counter).style.color = '#c20d0d';
		} else{
			document.getElementById(counter).style.color = '#1fa615';
		}
	}
	
	function showurl(title,shownurl){			
		console.log(title.value);		
		document.getElementById(shownurl).value = title.value.
		toLowerCase().
		replace(/ and /g,'-').replace(/ & /g,'-').replace(/ /g,'-').replace(/'/g,'').replace(/,/g,'').replace(/"/g,'').replace(/&/g,'-');
	}
	
	function keywordIn(checked,keywordcheck){	
		var wordArray = checked.value.split(' ');
		var keyArray = document.getElementById("metaKeys").value.split(' ');
		//var keyArray = ["design", "marketing", "creativity", "standout", "work" ];
		//var wordArray = ["design", "marketing", "creativity" ];
		//console.log(keyArray);
		//console.log(wordArray);
		var k = 0;
		for (var i = 0; i < wordArray.length; i++) {
			for (var j = 0; j < keyArray.length; j++) {
				//console.log(wordArray[i].toLowerCase()+' ~ '+keyArray[j].toLowerCase());
				if ((keyArray[j].toLowerCase() == wordArray[i].toLowerCase()) && (keyArray[j].toLowerCase()!='')) {
					k++;
				} 
				if(k > 0){
					document.getElementById(keywordcheck).value = 'Yes, '+k+' match(es)';
					document.getElementById(keywordcheck).style.color = '#1fa615;';
				} else {
					document.getElementById(keywordcheck).value = 'no';
				}
				//console.log(k);
			}
		}
	}

	var swapsectiontype = function(){};	
	function swapsectiontype(){
		if(document.getElementById("swap").value == 'text'){
			document.getElementById("text").style.display = 'block';
			//alert("yay!");
		};
		if(document.getElementById("swap").value == 'htmltext'){
			document.getElementById("htmltext").style.display = 'block';
		};
		if(document.getElementById("swap").value == 'image'){
			document.getElementById("image").style.display = 'block';
		};
		if(document.getElementById("swap").value == 'link'){
			document.getElementById("link").style.display = 'block';
		};
		
	}
	
	function runScripts(){ 
		swapsectiontype();
	}
	
</script>     
</head>

<body onLoad="runScripts">

    <?php
    if($msg != ''){ 
		echo '<div id="msg"><img class="icon_close" src="/images/icon_close.png" onClick="setVisibility(\'msg\', \'none\');" />'.$msg.'</div>';
		unset($msg);
	} 
	if($error != ''){
		echo '<div id="error"><img class="icon_close" src="/images/icon_close.png" onClick="setVisibility(\'error\', \'none\');" />'.$error.'</div>';
		unset($error);
	}
	?>
    
<div id="loading">