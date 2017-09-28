<?php
if($_POST['apptreq'] == 'SpineCenterappts!'){
	// Define variables
	$cmd = $_POST['cmd'];
	$fname = $_POST['fname'];
	$lname = $_POST['lname'];
	$email = $_POST['email'];
	$subscribe = $_POST['subscribe'];
	$phoneArea = $_POST['phoneArea'];
	$phonePre = $_POST['phonePre'];
	$phonePost = $_POST['phonePost'];
	$month = $_POST['month'];
	$day = $_POST['day'];
	$year = $_POST['year'];
	$date = $_POST['date'];
	$hour = $_POST['hour'];
	$minute = $_POST['minute'];
	$ap = $_POST['ap'];
	$physician = $_POST['physician'];

	//format arrays for error checking
	$physicians = array('Dr. Kevin P. McCarthy', 'C. Chambliss Harrod', 'first available');
	$ampm = array('am', 'pm');

	//Format variable strings to be used in email
	$name = $fname.' '.$lname;
	$phone = $phoneArea.'-'.$phonePre.'-'.$phonePost;
	$time = $hour.':'.$minute.' '.$ap;

	$success = TRUE;
	$error ='<ul id="error-list">';
	// Check USER errors
	if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email) || $email == '') { //check that email is in proper format
		$error .='<li style="color:red;">You\'ve provided an email address that is not properly formatted. Addresses must be formatted as follows: "user@domain.com"</li>';
		$eEmail = TRUE;
		$success = FALSE;
	}
	if($fname == '' || $lname == '') { //check that First and Last name only contain letters
		$error .='<li style="color:red;">You must enter your first and last name.</li>';
		if($fname == ''){
			$eFname = TRUE;
		}
		if($lname == ''){
			$eLname = TRUE;
		}
		$success = FALSE;
	}else{
		if(!ereg('[A-Za-z]', $fname) || !ereg('[A-Za-z]', $lname)) { //check that First and Last name only contain letters
			$error .='<li style="color:red;">Your name can only contain uppercase and lowercase letters.</li>';
			if(!ereg('[A-Za-z]', $fname)){
				$eFname = TRUE;
			}
			if(!ereg('[A-Za-z]', $lname)){
				$eLname = TRUE;
			}
			$success = FALSE;
		}
	}

	if($date == '') { //validate date
		$error .='<li style="color:red;">Please select a valid date.</li>';
		$eDate = TRUE;
		$success = FALSE;
	}
	if(!is_numeric($hour) || !is_numeric($minute) || !in_array($ap, $ampm)) { //validate time
		$error .='<li style="color:red;">Error, the selected time is invalid.</li>';
		$eTime = TRUE;
		$success = FALSE;
	}
	if(strlen($phoneArea)!='3' || strlen($phonePre)!='3' || strlen($phonePost)!='4' || !is_numeric($phoneArea) || !is_numeric($phonePre) || !is_numeric($phonePost)) { //validate phone number
		$error .='<li style="color:red;">The provided phone number is invalid.</li>';
		$ePhone = TRUE;
		$success = FALSE;
	}
	if($phoneArea == '' || $phonePre == '' || $phonePost == '') { //validate phone number
		$error .='<li style="color:red;">You must provide a full phone number with area code.</li>';
		$ePhone = TRUE;
		$success = FALSE;
	}
	if(!in_array($physician, $physicians)) { //validate physician
		$error .='<li style="color:red;">Please select a valid doctor.</li>';
		$eDoc = TRUE;
		$success = FALSE;
	}
	$error .='</ul>';

	if($success){
		$msg = '<p class="success"><strong>Thank you for requesting an appointment with us.</strong> A confirmation email will be sent momentarily. Please make sure "info@spinecenterbr.com" is added to your address book to prevent your spam filter from blocking the email.</p>
	<p class="success">A representative will be contacting you soon.</p>';

		// message
		$message = '
		<html>
		<head>
		  <title>Your Appointment</title>
		</head>
		<body>
		<br/>
		<table cellpadding="30" cellspacing="0" border="0" width="80%" align="center">
		<tr>
		<td bgcolor="#001f42" ><img src="http://spinecenterbr.com/newsletter/1114/images/logo.jpg" alt="The Spine Center at Bone and Joint Clinic" /></td>
		<td bgcolor="#001f42" ><p style="font-size:20px; font-weight:bold; font-family:Arial,sans-serif;color:#FFFFFF;margin:0 20px;">Your Appointment Request</p></td>
		</tr>
		<tr>
		<td bgcolor="#FFFFFF" colspan="2" style="border-left:#c6cabf 1px solid; border-right:#c6cabf 1px solid;">
		  <p style="font-family:Tahoma, Geneva, sans-serif; color:#333; font-size:15px; line-height:20px;">You have submitted an appointment <em>request</em> but an actual appointment has not been scheduled yet since our physicians may be busy with other patients at that time. However, one of our staff members will contact you within 24 hours to confirm availability or schedule the closest available time.<br /><br />

		  <strong> Desired Date:</strong> '.$date.'<br /><br />
		  <strong> Desired Time:</strong> '.$time.'<br /><br />
		  <strong> Desired Physician:</strong> '.$physician.'<br /><br />
		</td>
		</tr>
        <tr>
        <td bgcolor="#c6cabf" width="50%">
		<p style="font-family:Tahoma, Geneva, sans-serif; color:#333; font-size:13px; line-height:20px;">7301 Hennessy Blvd., Suite 300<br>Baton Rouge, LA 70808</p>
		<a href="http://spinecenterbr.com" style="font-family: Tahoma, Geneva, sans-serif; color:#248ACA; font-size:14px;">www.spinecenterbr.com</a></td><td  bgcolor="#c6cabf" align="right" width="50%"><span style="font-family: Tahoma, Geneva, sans-serif; color:#248ACA; font-size:18px;">225-766-0050</span></td>
        </tr>
		</table>
		</body>
		</html>
		';

		$adminMessage = '
		<html>
		<head>
		  <title>Appointment Request</title>
		</head>
		<body>';

		$adminMessage .= '
		  <p>An appointment has been requested from the spinecenterbr.com web site with the following information:</p>
		  <p>Name: '.$name.'</p>
		  <p>Email: '.$email.'</p>
		  <p>Phone: '.$phone.'</p>
		  <p>Desired Date: '.$date.'</p>
		  <p>Desired Time: '.$time.'</p>
		  <p>Desired Physician: '.$physician.'</p>
		</body>
		</html>
		';

		// to
		$to  = $email;
		//$adminTo = 'bryan@kerigan.com';
		$adminTo = 'info@spinecenterbr.com';
		$superAdmin = 'support@kerigan.com, jack@kerigan.com';
		//$adminBcc = 'support@kerigan.com';
		//$adminCc = '';

		// subject
		$subject = 'Your appointment with the Spine Center';
		$adminSubject = 'Appointment Request from spinecenterbr.com';

		// To send HTML mail, the Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: The Spine Center at Bone and Joint Clinic <noreply@spinecenterbr.com>' . "\r\n";

		$adminHeaders  = 'MIME-Version: 1.0' . "\r\n";
		$adminHeaders .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$adminHeaders .= 'From: The Spine Center at Bone and Joint Clinic <noreply@spinecenterbr.com>' . "\r\n";
		//$adminHeaders .= 'Bcc:'.$adminBcc. "\r\n";

		$superAdminHeaders = 'MIME-Version: 1.0' . "\r\n";
		$superAdminHeaders .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$superAdminHeaders .= 'From: The Spine Center at Bone and Joint Clinic <noreply@spinecenterbr.com>' . "\r\n";

		// set the envelope (actual sender)
		$noreply = "-fnoreply@{$_SERVER['SERVER_NAME']}";

		// Mail it
		if(mail($to, $subject, $message, $headers, $noreply )){
			mail($superAdmin, $adminSubject, $adminMessage, $superAdminHeaders, $noreply );
			mail($adminTo, $adminSubject, $adminMessage, $adminHeaders, $noreply);
		}
/*
		mail($to, $subject, $message, $headers);
		if(mail($adminTo, $adminSubject, $adminMessage, $adminHeaders)){
			mail('support@kerigan.com', $adminSubject, $adminMessage, $superAdminHeaders);
		}*/


		if($subscribe == '1'){
			$postdata = http_build_query(
				array(
					'email' => $email,
					'field' => '',
					'nlbox' => '45',
					'funcml' => 'add',
					'p' => '1038',
					'_charset' => 'utf-8'
				)
			);

			$opts = array('http' =>
				array(
					'method'  => 'POST',
					'header'  => 'Content-type: application/x-www-form-urlencoded',
					'content' => $postdata
				)
			);

			$context  = stream_context_create($opts);

			$kmail = str_replace("\r\n",'',file_get_contents('http://kmailer.kerigan.com/box.php', false, $context));
		}// END EMAIL SUBSCRIPTION


	}else{
		$msg = '<p style="color:red;">Your request was not sent. Please correct the following errors and try again.</p>';
		$msg .= $error;
	}
}else{
	$msg = '<p>By completing the form below you are not scheduling a time but rather requesting an appointment. One of our reservationists will contact you quickly to confirm the most convenient time available.</p>';
}

