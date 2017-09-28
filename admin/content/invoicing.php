<div id="invoicing">
    <h1>Invoicing</h1>
    <?php
	if($msg != ''){ echo '<p class="success">'.$msg.'</p>'; }
	if($error != ''){ echo '<p class="error">'.$error.'</p>'; }
    if($body == 'invoice'){
		 if($_GET['recurring'] && is_numeric($_GET['account'])){
			 //check the acocunt
			 if(array_key_exists($_GET['account'],$accounts)){
				 $recurring = TRUE;
				 $aID = $_GET['account'];
			 }
		 }
		//create an invoice
		?>
        <div id="readroot" style="display: none;"><li><input type="text" class="items" name="items[]"/> <input type="text" class="qty" name="qty[]"/> <input class="rate" name="rate[]"/></li></div>
        <form action="<?php echo $page_base; ?>" method="post" enctype="multipart/form-data" >
        	<?php if(!$recurring){ ?>
                <label>Account:</label>
                <select class="dd" name="account">
                    <?php
                    foreach($accounts as $key => $value){
                        echo "<option value=\"$key\">$value</option>\r\n";
                    }
                    ?>
                </select>
                <input type="hidden" name="action" value="invoice" />
            <?php }else{ ?>
				<p>Recurring Invoice for <?php echo $accounts[$_GET['account']]; ?></p>
                <label>Frequency:<br />
                	<select class="dd" name="frequency">
                    	<option value="monthly" <?php if($clean['frequency'] == 'monthly'){ echo 'selected'; } ?>>Monthly</option>
                    	<option value="yearly" <?php if($clean['frequency'] == 'yearly'){ echo 'selected'; } ?>>Yearly</option>
                    </select>
                </label>
                <input type="hidden" name="account" value="<?php echo $_GET['account']; ?>" />
                <input type="hidden" name="action" value="recurring" />
                
				<script type="text/javascript" src="/js/calendarDateInput.js">
                /***********************************************
                * Jason\'s Date Input Calendar- By Jason Moon http://calendar.moonscript.com/dateinput.cfm
                * Script featured on and available at http://www.dynamicdrive.com
                * Keep this notice intact for use.
                ***********************************************/
                </script>
				<label>Recurrance Date: <span class="small">(don't choose later than the 28th, you idiot)</span><br /> <script>DateInput('startDate', true, 'YYYY/MM/DD'<?php if(isset($clean['date'])){ echo ', '.$clean['date']; }?>)</script><noscript><br><input size="10" type="text" name="startDate" />(MM/DD/YYYY)<br></noscript></label>
                <label>Limit: <span class="small">(0 = unlimited)</span><br /><input type="text" class="qty" name="limit" value="<?php if(isset($clean['limit'])){ echo $clean['limit']; }else{ echo '0'; } ?>" /></label>
			<?php } ?>
        <label><div class="items">Item</div> <div class="qty">Qty.</div> <div class="rate">Rate</div></label>
		<ol id="items">
        <?php if(count($clean['items']) > 1){
			foreach($clean['items'] as $key => $value){?>
            
			<li><input type="text" class="items" name="items[]" value="<?php echo $value; ?>"/> <input type="text" class="qty" name="qty[]"  value="<?php echo $clean['qty'][$key]; ?>"/> <input class="rate" name="rate[]"  value="<?php echo $clean['qty'][$key]; ?>"/></li>
			
			<?php }
		}else{?>
			
            <li><input type="text" class="items" name="items[]" value="<?php echo $clean['items'][0]; ?>"/> <input type="text" class="qty" name="qty[]"  value="<?php echo $clean['qty'][0]; ?>"/> <input class="rate" name="rate[]"  value="<?php echo $clean['qty'][0]; ?>"/></li>
		
		<?php } ?>
		<span id="writeroot"></span>
		</ol>
		<input type="button" value="Add Fields" onclick="moreFields();"/>
        <input type="image" src="/images/button-submit.gif" class="button" />
        </form>
        <?php
    }elseif($body == 'accounts'){//user actions
		if(isset($_GET['id']) && is_numeric($_GET['id'])){
		//see a specific account
			
		}else{
		//see all accounts
			
		}
    }else{
	//default page
		$query = "SELECT * FROM invoices";
		
		//now do the pagination settings
		if(!$result = mysql_query($query)){
			die('error: '.mysql_error().$query);
		}
		$count = mysql_num_rows($result);
		
		$page = $_GET['page'];
		if(!is_numeric($page) || $page == 0 || $page == ''){
			$page ='1';
		}

		$show = 20;
		$ceil = ($count / $show);
		$pageCount = ceil($ceil);
		if($page > $pageCount){
			$page = 1;
		}
		//do math to work out how to limit the query to get the correct results
		$start = ($page - 1) * $show;
		//add limit
		$query .= " LIMIT $start, $show";
		
		//build pagination
		$paging .= '<div id="pagination">';
		if($page != 1){
			$prev = $page - 1;
			$paging .= ' <a href="'.$page_base.'&page='.$prev.'">Prev</a>';;
		}
		
		$i = 1;
		while($i <= $pageCount && $pageCount != 1){
			if($i != $page){
				$paging .= ' <a href="'.$page_base.'&page='.$i.'">'.$i.'</a>';
			}else{
				$paging .= '<span>'.$i.'</span>';
			}
			$i++;
		}
		
		if($page != $pageCount && $pageCount != 0){
			$next = $page + 1;
			$paging .= ' <a href="'.$page_base.'&page='.$next.'">Next</a>';
		}		
		$paging .= '</div>';
		
		$list = '<strong>Unpaid Invoices</strong>:<ol id="items">';
		//rerun query with limits
		if(!$result = mysql_query($query)){
			die('query failed'.mysql_error());
		}
		while($record = mysql_fetch_assoc($result)){
			$accountID = $record['account'];
			$list .= '<li class="invoice"><a href="/invoice.php?id='.$record['id'].'&c='.$record['hash'].'" target="_blank">'.$record['id'].' - '.cal_date($record['date']).' - '.$accounts[$accountID].'</a> <br />- <a href="'.$page_base.'&action=pay&invoice='.$record['id'].'">Mark Paid</a></li>';		
		}
		$list .= "</ol>";
		$list .= $paging;
		echo $list;
		echo '<br />
		<p><a href="/?cmd=invoicing&action=invoice">Create a Single Invoice</a></p><br />
		<p><a href="/?cmd=invoicing&action=accounts">Accounts</a></p>
		';
    }
    ?>
</div>