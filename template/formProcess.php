<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/securimage/securimage.php'; 
$securimage = new Securimage();

$id = $_GET['id'];
$todays_date = date("Y/m/d");
$IP = $_SERVER['REMOTE_ADDR'];
$formID = $_POST['formID'];
//echo('today is '.$today);

$siteName = 'The Spine Center';
$siteURL = 'spinecenterbr.com';
if($item!=''){
	$landingPage = $cont.'/'.$item.'/';
}else{
	$landingPage = $cont.'/';
}
$highlight = array();
$req_questions = array();
$errormsg = '';
$highlight[] = '';
if($_POST && $_POST['control'] = 'formSubmit'){
	//check values
	$passCheck = TRUE;
	$query = "SELECT id, errorReporting FROM form_questions WHERE fID='$formID' AND required='1'"; // required
	$result = mysql_query($query);
	//print_r($_POST);
	if(mysql_num_rows($result) != 0){
		while($record = mysql_fetch_assoc($result)){
			$req_questions[] = $record['id'];			
		}
		
		//print_r($req_questions);
		foreach($_POST as $key => $value){
			if(is_array($value)){
				if(in_array('phonenumber', $value) && ($value[1] == '' || $value[2] == '' || $value[3] == '')){
					$passCheck = FALSE;
					$errormsg = '&#8250; Required fields were left blank. Please fill out the marked fields below.<br>';
					$highlight[] .= $key;
					
				}
			}
			if(in_array($key, $req_questions) && $value == ''){
				$passCheck = FALSE;
				$highlight[] .= $key;
			}
		}
		if($highlight != ''){
			$errormsg = '&#8250; Required fields were left blank. Please fill out the marked fields below.<br>';
		}		
	}
	
	$equery = "SELECT id FROM form_questions WHERE fID='$formID' AND errorReporting='email'"; // check email
	$eresult = mysql_query($equery);
	if(mysql_num_rows($eresult) != 0){
		$email_questions = array();
		while($erecord = mysql_fetch_assoc($eresult)){
			$email_questions[] = $erecord['id']; 
		}
		foreach($_POST as $k => $v){
			if(in_array($k, $email_questions)){
				$logEmail = $v;
				if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $v) || $v == '') { 
					$errormsg .='&#8250; You\'ve provided an email address that is not properly formatted. Addresses must be formatted as follows: "user@domain.com"<br>';
					$passCheck = FALSE;
					//echo $k.$v;
					$highlight[] .= $k;
				}else{
					$postedEmail = $v;
				}
			}
		}		
	}
	$cquery = "SELECT id FROM form_questions WHERE fID='$formID' AND errorReporting='captcha'"; // check email
	$cresult = mysql_query($cquery);
	if(mysql_num_rows($cresult) != 0){
		$captcha_qs = array();
		while($crecord = mysql_fetch_assoc($cresult)){
			if($securimage->check($_POST['captcha_code']) == false || $_POST['captcha_code'] == '') {
				$errormsg .= '&#8250; Security code was not copied correctly.<br>';
				$passCheck = FALSE;
				$highlight[] .= $l;
			}
		}		
	}
	
	$squery = "SELECT id, errorReporting FROM form_questions WHERE fID='$formID' AND errorReporting LIKE 'subscribe%'"; // check subscription
	$sresult = mysql_query($squery);
	if(mysql_num_rows($sresult) != 0){
		$subscription_qs = array();
		$codes = array();
		while($srecord = mysql_fetch_assoc($sresult)){
			$codes = explode(',',$srecord['errorReporting']);
			//print_r($codes);
			$nlCode = $codes[1];
			$pCode = $codes[2];
			$subscription_qs[] = $srecord['id'];
			//echo $nlCode.' | '.$pCode; 
		}
		//print_r($_POST);
		foreach($_POST as $s => $t){
			//print_r($subscription_qs);	
			if(in_array($s, $subscription_qs)){
				
				$que = "SELECT * FROM form_answers WHERE id=$t";
				$res = mysql_query($que);
				while($rec = mysql_fetch_assoc($res)){
					$subscribe = $rec['answer'];
				}
				//echo $que;
				
				if($subscribe == 'Yes'){
					//echo 'Yes';
					$postdata = http_build_query(array(
							'email' => $postedEmail,
							'field' => '',
							'nlbox' => $nlCode,
							'funcml' => 'add',
							'p' => $pCode,
							'_charset' => 'utf-8'
						));
					 
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
			}
		}
	} 
	
	//check email and start script to send receipt email
	if($passCheck){	
		$query = "INSERT INTO form_users SET IP='$IP', email='$logEmail', fID='$formID'";
		if(mysql_query($query)){
			$user = mysql_insert_id();
		}else{
			die('there was an error adding the user. '.mysql_error());
		}
		foreach($_POST as $key => $value){
			if(is_numeric($key)){
				if(is_array($value)){
					if(in_array('am', $value) || in_array('pm', $value)){
						  $value = $value[0].':'.$value[1].' '.$value[2]; 
						  $query = "INSERT INTO form_results SET qID='".clean($key)."', answer='".clean($value)."', uID='$user', fID='$id'";
						  if(!$result = mysql_query($query)){
								die('there was an error adding the results 1. '.mysql_error().' '.$query);
						  }
					}elseif(in_array('phonenumber', $value)){
						  $value = $value[1].'-'.$value[2].'-'.$value[3]; 
						  $query = "INSERT INTO form_results SET qID='".clean($key)."', answer='".clean($value)."', uID='$user', fID='$id'";
						  if(!$result = mysql_query($query)){
								die('there was an error adding the results 1. '.mysql_error().' '.$query);
						  }
					}else{
						foreach($value as $k => $val){
							$query = "INSERT INTO form_results SET qID='".clean($key)."', answer='".clean($val)."', uID='$user', fID='$id'";
							if(!$result = mysql_query($query)){
								die('there was an error adding the results 1. '.mysql_error().' '.$query);
							}
						}
					}
				}else{
					$query = "INSERT INTO form_results SET qID='".clean($key)."', answer='".clean($value)."', uID='$user', fID='$id'";
					if(!$result = mysql_query($query)){
						die('there was an error adding the results 2. '.mysql_error().' '.$query);
					}
				}
			}
		}
		
		$query = "SELECT * FROM forms WHERE id='$formID'";
		$result = mysql_query($query);
		$record = mysql_fetch_assoc($result);
		$success_message = $record['success_message'];
		$form_contacts = $record['form_contacts'];
		$form_name = $record['name'];
		
		//$postedArray = $_POST['6'];
		//print_r($postedArray);
		if($record['form_contacts'] != ''){
			//send email
			//assemble email

			$query = "SELECT * FROM form_questions WHERE fID='$formID' AND errorReporting != 'captcha' ORDER BY sortOrder ";
			$result = mysql_query($query);
			while($record = mysql_fetch_assoc($result)){
				$message .= '<p style="font-family:Arial, Helvetica, sans-serif; color:#333;"><strong>'.$record['question'].'</strong><br /><span style="color:#555;">';
				$que = "SELECT answer FROM form_results WHERE qID='".$record['id']."' AND uID='$user'";
				$res = mysql_query($que);
				if($record['type'] == 'text' || $record['type'] == 'textarea'){
					$rec = mysql_fetch_assoc($res);
					
					$message .= ''.$rec['answer'].'</span></p>'."\r\n";
					
				}elseif($record['type'] == 'radio' || $record['type'] == 'dropdown'){
					$rec = mysql_fetch_assoc($res);
					$q = "SELECT answer FROM form_answers WHERE id='".$rec['answer']."'";
					$r = mysql_fetch_assoc(mysql_query($q));
					$message .= ''.$r['answer'].'</span></p>'."\r\n";
				}elseif($record['type'] == 'check'){
					while($rec = mysql_fetch_assoc($res)){
						$q = "SELECT answer FROM form_answers WHERE id='".$rec['answer']."'";
						$r = mysql_fetch_assoc(mysql_query($q));
						$message .= ''.$r['answer'].'<br/>';
					}
					$message .= '</span></p>'."\r\n";
				}
			}
			
			$adminIntro = '
			<html>
			<head>
			  <title>Your Appointment</title>
			</head>
			<body>
			<br/>
			<table cellpadding="30" cellspacing="0" border="0" width="90%" align="center">
			<tr>
			<td bgcolor="#001f42" ><img src="http://spinecenterbr.com/newsletter/1114/images/logo.jpg" alt="The Spine Center at Bone and Joint Clinic" /></td>
			<td bgcolor="#001f42" ><p style="font-size:20px; font-weight:bold; font-family:Arial,sans-serif;color:#FFFFFF;margin:0 20px;">Satisfaction Survey Submission</p></td>
			</tr>
			<tr>
			<td bgcolor="#FFFFFF" colspan="2" style="border-left:#c6cabf 1px solid; border-right:#c6cabf 1px solid;">
				<p style="font-family:Verdana, Geneva, sans-serif; font-size:16px; color:#555;">There was a '.$form_name.' submission on the '.$siteURL.' website. Details are below:</p>'."\r\n";
			
			$receiptIntro = '
			<html>
			<head>
			  <title>Your Appointment</title>
			</head>
			<body>
			<br/>
			<table cellpadding="30" cellspacing="0" border="0" width="90%" align="center">
			<tr>
			<td bgcolor="#001f42" ><img src="http://spinecenterbr.com/newsletter/1114/images/logo.jpg" alt="The Spine Center at Bone and Joint Clinic" /></td>
			<td bgcolor="#001f42" ><p style="font-size:20px; font-weight:bold; font-family:Arial,sans-serif;color:#FFFFFF;margin:0 20px;">Satisfaction Survey Submission</p></td>
			</tr>
			<tr>
			<td bgcolor="#FFFFFF" colspan="2" style="border-left:#c6cabf 1px solid; border-right:#c6cabf 1px solid;">
				<p style="font-family:Verdana, Geneva, sans-serif; font-size:16px; color:#555;">Thank you for contacting us. For your records, here\'s what you submitted:</p>'."\r\n";
			$adminMessage = $adminIntro.$message.'</td>
		</tr> 
        <tr>
        <td bgcolor="#c6cabf" width="50%">
		<p style="font-family:Tahoma, Geneva, sans-serif; color:#333; font-size:13px; line-height:20px;">7301 Hennessy Blvd., Suite 200<br>Baton Rouge, LA 70808</p>
		<a href="http://spinecenterbr.com" style="font-family: Tahoma, Geneva, sans-serif; color:#248ACA; font-size:14px;">www.spinecenterbr.com</a></td><td  bgcolor="#c6cabf" align="right" width="50%"><span style="font-family: Tahoma, Geneva, sans-serif; color:#248ACA; font-size:18px;">225-766-0050</span></td>
        </tr> 
		</table>   
		</body>
		</html>
			';
			
			$receiptMessage = $receiptIntro.$message.'</td>
		</tr> 
        <tr>
        <td bgcolor="#c6cabf" width="50%">
		<p style="font-family:Tahoma, Geneva, sans-serif; color:#333; font-size:13px; line-height:20px;">7301 Hennessy Blvd., Suite 200<br>Baton Rouge, LA 70808</p>
		<a href="http://spinecenterbr.com" style="font-family: Tahoma, Geneva, sans-serif; color:#248ACA; font-size:14px;">www.spinecenterbr.com</a></td><td  bgcolor="#c6cabf" align="right" width="50%"><span style="font-family: Tahoma, Geneva, sans-serif; color:#248ACA; font-size:18px;">225-766-0050</span></td>
        </tr> 
		</table>   
		</body>
		</html>
			';
			
			// to
			$to  = $form_contacts;
			//$to  = 'bryan@kerigan.com';
			$adminTo  = 'conversion@kerigan.com';
			//$adminTo  = 'bryan@kerigan.com';
			$submitee = $postedEmail;
			
			// subject
			$subject = $siteName.' - '.$form_name.' Submission';
			$adminSubject = $siteName.' - '.$form_name.' Submission';
			
			// headers
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= 'From: '.$siteName.' <noreply@'.$siteURL.'>' . "\r\n";
			
			// set the envelope (actual sender)
			$noreply = "-fnoreply@{$_SERVER['SERVER_NAME']}";
			
			// Mail it
			if(mail($to, $subject, $adminMessage, $headers, $noreply ) && mail($submitee, $subject, $receiptMessage, $headers, $noreply )){
				if (mail($adminTo, $adminSubject, $adminMessage, $headers, $noreply )) {
					$sent = TRUE;
					//echo 'email sent';
					$pageContent = '<p class="success-message">'.$success_message.'</p>';
				}else{
					$error .= 'There was an error sending your request. If you continue to get this message, please contact <a href="mailto:support@kerigan.com">support@kerigan.com</a> for assistance.';
					echo $error;
				}
			}else{
				$error .= 'There was an error sending your submission receipt, but your request has been sent to our reservationists. Please allow up to 24 hours for confirmation of your appointment.';
				echo $error;
			}
			
			/*if($mail){
				$sent = TRUE;
			}else{
				$error .= 'There was an error sending your email. If you continue to get this message, please contact <a href="mailto:support@kerigan.com">support@kerigan.com</a> for assistance.';
			}*/
	
		}
		//echo 'success';
		
		$pageContent = '<p class="success-message">'.$success_message.'</p>';
		//$pageName = 'Thank You!';
		
		
	}else{
		//echo 'pass fail';
		//$formCode = '[form]'.$_POST['formID'].'[/form]';
		$pageContent = embed_forms($pageContent, $_POST, $_GET, $highlight, $landingPage, $errormsg);
	}
	
}else{
	//replace with cms-forms
	$pageContent = embed_forms($pageContent, $_POST, $_GET, $highlight, $landingPage, $errormsg);
}

include('support.php'); ?>