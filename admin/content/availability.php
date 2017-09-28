<script type="text/javascript">
	$(function(){
		$("#dateSelect").datepicker();
		$("#endDate").datepicker();
	});
</script>
<?php
$id = clean($_GET['id']);
$body .= '<h1>Manage Your Availability</h1>';
if($error != ''){ $body .= '<p class="error">'.$error.'</p>'; }
if($msg != ''){ $body .= '<p class="success">'.$msg.'</p>'; }
$pageFile = '/?cmd=availability';//so i can reuse this code... it's a bitch to rewrite

if(!is_numeric($id)){
	$page = clean($_GET['p']);
	if($page == ''){
		$page = 1;
	}
	$prev = $page-1;
	$next = $page+1;
	//default page, list all events
	$query = "SELECT * FROM availability";
	if(!$result = mysql_query($query)){
		die(mysql_error());
	}
	$count = mysql_num_rows($result);
	
	$show = 10;
	$pageCount = ceil($count/$show);
	
	$start = ($page-1) * $show;
	
	$query = $query." ORDER BY date DESC LIMIT $start,$show";
	if(!$result = mysql_query($query)){
		die($query.mysql_error());
	}
	
	if($pageCount > 1){
		$pagination .= '<div class="pagination">';
		if($page != 1){
			$pagination .= ' <a href="'.$pageFile.'&p='. $prev .'">prev</a> ';
		}
		$i=1;
		while($i<=$pageCount){
			if($i != $page){
				$pagination .= ' <a href="'.$pageFile.'"&p='.$i.'">';
			}
			$pagination .= $i;
			if($i != $page){
				$pagination .= '</a> ';
			}
			$i++;
		}
		if($page != $pageCount){
			$pagination .= ' <a href="'.$pageFile.'&p='. $next .'">next</a> ';
		}
		$pagination .= '</div>';
	}
	$body .= '<ul class="page-list">';
	while($record = mysql_fetch_assoc($result)){
		$body .= '
		<li><a class="mid" href="'.$pageFile.'&action=edit&id='.$record['id'].'">'.crop($record['name'],35).' <span class="small">'.cal_date($record['date']).' - '.$record['class'].'</span></a>';
	}
	$body .= '</ul>';
	
	$body = $body.$pagination;
	
	//get todays date and preload it into the date fields
	if($_POST['date'] == ''){
		$date = date("m/d/Y");
		$_POST['date'] = $date;
	}
	
	$body .= '
	<h2>Block Day(s)</h2>
	<form name="form" class="site" action="'.$pageFile.'" method="post" enctype="multipart/form-data">
		<label>Name: <span class="small">(will not show on site)</span><br />
		<input type="text" name="name" class="text" value="'.$_POST['name'].'" /></label>
		<label>Date:<br />
		<input type="text" id="dateSelect" name="date" value="'.$_POST['date'].'" style="margin: 0;" /></label>
		<label class="radio"><input type="radio" name="class" value="full"'; if($_POST['class'] == 'full' || $_POST['class'] == ''){ $body .= ' checked'; } $body .= '> All Day</label>
		<label class="radio"><input type="radio" name="class" value="morning"'; if($_POST['morning'] == 'full'){ $body .= ' checked'; } $body .= '> Morning</label>&nbsp;&nbsp;
		<label class="radio"><input type="radio" name="class" value="afternoon"'; if($_POST['afternoon'] == 'full'){ $body .= ' checked'; } $body .= '> Afternoon</label>&nbsp;&nbsp;
		<input type="hidden" name="action" value="add" />&nbsp;&nbsp;
		<input type="hidden" name="id" value="'.$_POST['id'].'" />
		<div class="clear"></div>
		<input type="submit" class="submit" value="Submit" />
	</form>';
	
}else{
	if($error == ''){
		$query = "SELECT * FROM availability WHERE id='$id'";
		if(!$result = mysql_query($query)){
			die($query.mysql_error());
		}
		$record = mysql_fetch_assoc($result);
	}else{
		$record = $_POST;
	}
	
	$body .= '
    <p class="prev"><a href="'.$pageFile.'">Return to Previous Page</a></p>
	<h2>Edit Date Block</h2>
	<form name="form" class="site" action="'.$pageFile.'" method="post" enctype="multipart/form-data">
		<label>Name:<br />
		<input type="text" name="name" class="text" value="'.$record['name'].'" /></label>
		<label>Date:<br />
		<input type="text" id="dateSelect" name="date" value="'.cal_date($record['date']).'" style="margin: 0;" /></label>
		<label class="radio"><input type="radio" name="class" value="full"'; if($record['class'] == 'full'){ $body .= ' checked'; } $body .= '>All Day</label>
		<label class="radio"><input type="radio" name="class" value="morning"'; if($record['class'] == 'morning'){ $body .= ' checked'; } $body .= '>Morning</label>
		<label class="radio"><input type="radio" name="class" value="afternoon"'; if($record['class'] == 'afternoon'){ $body .= ' checked'; } $body .= '>Afternoon</label>
		<input type="hidden" name="action" value="edit" />
		<input type="hidden" name="id" value="'.$record['id'].'" />
		<div class="clear"></div>
		<input type="submit" class="submit" value="Submit" />
	</form>
	<form name="deleteSite" id="deleteSite" action="'.$pageFile.'" onSubmit="return confirm(\'Are you sure you want to delete this item?\');" method="post">
		<input type="hidden" name="id" value="'.$record['id'].'"/>
		<input type="hidden" name="action" value="delete" />
		<input type="submit" class="delete button-fix" value="Delete" id="page-delete" />
	</form>
	';
}
if($redirect){
	header('Location: '.$pageFile);
}
echo $body;

