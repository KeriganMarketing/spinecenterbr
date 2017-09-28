<?php
//create array of accounts
$accountQuery = "SELECT id, name FROM accounts";
$accountResult = mysql_query($accountQuery);
$accounts = array();
while($account = mysql_fetch_assoc($accountResult)){
	$accounts[$account['id']] = $account['name'];
}
//print_r($accounts)

$page_base="/?cmd=invoicing";
$id = clean($_POST['id']);

//clean for use in a query
$clean = array();
//print_r($_POST);
foreach($_POST as $key => $value){
	if(is_array($value)){
		$clean[$key] = array();
		foreach($value as $k=>$v){
			array_push($clean[$key],clean($v));
		}
	}else{
		$clean[$key] = clean($value);
	}
}
if($clean['action'] == 'invoice'){
	$date = date("Y/m/d");
	$create = create_invoice($clean['account'],$date,$clean['items'],$clean['qty'],$clean['rate']);
	if($create == 'success'){
		$msg .= 'Invoice successfully created.';
	}else{
		$error .= $create;
	}
}
if($clean['action'] == 'recurring'){
}

if($item == 'accounts'){ $body = 'accounts'; }
if($item == 'invoice'){ $body = 'invoice'; }
if($item == 'view' && is_numeric($_GET['id'])){ $body = 'invoice'; }

if($_POST['action'] == 'invoice'){
}
?>