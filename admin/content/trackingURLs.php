<div id="pages">
<h1>Manage Your Trackable URLs</h1>
<?php
if($allowed){
	if($_GET['action'] == 'edit' && is_numeric($_GET['id'])){
		$body .= '<p class="prev"><a href="'.$pageBase.'">Return to Previous Page</a></p>';
		$body .= '<h3>Edit a Campaign</h3>';
		
		$id = clean($_GET['id']);
		$query = "SELECT * FROM trackingURLs WHERE id='$id' LIMIT 1";
		$result = mysql_query($query);
		$record = mysql_fetch_assoc($result);
		
		$body .= '
			<form action="'.$pageBase.'" method="post">
				<label>Name:<br />
				<input type="text" class="text" name="name" value="'.$record['name'].'"/></label>
				<label>Controller: <span class="small">I.E. '.$site.'/<strong>this</strong> - must be unique</span><br />
				<input type="text" class="text" name="key" value="'.$record['cont'].'"/></label>
				<label>Redirect to which page:<br />
				<select name="location" class="dd">
				';
				foreach($pageArray as $key => $value){
					$body .= '<option value="'.$key.'"'; if($record['location'] == $key){ $body .= ' selected'; } $body .='>'.$value.'</option>'."\r\n";
				}
				$body .= '
				</select></label>
				<input type="hidden" name="cmd" value="action" />
				<input type="hidden" name="id" value="'.$id.'" />
                <input type="submit" class="submit" value="Submit" />
			</form>
            <form name="deleteSite" id="deleteSite" action="'.$pageBase.'" onSubmit="return confirm(\'Are you sure you want to delete this campaign?\');" method="post">
                <input type="hidden" name="id" value="'.$record['id'].'"/>
                <input type="hidden" name="cmd" value="delete" />
                <input type="submit" class="delete button-fix" id="page-delete" value="Delete" />
            </form>
		';
	
	}else{
		//show campaign stats
		
		//make form for restricting results
		if($_GET['start']){
			$startDate = $_GET['start'];
		}else{
			$startDate = date("m")-1; if(strlen($startDate) == 1){ $startDate = '0'.$startDate; } $startDate .= date("/d/Y");
		}
		if($_GET['end']){
			$endDate = $_GET['end'];
		}else{
			$endDate = date("m/d/Y");
		}
		
		$body .= '<h3>Existing URLs / Stats</h3>';
		
		$body .= '<form action="'.$pageBase.'" method="get" enctype="multipart/form-data">
		<label>Show only visits between:</label>
		<label class="radio" style="width:200px;">Start Date: <input type="date" name="start" value="'.$startDate.'" /></label>
		<label class="radio" style="width:200px;">End Date: <input type="date" name="end" value="'.$endDate.'" /></label>
		<input type="hidden" name="cmd" value="trackingURLs" />
		<input class="side-button submit" type="submit" value="Submit" />
		</form>';
		
		$body .= '<ul class="page-list">';
		foreach($campaigns as $key => $value){
			$query = "SELECT ip FROM trackingURLImpressions WHERE campaign='$key'";
			if(isset($_GET['start']) && isset($_GET['end'])){
				$query .= " AND date >= '".clean(server_date($_GET['start']))."' AND date <= '".clean(server_date($_GET['end']))."'";
			}
			if(!$result = mysql_query($query)){
				die(mysql_error()."\r\n".'<br />'.$query);
			}
			$count = mysql_num_rows($result);
			$unique = array();
			while($record = mysql_fetch_assoc($result)){
				if(!in_array($record['ip'],$unique)){
					array_push($unique,$record['ip']);
				}
			}
			$uniqueCount = count($unique);
			$body .= '<li><a class="short" title="Edit this campaign" href="'.$pageBase.'&action=edit&id='.$key.'">'.crop($value,32).'</a> <span class="count">Total Visits: '.$count.'</span> <span class="count">Unique Visits:'.$uniqueCount.'</span><div class="clear"></div></li>'."\r\n";
			
		}
		if(count($campaigns) == 0){
			$body .= '<li>You Do not have any current trackable URLs.</li>';
		}
		$body .= '</ul>';
		$body .= '<hr />';
		$body .= '<h3>Add a Campaign</h3>';
		
		$id = clean($_GET['id']);
		
		$body .= '
			<form action="'.$pageBase.'" method="post">
				<label>Name:<br />
				<input type="text" class="text" name="name" value="'.$_POST['name'].'"/></label>
				<label>Controller: <span class="small">I.E. '.$site.'/<strong>this</strong> - must be unique</span><br />
				<input type="text" class="text" name="key" value="'.$_POST['key'].'"/></label>
				<label>Redirect to which page:<br />
				<select name="location" class="dd">
				';
				foreach($pageArray as $key => $value){
					$body .= '<option value="'.$key.'"'; if($_POST['location'] == $key){ $body .= ' selected'; } $body .='>'.$value.'</option>'."\r\n";
				}
				$body .= '
				</select></label>
                <input type="hidden" name="cmd" value="action" />
                <input type="submit" class="submit" value="Submit" />
			</form>
		';
	}
    if($error != ''){
        echo '<p class="error">'.$error.'</p>';
    }
    if($msg != ''){
        echo '<p class="success">'.$msg.'</p>';
    }
	echo $body;
	
}else{
	echo '<p>You have no tracking urls for your site, please <a href="/">click here</a> to continue.</p>';
}
?>
<script>
	$(":date").dateinput({
		
		//turn on month/year selector
		selectors: true, 
	
		// this is displayed to the user
		format: 'mm/dd/yyyy',
	});
</script>
</div>