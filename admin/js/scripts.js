// JavaScript Document
<!--
//Create more fields (Frazee's Job Profiler has example of useage commented out)
var counter = 0;

function moreFields() {
	counter++;
	var newFields = document.getElementById('readroot').cloneNode(true);
	newFields.id = '';
	newFields.style.display = 'block';
	var newField = newFields.childNodes;
	for (var i=0;i<newField.length;i++) {
		var theName = newField[i].name
		if (theName)
			newField[i].name = theName + counter;
	}
	var insertHere = document.getElementById('writeroot');
	insertHere.parentNode.insertBefore(newFields,insertHere);
}


function showhide(divid, state){
	document.getElementById(divid).style.display=state
}
// -->

//function for js cookies
function createCookie(name,value,path,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path="+path;
}

//Alert via title change
function titleAlert(msg) {
    var oldTitle = document.title;
    var timeoutId = setInterval(function() {
        document.title = document.title == msg ? oldTitle : msg;
    }, 1000);
    window.onmousemove = function() {
        clearInterval(timeoutId);
        document.title = oldTitle;
        window.onmousemove = null;
    };
}

//change div display state
function setVisibility(id, visibility) {
	document.getElementById(id).style.display = visibility;
}
