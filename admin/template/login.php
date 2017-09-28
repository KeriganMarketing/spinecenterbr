<?php
$rurl = $_POST['return'];

$IP = $_SERVER['REMOTE_ADDR']; //get user's IP address
$time = date('Y-m-d H:i:s');
$IPQuery = "SELECT * FROM bannedIPs WHERE IP='$IP'";
$bannedCheck = mysql_query($IPQuery);
$bannedCheckArray = array();

while($bannedCheckRow = mysql_fetch_assoc($bannedCheck)) {
	array_push($bannedCheckArray, $bannedCheckRow['banned']); // push results to array
}

if(in_array('yes', $bannedCheckArray)){
	$loginAllowed = FALSE;//check user's IP against the array
}else{
	$loginAllowed = TRUE;//if it's not allow the login to be displayed
}

if($loginAllowed){		
	// Get variables
	$psUser = clean($_POST['user']); //echo $psUser.'<br>';
	$psPassword = clean($_POST['pass']); //echo $psPassword.'<br>';
	
	// Check if the information has been filled in
	if($psUser != '' || $psPassword != ''){
		// Authenticate user
		$query = "SELECT userID, MD5(UNIX_TIMESTAMP() + userID + RAND(UNIX_TIMESTAMP())) GUID FROM userTable WHERE userName = '$psUser' AND userPass = password('$psPassword')";
		$result = mysql_query($query);
		$record = mysql_fetch_row($result);
		//print_r($record);
		if($record){
			// Update the user record
			$sQuery = "UPDATE userTable SET GUID='".$record[1]."' WHERE userID='".$record[0]."'";
			//echo $sQuery;
			if(!mysql_query($sQuery)){
				die(mysql_error());
			}
			if($_POST['secure'] != '1'){
				setcookie("secure","0");
			}else{
				setcookie("secure","1");
			}
			// Set the cookie and redirect
			if($_POST['remember'] != '1'){
				setcookie("session_id", $record[1], time()+240);
			}else{
				setcookie("session_id", $record[1]);
				setcookie("remember", "1");
			}
			//remove IP logging in from the bannedIPs table
			$unbanQuery = "DELETE FROM bannedIPs WHERE IP='$IP'";
			mysql_query($unbanQuery);
			
			if($rurl !=''){
				header('Location: '.$rurl);
			}else{
				header('Location: /');
			}
			
		}else{
			
			$banQuery = "INSERT INTO bannedIPs SET IP='$IP', time='$time'";
				
			if(mysql_num_rows($IPResult) > '19') { //this is their 20th attempt
					$banQuery = "UPDATE bannedIPs SET banned='yes' WHERE IP='$IP'";
					echo 'This IP has been banned';
					$loginAllowed = FALSE;
			}
			mysql_query($banQuery);
			$error = 'Login Failed';
		}
	}
	if($_GET['expired'] == 'true'){
		$error .= 'Your session timed out due to inactivity, please log back in.';
	}
	include('includes/page-top.php');
	if($loginAllowed){
	?>
    <p align="center"><img src="/images/logo.png" style="margin:20px auto 0;" /></p>
        <div id="login-box">
            <form action="/" method="post" enctype="multipart/form-data">
            <?php if($error != ''){ echo '<p class="error">'.$error.'</p>'; }?>
            <label>User Name:<br>
            <input type="text" name="user" id="user" class="text" /></label>
            <label>Password:<br>
            <input type="password" name="pass" id="pass" class="text" /></label>
            <label class="remember"><input type="checkbox" class="check" name="remember" value="1" checked/>&nbsp;<span class="remember">Remember me on this computer (<a href="/popup.php?cmd=remember" onClick="return popup(this,'help')">?</a>)</span></label>
            <label class="remember"><input type="checkbox" class="check" name="secure" value="1" checked/>&nbsp;<span class="remember">Use secure browsing (<a href="/popup.php?cmd=secure" onClick="return popup(this,'help')">?</a>) <img title="Secure Site" align="right" height="20" src="/images/icon_lock.png" alt="Secure Site" /></span></label>
            <input type="submit" class="submit" value="Sign In" style="float:left;" />
            <input type="hidden" name="return" value="<?php echo $_GET['return']; ?>" />
            <a class="forgot" href="/password.php">I forgot my user name / password</a>
            </form>
        </div>
	<?php 
	}else{
	?>
        <div id="login-box">
            <p><strong>This IP Address Has Been Banned.</strong><br>
            If you'd like to be unblocked please email <a href="mailto:support@kerigan.com">support@kerigan.com</a> with your name, phone number, and the IP address shown here: <?php echo $IP; ?></p>
        </div>
	<?php
	}
}else{
?>
    <div id="login-box">
        <p><strong>This IP Address Has Been Banned.</strong><br>
        If you'd like to be unblocked please email <a href="mailto:support@kerigan.com">support@kerigan.com</a> with your name, phone number, and the IP address shown here: <?php echo $IP; ?></p>
    </div>
<?php
}
?>
<div id="footer" class="login">
<?php
include('includes/footer.php');
?>
</div>