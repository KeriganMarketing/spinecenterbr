<div class="clear"></div>


<SCRIPT TYPE="text/javascript">
<!--
function popup(mylink, windowname)
{
if (! window.focus)return true;
var href;
if (typeof(mylink) == 'string')
   href=mylink;
else
   href=mylink.href;
window.open(href, windowname, 'width=800,height=600,scrollbars=yes');
return false;
}
//-->
</SCRIPT>
<?php if ($_COOKIE['remember'] != '1' && $templateFile != 'login.php'){ ?>
  
<script type="text/javascript" language="javascript">
	//event to check session time variable declaration
	var checkSessionTimeEvent;
	
	$(document).ready(function() {
		//event to check session time left (times 1000 to convert seconds to milliseconds)
		checkSessionTimeEvent = setInterval("checkSessionTime()",10*1000);
	});
	//Your timing variables in number of seconds
	
	//total length of session in seconds
	var sessionLength = 1200; 
	//time warning shown (10 = warning box shown 10 seconds before session starts)
	var warning = 20;  
	//time redirect forced (10 = redirect forced 10 seconds after session ends)     
	var forceRedirect = 5; 
	
	//time session started
	var pageRequestTime = new Date();
	
	//session timeout length
	var timeoutLength = sessionLength*1000;
	
	//set time for first warning, ten seconds before session expires
	var warningTime = timeoutLength - (warning*1000);
	
	//force redirect to log in page length (session timeout plus 10 seconds)
	var forceRedirectLength = timeoutLength + (forceRedirect*1000);
	
	//set number of seconds to count down from for countdown ticker
	var countdownTime = warning;
	
	//warning dialog open; countdown underway
	var warningStarted = false;
	
	function checkSessionTime()
	{
		//get time now
		var timeNow = new Date(); 
		
		//event create countdown ticker variable declaration
		var countdownTickerEvent; 	
		
		//difference between time now and time session started variable declartion
		var timeDifference = 0;
		
		timeDifference = timeNow - pageRequestTime;
	
		if (timeDifference > warningTime && warningStarted === false)
			{            
				//call now for initial dialog box text (time left until session timeout)
				countdownTicker(); 
				
				//set as interval event to countdown seconds to session timeout
				countdownTickerEvent = setInterval("countdownTicker()", 1000);
				
				$('#dialogWarning').dialog('open');
				warningStarted = true;
				titleAlert('Timeout Alert!');

			}
		else if (timeDifference > timeoutLength){
				//close warning dialog box
				if ($('#dialogWarning').dialog('isOpen')) $('#dialogWarning').dialog('close');
				
				//$("p#dialogText-expired").html(timeDifference);
				$('#dialogExpired').dialog('open');
				
				 //clear (stop) countdown ticker
				clearInterval(countdownTickerEvent);
			}
			
		if (timeDifference > forceRedirectLength)
			{    
				//clear (stop) checksession event
				clearInterval(checkSessionTimeEvent);
				//force relocation
				window.location="/?expired=true&return=<?php echo $_SERVER['REQUEST_URI']; ?>";
			}
	
	}
	
	function countdownTicker()
	{
		//put countdown time left in dialog box
		$("span#dialogText-warning").html(countdownTime);
		
		//decrement countdownTime
		countdownTime--;
	}
	
	$(function(){              
		// jQuery UI Dialog    
		$('#dialogWarning').dialog({
			autoOpen: false,
			width: 400,
			modal: true,
			resizable: false,
			buttons: {
				"Extend Your Session": function() {
					clearInterval(checkSessionTimeEvent);
					$('#dialogWarning').dialog('close');
					createCookie('session_id','<?php echo $_COOKIE['session_id']; ?>','/');
					//location.reload();
				}
			}
		});
		
		$('#dialogExpired').dialog({
			autoOpen: false,
			width: 400,
			modal: true,
			resizable: false,
			close: function() {
					window.location="/?expired=true&return=<?php echo $_SERVER['REQUEST_URI']; ?>";
				},
			buttons: {
				"Login": function() {
					window.location="/?expired=true&return=<?php echo $_SERVER['REQUEST_URI']; ?>";
				}
			}
		});
	});
</script>
    

<!--Dialog box contents-->
<div id="dialogExpired" title="Session Expired!"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 0 0;"></span> Your session has expired!<p id="dialogText-expired"></p></div>

<div id="dialogWarning" title="Your session is about to expire!"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 0 0;"></span> Your session will expire in <span id="dialogText-warning"></span> seconds!</div>
<?php } ?>