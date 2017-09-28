<?php
$id = clean($_POST['id']);

if($item == 'sites'){ $body = 'sites'; }
if($item == 'users'){ $body = 'users'; }

if($_POST['action'] == 'site'){
	if($_POST['cmd'] == 'delete'){
		$query = "DELETE FROM siteTable WHERE id='$id'";
		if(mysql_query($query)){
				$success = TRUE;
				$msg = 'The Information has been deleted successfully.';
				unset($_POST);
		}else{
			$error .= 'There was a problem processing your request. Please try again.<br />'."\r\n".mysql_error();
		}
	}else{
		//get current site data
		if($id !=''){
			$query = "SELECT * FROM siteTable WHERE id='$id'";
			if($result = mysql_query($query)){
				$current = mysql_fetch_assoc($result);
			}
		}
		
		//place all page types values into string separated by commas
		$count = count($_POST['pageTypes']);
		$i = 1;
		$pageTypeVals = array();
		foreach($_POST['pageTypes'] as $value){
			array_push($pageTypeVals,clean($value));
		}
		foreach($_POST['specialPageTypes'] as $value){
			array_push($pageTypeVals,clean($value));
		}
		foreach(explode(',',$_POST['addtlPageTypes']) as $value){
			if($value != ''){
				array_push($pageTypeVals,clean($value));
			}
		}
		//print_r($pageTypeVals);
		$count = count($pageTypeVals);
		$i=1;
		$pageTypesVal = '';
		foreach($pageTypeVals as $value){
			$pageTypesVal .= $value;
			if($i > 0 && $i < $count+1) { $pageTypesVal .= ','; }
			$i++;
		}
		print($pageTypeVal);
		$url = clean($_POST['url']);
		$dbName = clean($_POST['dbName']);
		$dbUser = clean($_POST['dbUser']);
		$dbPass = clean($_POST['dbPass']);
		$root = clean($_POST['root']);
		if($id != '' && is_numeric($id)){
			if($current['url'] != $_POST['url']){
				$replaceURL = TRUE;
			}
		}
		$hasUsers = clean($_POST['hasUsers']);
		$hasPagesVal = clean($_POST['hasPages']);
		
		//don't pass checks your ass can't cash
		$passCheck = TRUE;
		if($url == ''){ $passCheck = FALSE; $error .= 'The URL cannot be left blank<br />'."\r\n"; }
		if($root == ''){ $passCheck = FALSE; $error .= 'The root cannot be left blank<br />'."\r\n"; }
		if($hasUsers == '' || !is_numeric($hasUsers)){ $passCheck = FALSE; $error .= 'The hasUsers value cannot be left blank and must be a number<br />'."\r\n"; }
		if($hasPagesVal == '' || !is_numeric($hasPagesVal)){ $passCheck = FALSE; $error .= 'The hasPages value cannot be left blank and must be a number<br />'."\r\n"; }
		
		if($passCheck){
			$vals = "url='$url', dbName='$dbName', dbUser='$dbUser', dbPass='$dbPass', pageTypes='$pageTypesVal', hasUsers='$hasUsers', hasPages='$hasPagesVal', root='$root'";
			//form query
			if($id != '' && is_numeric($id)){
				$query = "UPDATE siteTable SET ".$vals." WHERE id='$id'";
			}else{
				$query = "INSERT INTO siteTable SET ".$vals."";
			}
			//echo $query;
			if(mysql_query($query)){
				if($replaceURL == TRUE){
					
					//connect to site's specific database
					$db = $current['dbName'];
					$user = $current['dbUser'];
					$pass = $current['dbPass'];
					
					mysql_close($database);
					$database = mysql_connect($host,$user,$pass);
					if(!mysql_select_db($db,$database)){
						die('could not connect to db'.mysql_error());
					}
					
					//create loop to find and fix absolute urls
					$query = "SELECT pageID,pageContent FROM pageTable";
					if(!$result = mysql_query($query)){
						echo 'error:'.mysql_error();
					}
					
					while($record = mysql_fetch_assoc($result)){
						$pageID = $record['pageID'];
						$newContent = clean(str_replace($current['url'],$url,$record['pageContent']));
						$q = "UPDATE pageTable SET pageContent='$newContent' WHERE pageID='$pageID'";
						if(!mysql_query($q)){
							die('there was an error with the query'.mysql_error().'<br /><br />'.$q);
						}else{
							$msg = 'updated<br />';
						}
					}
					
					//reconnect to admin db
					mysql_close($database);
					include('../includes/db_connect.php');
					
				}
				$success = TRUE;
				$msg = 'The Information has been submitted successfully.';
				unset($_POST);
			}else{
				$error .= 'There was a problem processing your request. Please try again.<br />'."\r\n".mysql_error().'<br />'.$query;
			}
		}
	}
}
if($_POST['action'] == 'user'){
	if($_POST['cmd'] == 'delete'){
		$query = "DELETE FROM userTable WHERE userID='$id'";
		if(mysql_query($query)){
				$success = TRUE;
				$msg = 'The Information has been deleted successfully.';
				unset($_POST);
		}else{
			$error .= 'There was a problem processing your request. Please try again.<br />'."\r\n".mysql_error();
		}
	}else{
		$user = clean($_POST['user']);
		$pass = clean($_POST['pass']);
		$email = clean($_POST['email']);
		$fName = clean($_POST['fName']);
		$lName = clean($_POST['lName']);
		$site = clean($_POST['site']);
		$comments = clean($_POST['comments']);
		
		//don't pass checks your ass can't cash
		$passCheck = TRUE;
		if($user == ''){ $passCheck = FALSE; $error .= 'The User Name cannot be left blank<br />'."\r\n"; }
		if($email == ''){ $passCheck = FALSE; $error .= 'The Email cannot be left blank<br />'."\r\n"; }
		if($pass == '' && $id == ''){ $passCheck = FALSE; $error .= 'The Password cannot be left blank<br />'."\r\n"; }
		if($pass != '' && strlen($pass) < 7){ $passCheck = FALSE; $error .= 'The Password cannot be less than 6 characters<br />'."\r\n"; }
		if($site == '' || !is_numeric($site)){ $passCheck = FALSE; $error .= 'The Site value cannot be left blank and must be a number<br />'."\r\n"; }
		
		if($passCheck){
			$vals = "userName='$user', email='$email', siteID='$site', sData='$comments', fName='$fName', lName='$lName'";
			if($pass != ''){ $vals .= ", userPass=PASSWORD('$pass')";}
			//form query
			if($id != '' && is_numeric($id)){
				$query = "UPDATE userTable SET ".$vals." WHERE userID='$id'";
			}else{
				$query = "INSERT INTO userTable SET ".$vals;
			}
			if(mysql_query($query)){
				$success = TRUE;
				$msg = 'The Information has been submitted successfully.';
				unset($_POST);
			}else{
				$error .= 'There was a problem processing your request. Please try again.<br />'."\r\n".mysql_error().'<br />'.$query;
			}
		}
	}
}
?>