//Make Form
?>
<div id="appt-req" class="row">
<div class="col res-1 tab-1 mobile-fill" >
<p class="appt-req-title">Request an Appointment</p>
<?php echo $msg; ?>
</div>
<form action="" method="post" id="apptRequest" name="apptRequest" enctype="multipart/form-data">
<div class="col res-1 tab-1 ph-1" >
<p><input type="text" name="fname" class="text<?php if($eFname){ echo ' error'; } ?>" id="fname" value="<?php echo $fname; ?>" placeholder="First Name" /> <input type="text" name="lname" id="lname" class="text<?php if($eLname){ echo ' error'; } ?>" value="<?php echo $lname; ?>" placeholder="Last Name" /></p>
<p><input type="text" name="email" class="text<?php if($eEmail){ echo ' error'; } ?>" id="email" value="<?php echo $email; ?>" placeholder="Email Address" /></p>
<p>Would you like to receive our eNewsletter?
	<br><span class="option-inline"><input type="radio" id="newsletteryes" name="subscribe" checked value="1" >Yes</span><span class="radio option-inline"><input type="radio" id="newsletterno" name="subscribe" value="0" >No</span> </p>
</div>
<div class="col res-1 tab-12 ph-1" >
<p>Phone: <input type="text" name="phoneArea" class="text<?php if($ePhone){ echo ' error'; } ?>" id="phoneArea" value="<?php echo $phoneArea; ?>" maxlength="3" onkeyup="toNext(this,3,'phonePre')" />-
		  <input type="text" name="phonePre" class="text<?php if($ePhone){ echo ' error'; } ?>" id="phonePre" value="<?php echo $phonePre; ?>" maxlength="3" onkeyup="toNext(this,3,'phonePost')" />-
		  <input type="text" name="phonePost" class="text<?php if($ePhone){ echo ' error'; } ?>" id="phonePost" value="<?php echo $phonePost; ?>" maxlength="4" /></p>

