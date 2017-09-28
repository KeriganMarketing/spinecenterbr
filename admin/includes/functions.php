<?php
//get an array of sites already in the system
function get_site_array(){
	$query = "SELECT id, url FROM siteTable ORDER BY url ASC";
	if($result = mysql_query($query)){
	
		$siteArray = array();
		while($record = mysql_fetch_assoc($result)){
			$a = $record['id'];
			$b = $record['url'];
			$siteArray[$a] = $b;
		}
		return $siteArray;
	}else{
		return mysql_error();
	}
}
//get an array of sites already in the system
function get_user_array(){
	$query = "SELECT * FROM userTable";
	if($result = mysql_query($query)){
		
		$userArray = array();
		while($record = mysql_fetch_assoc($result)){
			$a = $record['userID'];
			$b = $record['userName'];
			if($record['siteID'] == '0'){ $b .= ' <span class="small">(super user)</span>'; }
			$userArray[$a] = $b;
		}
		return $userArray;
	}else{
		return mysql_error();
	}
}
/////////////////
//MISCELLENIOUS//
/////////////////

//cleanse form data for use in a query
function clean($string){
	if(is_array($string)){
		$array = array();
		foreach($string as $key=>$value){
			if(!is_numeric($key)){
				$key = mysql_real_escape_string($key);
			}
			if(!is_numeric($value)){
				$value = mysql_real_escape_string($value);
			}
			$array[$key] = $value;
		}
		$var = $array;
	}else{
		if(!is_numeric($string)){
			$string = mysql_real_escape_string($string);
		}
		$var = $string;
	}
	return $var;
}

//make a sure an item is an array
function make_array($input){
	if(is_array($input)){
		$output = $input;
	}else{
		$output = array($input);
	}
	return $output;
}

function dash_date($input){
	//input will be formatted MM/DD/YYYY
	$input = explode('-',$input);
	$y = $input[0];
	$m = $input[1];
	$d = $input[2];
	$date = $m.'/'.$d.'/'.$y;
	return $date;
}
function cal_date($input){
	//input will be formatted MM/DD/YYYY
	$input = explode('/',$input);
	$y = $input[0];
	$m = $input[1];
	$d = $input[2];
	$date = $m.'/'.$d.'/'.$y;
	return $date;
}
function server_date($input, $stripzeros = FALSE){
	//input will be formatted YYYY/MM/DD
	$input = explode('/',$input);
	$m = $input[0];
	$d = $input[1];
	$y = $input[2];
	if($stripzeros == TRUE){
		$d = ltrim($d,'0');
		$m = ltrim($m,'0');
	}
	$date = $y.'/'.$m.'/'.$d;
	return $date;
}
function crop($string,$count,$tail='...'){
	if(strlen($string)<$count){
		$string = $string;
	}else{
		$string = substr($string,0,$count).$tail;
	}
	return $string;
}
function file_upload($file,$dir){

	$target_path = $dir.basename($file['name']); 
	
	if(move_uploaded_file($file['tmp_name'], $target_path)) {
		return TRUE;
	}else{
		return FALSE;
	}
}

//export to csv
function getExcelData($data){
	$retval = "";
	if(is_array($data) && !empty($data)){
		$row = 0;
		foreach($data as $_data){
			if(is_array($_data) && !empty($_data)){
				if($row == 0){
					// write the column headers
					$retval = implode(",",array_keys(str_replace(',','',$_data)));
					$retval .= "\n";
				}
				//create a line of values for this row...
				$retval .= implode(",",array_values(str_replace(',','',$_data)));
				$retval .= "\n";
				//increment the row so we don't create headers all over again
				$row++;
			}
		}
	}
	return $retval;
}
?>