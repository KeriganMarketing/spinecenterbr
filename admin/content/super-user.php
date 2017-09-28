 <div id="super-user">
    <h1>Super User Menu</h1>
    <?php
	//$siteArray = get_site_array(); // see config.php file
	//print_r($siteArray);
	$siteMenu = '<select name="site" class="dd">'."\r\n";
	$siteMenu .= '<option value="0" ';
		if('0' == $_POST['site']){
			$siteMenu .= 'selected ';
		}
	$siteMenu .= '>Super User</option>'."\r\n";
	foreach($siteArray as $key => $value){
		$siteMenu .= '<option value="'.$key.'" ';
		if($key == $_POST['site']){
			$siteMenu .= 'selected ';
		}
		$siteMenu .= '>'.$value.'</option>'."\r\n";
	}
	$siteMenu .= '<option value="9999" ';
		if('9999' == $_POST['site']){
			$siteMenu .= 'selected ';
		}
	$siteMenu .= '>No Site in system</option>'."\r\n";
	$siteMenu .= '</select>'."\r\n";
	
	if($error != ''){
		echo '<p class="error">'.$error.'</p>';
	}
	if($msg != ''){
		echo '<p class="success">'.$msg.'</p>';
	}
    if($body == 'sites'){//site actions
		if(isset($_GET['id']) && is_numeric($_GET['id'])){
			$query = "SELECT * FROM siteTable WHERE id='".clean($_GET['id'])."' LIMIT 1";
			if(!$result = mysql_query($query)){
				die(mysql_error().' try refreshing');
			}
			$record = mysql_fetch_assoc($result);
			?>
			<p class="prev"><a href="/?cmd=super-user&action=sites">Return to Previous Page</a></p>
			<h2>Edit a Site</h2>
			<form class="site" action="/?cmd=super-user&action=sites&id=<?php echo $record['id']; ?>" method="post" enctype="multipart/form-data">
				<label>URL: <span class="small">(causes search and replace in the pageContent cell. If you do not want this, edit the db manually)</span><br />
				<input type="text" name="url" class="text" value="<?php echo $record['url']; ?>" /></label>
				<label>Database:<br />
				<input type="text" name="dbName" class="text" value="<?php echo $record['dbName']; ?>" /></label>
				<label>Database User:<br />
				<input type="text" name="dbUser" class="text" value="<?php echo $record['dbUser']; ?>" /></label>
				<label>Database Password:<br />
				<input type="text" name="dbPass" class="text" value="<?php echo $record['dbPass']; ?>" /></label>
				<label>Root:<br />
				<input type="text" name="root" class="text" value="<?php echo $record['root']; ?>" /></label>
                <label>Page Types<br />
                <select multiple="multiple" name="pageTypes[]" class="multiple">
                <?php
                foreach($pages as $key => $value){
					if(!in_array($value,$globalPages) && $value != 'pages'){
                   		echo '<option'; if(in_array($value,explode(',',$record['pageTypes']))){ echo ' selected'; } echo ' value="'.$value.'">'.$value.'</option>';
					}
                }
                ?>
                </select></label>
                <label>Spaecial Pages<br />
                <select multiple="multiple" name="specialPageTypes[]" class="multiple">
                <?php
                foreach($specialPages as $key => $value){
					echo '<option'; if(in_array($value,explode(',',$record['pageTypes']))){ echo ' selected'; } echo ' value="'.$value.'">'.$value.'</option>';
                }
                ?>
                </select></label>
                <label>Additional Page Types:<br />
				<textarea name="addtlPageTypes" class="textarea"><?php
				$i=1;
				foreach(explode(',',$record['pageTypes']) as $key => $value){
					if(!in_array($value,$pages) && !in_array($value,$specialPages)){ 
						echo $value; if($i>0){ echo ','; } $i++;
					} 
				} ?></textarea></label>
				<label style=" display:inline-block; width: 400px;">Has Log In System?</label> <label class="radio">yes <input type="radio" name="hasUsers" <?php if($record['hasUsers'] == '1'){ echo 'checked ';} ?>value="1" /></label> <label class="radio">no <input type="radio" name="hasUsers" <?php if($record['hasUsers'] != '1'){ echo 'checked ';} ?>value="0" /></label>
				<label style=" display:inline-block; width: 400px;">Has CMS Pages?</label> <label class="radio">yes <input type="radio" name="hasPages" <?php if($record['hasPages'] == '1'){ echo 'checked ';} ?>value="1" /></label> <label class="radio">no <input type="radio" name="hasPages" <?php if($record['hasPages'] != '1'){ echo 'checked ';} ?>value="0" /></label>
                <div clas="clear"></div>
				<input type="hidden" name="action" value="site" />
				<input type="hidden" name="id" value="<?php echo $record['id']; ?>" />
				<input type="submit" class="submit" value="Submit"/>
			</form>
            <form name="deleteSite" id="deleteSite" action="/?cmd=super-user&action=sites" onSubmit="return confirm('Are you sure you want to delete this site?');" method="post">
                <input type="hidden" name="id" value="<?php echo $record['id']; ?>"/>
				<input type="hidden" name="action" value="site" />
                <input type="hidden" name="cmd" value="delete" />
                <input type="submit" class="delete button-fix" value="Delete" />
            </form>
			<?php
		}else{
			?>
			<p class="prev"><a href="/?cmd=super-user">Return to Previous Page</a></p>
            <h2>Sites:</h2>
            <ul class="page-list">
            	<?php
				foreach($siteArray as $key => $value){
            		$siteList .= '<li><a href="/?cmd=super-user&action=sites&id='.$key.'">'.$value.'</a></li>';
				}
				echo $siteList;
				?>
            </ul>
            <?php
		}
    }elseif($body == 'users'){//user actions
		if(isset($_GET['id']) && is_numeric($_GET['id'])){
			$query = "SELECT * FROM userTable WHERE userID='".clean($_GET['id'])."' LIMIT 1";
			if(!$result = mysql_query($query)){
				die('invalid id');
			}
			$record = mysql_fetch_assoc($result);
			?>
			<p class="prev"><a href="/?cmd=super-user&action=users">Return to Previous Page</a></p>
			<h3>Edit a User</h3>
            <form class="user" action="/?cmd=super-user&action=users&id=<?php echo $record['id']; ?>" method="post" enctype="multipart/form-data">
                <label>User Name:<br />
                <input type="text" name="user" class="text" value="<?php echo $record['userName']; ?>" /></label>
                <label>Password: (Leave blank to keep the same)<br />
                <input type="text" name="pass" class="text" value="" /></label>
                <label>Email:<br />
                <input type="text" name="email" class="text" value="<?php echo $record['email']; ?>" /></label>
                <label>First Name:<br />
                <input type="text" name="fName" class="text" value="<?php echo $record['fName']; ?>" /></label>
                <label>Last Name:<br />
                <input type="text" name="lName" class="text" value="<?php echo $record['lName']; ?>" /></label>
                <label>Site:<br />
                <?php 
				$siteMenu = '<select name="site" class="dd">'."\r\n";
				$siteMenu .= '<option value="0" ';
					if('0' == $record['siteID']){
						$siteMenu .= 'selected ';
					}
				$siteMenu .= '>Super User</option>'."\r\n";
				foreach($siteArray as $key => $value){
					$siteMenu .= '<option value="'.$key.'" ';
					if($key == $record['siteID']){
						$siteMenu .= 'selected ';
					}
					$siteMenu .= '>'.$value.'</option>'."\r\n";
				}
				$siteMenu .= '</select>'."\r\n";
                echo $siteMenu;
                ?></label>
                <label>Comments:<br />
                <textarea name="comments" class="textarea"><?php echo $record['sData']; ?></textarea></label>
                <input type="hidden" name="id" value="<?php echo $record['userID']; ?>"/>
				<input type="hidden" name="action" value="user" />
                <input type="submit" class="submit" value="Submit" />
            </form>
            <form name="deleteUser" id="deleteUser" action="/?cmd=super-user&action=users" onSubmit="return confirm('Are you sure you want to delete this user?');" method="post">
                <input type="hidden" name="id" value="<?php echo $record['userID']; ?>"/>
				<input type="hidden" name="action" value="user" />
                <input type="hidden" name="cmd" value="delete" />
                <input type="submit" class="delete button-fix" value="Delete" />
            </form>
			<?php
		}else{
			?>
			<p class="prev"><a href="/?cmd=super-user">Return to Previous Page</a></p>
            <h2>Users:</h2>
            <ul class="page-list">
            	<?php
				foreach($userArray as $key => $value){
            		$userList .= '<li><a href="/?cmd=super-user&action=users&id='.$key.'">'.$value.'</a></li>';
				}
				echo $userList;
				?>
            </ul>
            <?php
		}
    }else{//default page
    ?>
        <p><a href="/?cmd=super-user&action=sites">Edit Current Sites</a></p>
        <h2>Add a Site</h2>
        <form class="site" action="/?cmd=super-user" method="post" enctype="multipart/form-data">
            <label>URL:<br />
            <input type="text" name="url" class="text" value="<?php echo $_POST['url']; ?>" /></label>
            <label>Database:<br />
            <input type="text" name="dbName" class="text" value="<?php echo $_POST['dbName']; ?>" /></label>
            <label>Database User:<br />
            <input type="text" name="dbUser" class="text" value="<?php echo $_POST['dbUser']; ?>" /></label>
            <label>Database Password:<br />
            <input type="text" name="dbPass" class="text" value="<?php echo $_POST['dbPass']; ?>" /></label>
            <label>Root:<br />
            <input type="text" name="root" class="text" value="<?php echo $_POST['root']; ?>" /></label>
            <label>Page Types:<br />
            <select multiple="multiple" name="pageTypes[]" class="multiple">
            <?php
            foreach($pages as $key => $value){
                if(!in_array($value,$globalPages) && $value != 'pages'){
                    echo '<option'; if(in_array($value,explode(',',$_POST['pageTypes']))){ echo ' selected'; } echo ' value="'.$value.'">'.$value.'</option>';
                }
            }
            ?>
            </select></label>
            <label>Spaecial Pages<br />
            <select multiple="multiple" name="specialPageTypes[]" class="multiple">
            <?php
            foreach($specialPages as $key => $value){
				echo '<option'; if(in_array($value,explode(',',$_POST['pageTypes']))){ echo ' selected'; } echo ' value="'.$value.'">'.$value.'</option>';
            }
            ?>
            </select></label>
            <label>Additional Page Types:<br />
            <textarea name="addtlPageTypes" class="textarea"><?php
            $i=1;
            foreach(explode(',',$_POST['pageTypes']) as $key => $value){
                if(!in_array($value,$pages) && !in_array($value,$specialPages)){ 
                    echo $value; if($i>0){ echo ','; } $i++;
                } 
            } ?></textarea></label>
            <label style=" display:inline-block; width: 400px;">Has Log In System?</label> <label class="radio">yes <input type="radio" name="hasUsers" <?php if($_POST['hasUsers'] == '1'){ echo 'checked ';} ?>value="1" /></label> <label class="radio">no <input type="radio" name="hasUsers" <?php if($_POST['hasUsers'] != '1'){ echo 'checked ';} ?>value="0" /></label>
				<label style=" display:inline-block; width: 400px;">Has CMS Pages?</label> <label class="radio">yes <input type="radio" name="hasPages" <?php if($_POST['hasPages'] == '1'){ echo 'checked ';} ?>value="1" /></label> <label class="radio">no <input type="radio" name="hasPages" <?php if($_POST['hasPages'] != '1'){ echo 'checked ';} ?>value="0" /></label>
                <div clas="clear"></div>
            <input type="hidden" name="action" value="site" />
            <input type="submit" class="submit" value="Submit" style="margin-bottom: 10px;" />
        </form>
        <hr />
        <p><a href="/?cmd=super-user&action=users">Edit Current Users</a></p>
        <h2>Add a User</h2>
        <form class="user" action="/?cmd=super-user" method="post" enctype="multipart/form-data">
            <label>User Name:<br />
            <input type="text" name="user" class="text" value="<?php echo $_POST['user']; ?>" /></label>
            <label>Password:<br />
            <input type="text" name="pass" class="text" value="<?php echo $_POST['pass']; ?>" /></label>
            <label>Email:<br />
            <input type="text" name="email" class="text" value="<?php echo $_POST['email']; ?>" /></label>
            <label>Site:<br />
            <?php 
			echo $siteMenu;
			?></label>
            <label>Comments:<br />
            <textarea name="comments" class="textarea"><?php echo $_POST['comments']; ?></textarea></label>
				<input type="hidden" name="action" value="user" />
            <input type="submit" class="submit" value="submit" />
        </form>
    <?php
    }
    ?>
</div>