<p>Desired Date: <input type="<?php if($isPhone){ echo 'date'; } else { echo 'text'; } ?>" name="date" class="calendar<?php if(!($isPhone)){ echo' datecal'; } if($eDate){ echo ' error'; } ?>" id="date" value="<?php echo $date; ?>" placeholder="MM/DD/YYYY" /></p>
<p>Desired Time: <select name="hour" id="hour">
					<option <?php if($hour =='' || $hour == '8'){ ?> selected <?php } ?> value="8">8</option>
					<option <?php if($hour == '9'){ ?> selected <?php } ?> value="9">9</option>
					<option <?php if($hour == '10'){ ?> selected <?php } ?> value="10">10</option>
					<option <?php if($hour == '11'){ ?> selected <?php } ?> value="11">11</option>
					<option <?php if($hour == '12'){ ?> selected <?php } ?> value="12">12</option>
					<option <?php if($hour == '1'){ ?> selected <?php } ?> value="1">1</option>
					<option <?php if($hour == '2'){ ?> selected <?php } ?> value="2">2</option>
					<option <?php if($hour == '3'){ ?> selected <?php } ?> value="3">3</option>
					<option <?php if($hour == '4'){ ?> selected <?php } ?> value="4">4</option>

				</select>:<select name="minute" id="minute">
					<option <?php if($minute =='' || $minute == '00'){ ?> selected <?php } ?> value="00" >00</option>
					<option <?php if($minute == '15'){ ?> selected <?php } ?> value="15">15</option>
					<option <?php if($minute == '30'){ ?> selected <?php } ?> value="30">30</option>
					<option <?php if($minute == '45'){  ?> selected <?php } ?> value="45">45</option>
				</select>
				<select name="ap" id="ap">
					<option <?php if($ap == '' || $ap == 'am'){ ?> selected <?php } ?> value="am">am</option>
					<option <?php if($ap == 'pm'){ ?> selected <?php } ?> value="pm">pm</option>
			    </select> <br>
<span class="note">Our office hours are Mon-Fri, 8am – 5pm</span></p>
</div>
<div class="col res-1 tab-12 ph-1" >
<p class="question<?php if($eDoc){ echo ' error'; } ?>">Desired Physician:
	<span class="radio option-block"><input type="radio" name="physician" <?php if($physician == 'first available') { ?> checked <?php } ?> value="first available" >First Available</span>
    <span class="option-block"><input type="radio" name="physician" <?php if($physician == 'Dr. C. Chambliss Harrod') { ?> checked <?php } ?> value="Dr. C. Chambliss Harrod" >Dr. C. Chambliss Harrod</span>
    <span class="option-block"><input type="radio" name="physician" <?php if($physician == 'Dr. Kevin P. McCarthy') { ?> checked <?php } ?> value="Dr. Kevin P. McCarthy" >Dr. Kevin P. McCarthy</span></p>
</div>
<div class="col res-1 tab-1 ph-1" >
<p class="question<?php if(!($success) && $_POST){ echo ' error'; } ?>">You must complete all fields.</p>
</div>
<input type="hidden" name="cmd" value="send" />
<input type="hidden" name="apptreq" value="SpineCenterappts!" />
<input type="submit" name="Submit" id="submit" class="cta left" value="Submit" />
</form>
</div>
