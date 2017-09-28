<?php
require('includes/functions.php');
require('includes/db_connect.php');

$IP = $_SERVER['REMOTE_ADDR']; //get user's IP address
$time = date('Y-m-d H:i:s');
$IPQuery = "SELECT * FROM bannedIPs WHERE IP='$IP'";
$bannedCheck = mysql_query($IPQuery);
$bannedCheckArray = array();

while($bannedCheckRow = mysql_fetch_assoc($bannedCheck)) {
	array_push($bannedCheckArray, $bannedCheckRow['banned']); // push results to array
}

if(in_array('yes', $bannedCheckArray)){
	$logInAllowed = FALSE;//check user's IP against the array
}else{
	$logInAllowed = TRUE;//if it's not allow the login to be displayed
}

if($logInAllowed){
	if(isset($_POST['email'])){
		$email = clean($_POST['email']);
		$query = "SELECT * FROM userTable WHERE email = '$email'";
		$result = mysql_query($query);
		if($record = mysql_fetch_assoc($result)){
			//send recovery email
			// to
			$to  = $record['email'];
			// subject
			$subject = 'Password reset request from Kerigan Marketing Associates';
			// message
			$message = '
			<html>
				<head>
					<title>Password Reset Request</title>
				</head>
				<body>
					<p>This is an automatically generated email from kerigan.com to reset the password of the user: '. $record['userName'] .'</p>
					<p><a href="https://keriganonline.com/password.php?reset=1&user='. $record['userName'] .'&id='. $record['userID'] .'&token='. $record['GUID'] .'">click here to reset your password</a>.</p>
				</body>
			</html>
			';
			
			// To send HTML mail, the Content-type header must be set
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			
			// Additional headers
			$headers .= 'From: Kerigan Marketing Support <support@kerigan.com>' . "\r\n";
			$headers .= 'Bcc: george@kerigan.com' . "\r\n";
			
			// Mail it
			$mail = mail($to, $subject, $message, $headers);
			
			if($mail){
				$sent = TRUE;
			}else{
				$error .= 'There was an error sending your reset email. If you continue to get this message, please contact <a href="mailto:support@kerigan.com">support@kerigan.com</a> for assistance.';
			}
		}else{
			$error = '<p class="error">No account was found with that email address, please try again.</p>';
		}
	}
	if($_POST['cmd'] == 'resetAction'){
		$id = clean($_POST["id"]);
		$user = clean($_POST["user"]);
		$token = clean($_POST["token"]);
		$newPass = clean($_POST["newPass"]);
		$confirmNewPass = clean($_POST["confirmNewPass"]);
		
		$query = "SELECT * FROM userTable WHERE userName='$user' AND userID='$id' AND GUID='$token'";
		$result = mysql_query($query);
		$rows = mysql_num_rows($result);
		
		$passCheck = TRUE;
		//check that the user exists
		if($rows != '1') {$passCheck = FALSE; $error .= 'Those do not appear to be appropriate credentials.<br>';}
		if($newPass != $confirmNewPass) {$passCheck = FALSE; $error .= 'Your passwords do not match.<br>';}
		if(strlen($newPass) < 7) {$passCheck = FALSE; $error .= 'Your New Password is to short, it must be at least 6 characters.<br>';}
		
		if($passCheck){
			$sql = "UPDATE userTable SET userPass=PASSWORD('$newPass') WHERE userID='$id' AND userName='$user' AND GUID='$token'";
				
			if(!$result = mysql_query($sql)){
				$passCheck = FALSE;
				$error .= 'There was an error updating the database records'.mysql_error();
			}else{
				$resetted = TRUE;
			}
		}
		if(!$passCheck){
			$_GET['reset'] = 1;
			$_GET['token'] = $token;
			$_GET['id'] = $userID;
			$_GET['user'] = $userName;
		}
	}
	
	if($_GET['reset'] == '1' && isset($_GET['token']) && isset($_GET['id']) && isset($_GET['user'])){
	// if they are sent from the link in the email (ALL VARIABLES REQUIRED)
		$id = clean($_GET["id"]);
		$user = clean($_GET["user"]);
		$token = clean($_GET["token"]);
		
		$query = "SELECT * FROM userTable WHERE userName='$user' AND userID='$id' AND GUID='$token'";
		$result = mysql_query($query);
		$rows = mysql_num_rows($result);
		
		if($rows == '1'){
			$reset = TRUE;
		}else{
			$error = 'Invalid inputs supplied';
		}
	}
}
$pageTitle = 'Password Recovery';
include('includes/page-top.php');
?>
<div id="login-box-top-cap"></div>
<div id="login-box">
    <div class="password">
    <?php if($error != '') { echo '<p class="error">'.$error.'</p>'; } ?>
<?php
if($resetted){
?>
    <form>
	<p>'Thank you!<br />
    Your Password was successfully updated.<br />
    <a href="/">Click here log in</a>.</p>
    </form>
<?php
}
if($reset){
?>
    <form action="/password.php" method="post" enctype="multipart/form-data">
        <label>New Password<br />
        <input type="password" id="user" name="newPass" class="text" /></label>
        <label>Confirm New Password<br />
        <input type="password" id="pass" name="confirmNewPass" class="text" /></label>
        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
        <input type="hidden" name="user" value="<?php echo $user; ?>"/>
        <input type="hidden" name="token" value="<?php echo $token; ?>"/>
        <input type="hidden" name="cmd" value="resetAction"/>
        <input type="image" class="button" src="/images/button-submit.gif" />
    </form>
<?php
}
if($sent){
?>
    <form>
    <p>An email with instructions on how to reset your password has been sent. Please add "support@kerigan.com" to your address book to ensure that the message is not caught in your SPAM folder.</p>
    </form>
<?php
}
if($logInAllowed && !$reset && !$sent && !$resetted){
?>
    <form action="/password.php" method="post" enctype="multipart/form-data">
    <p>Enter the email address associated with the account in the box below and click Submit. An email with password reset instructions will be sent to you.</p>
    <label>Email Address:<br>
    <input type="text" name="email" id="user" class="text" /></label>
    <input type="image" class="button left" src="/images/button-submit.gif" />
    <a class="forgot" href="/">Return to the Main Page</a>
    </form>
<?php 
}
if(!$logInAllowed){
?>
    <form>
    <p><strong>This IP Address Has Been Banned.</strong><br>
    If you'd like to be unblocked please email <a href="mailto:support@kerigan.com">support@kerigan.com</a> with your name, phone number, and the IP address shown here: <?php echo $IP; ?></p>
    </form>
<?php
}
?>
	</div>
</div>
<div id="login-box-bot-cap"></div>
<div id="footer" class="login">
<?php
include('template/includes/footer.php');
?>
</div>