?>
<script>
$(":date").dateinput({
	
	//turn on month/year selector
	selectors: true, 

	// this is displayed to the user
	format: 'mm/dd/yyyy',
});
	

//enable / diable a form item
function isrecurring(){
		setVisibility('recurring', 'block');
		//document.form.endDate.disabled = true;
}
//enable / diable a form item
function isntrecurring(){
		setVisibility('recurring', 'none');
		//document.form.endDate.disabled = false;
}
//enable / diable a form item
function freetoggle(){
		if(document.form.free.checked){
			document.form.price.disabled = true;
		}else{
			document.form.price.disabled = false;
		}
}
function regYes(){
		setVisibility('registration', 'block');
}
function regNo(){
		setVisibility('registration', 'none');
}
function clearQuestion(){
	if(this.value == 'Label'){
		this.value = '';
		this.style.color = '#000';
	}
}
</script>

<?php /*?>//ALTER TABLE `eventCalendar` ADD `recurring` CHAR( 3 ) NOT NULL ,
//ADD `frequency` VARCHAR( 7 ) NOT NULL ,
//ADD `price` DECIMAL( 5, 2 ) NOT NULL ,
//ADD `free` INT( 1 ) NOT NULL ,
//ADD `location` VARCHAR( 225 ) NOT NULL ,
//ADD `email` VARCHAR( 225 ) NOT NULL ,
//ADD `image` VARCHAR( 225 ) NOT NULL ,
//ADD `register` INT( 1 ) NOT NULL ,
//ADD `comments` INT( 1 ) NOT NULL ,
//ADD `paymentInstructions` TEXT NOT NULL ;
//
//CREATE TABLE `eventCalendar` (
//  `id` int(225) NOT NULL auto_increment,
//  `startDate` varchar(10) NOT NULL default '',
//  `endDate` varchar(10) NOT NULL default '',
//  `time` varchar(25) NOT NULL default '',
//  `title` varchar(225) NOT NULL default '',
//  `text` longtext NOT NULL,
//  `url` varchar(225) NOT NULL default '',
//  `file` varchar(225) NOT NULL default '',
//  `active` int(1) NOT NULL default '1',
//  `recurring` char(3) NOT NULL default '',
//  `frequency` varchar(7) NOT NULL default '',
//  `price` decimal(5,2) NOT NULL default '0.00',
//  `free` int(1) NOT NULL default '0',
//  `location` varchar(225) NOT NULL default '',
//  `email` varchar(225) NOT NULL default '',
//  `image` varchar(225) NOT NULL default '',
//  `register` int(1) NOT NULL default '0',
//  `comments` int(1) NOT NULL default '0',
//  `paymentInstructions` text NOT NULL,
//  PRIMARY KEY  (`id`)
//) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=44 ;<?php */?>