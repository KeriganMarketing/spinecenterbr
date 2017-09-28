<div id="pages">

	<script type="text/javascript">
        $(function(){
            $('#tabs').tabs();
			$("#sortable").sortable();
        });
    </script>
<?php

if($allowed){
		
	$query = "SELECT * FROM pageTable WHERE parent IS NULL ORDER BY navOrder ASC";
	$pageArray = array();
	if($result = mysql_query($query)){
		while($record = mysql_fetch_assoc($result)){
			$a = $record['pageID'].'~'.$record['controller'];
			$b = $record['title'];
			$pageArray[$a] = $b;
		}
	}
	?>
    <h1>Manage the Pages of Your Site</h1>
   
    <?php
    if($error != ''){
        echo '<p class="error">'.$error.'</p>';
    }
    if($msg != ''){
        echo '<p class="success">'.$msg.'</p>';
    }elseif($body == 'archive' && isset($_GET['id']) && is_numeric($_GET['id'])){
		$archiveID = $_GET['id'];
		$archiveQuery = "SELECT * FROM pageArchive WHERE pageID=$archiveID ORDER BY time ASC";
		if(!$archiveResult = mysql_query($archiveQuery)){
			die('invalid id');
		}
		?>
        <p class="prev"><a href="/?cmd=pages&id=<?php echo $_GET['id']; ?>">Return to Previous Page</a></p>
        <h2>Previous Versions</h2>
            <ul class="page-list">
                <?php
				$pageList = '';
				$i=0;
				while($archiveRecord = mysql_fetch_assoc($archiveResult)){
					$pageList .= '
						<li><a name="archive'.$record['id'].'" href="#archive'.$archiveRecord['id'].'" onClick="setVisibility(\'preview'.$archiveRecord['id'].'\', \'block\');">'.$archiveRecord['time'].'</a></li>
						<div class="content-box" id="preview'.$archiveRecord['id'].'" style="display: none;">
							'.stripslashes($archiveRecord['content']).'
							<hr style="clear: both; margin: 15px 0 0 0;" />
							<form action="/?cmd=pages&id='.$_GET['id'].'" method="post" enctype="multipart/form-data">
								<input type="hidden" name="id" value="'.$archiveRecord['id'].'" />
								<input type="hidden" name="action" value="revert" />
								<input type="submit" class="submit" value="Restore" />
							</form>
						</div>
					';
					$i=1;
				}
				if($i <= 0){
					$pageList = '<li>You don\'t appear to have archived versions of this page.</li>';
				}
                echo $pageList;
                ?>
            </ul>
        <?php
	}else{//they wanna do something with a page
        if(isset($_GET['id']) && is_numeric($_GET['id'])){//they've a specific page in mind
			$id = $_GET['id'];
            $query = "SELECT * FROM pageTable WHERE pageID='$id' LIMIT 1";
            if(!$result = mysql_query($query)){
                die('invalid id');
            }
            $record = mysql_fetch_assoc($result);
			$meta = explode('[/split/]',$record['meta']);
			
			
            ?>
            <p class="prev"><a href="/?cmd=pages">Return to Previous Page</a></p>
            <h2>Edit a Page</h2>
            <form id="page" class="site" name="page" action="/?cmd=pages&action=page&id=<?php echo $record['pageID']; ?>" method="post" enctype="multipart/form-data">
                <label>Page Name: <br><p style="font-size:12px; font-weight:normal;">This the name of the page used in the navigation area.</p>
                <input title="This will be how the page shows in the navigation." type="text" name="title" class="text" value="<?php echo $record['title']; ?>" /></label>
                <label>Shortlink: <br><p style="font-size:12px; font-weight:normal;">Required for the URL in the address bar. Only lowercase latters, numbers and hyphens allowed. No spaces, commas or punctuation.</p>
                <input title="This will be how the page shows in the URL / address bar." type="text" name="controller" class="text" value="<?php echo $record['controller']; ?>" /></label>
                <?php if($hasVanTitle){ ?>
                	<label>Page Title: <p style="font-size:12px; font-weight:normal;">This the text used in the tab or title bar of the browser. Anything over 70 characters will be shortened. We recommend using keywords in the beginning.</p>
                	<input title="This the page title used by the browser." onKeyUp="count(this,'titlecount',70);keywordIn(this,'keywordvantitle');" onClick="count(this,'titlecount',70);keywordIn(this,'keywordvantitle');" type="text" name="vanTitle" class="text" value="<?php echo $record['vanTitle']; ?>" /></label>
                    <p style="font-weight:normal;">Characters Left: <input type="text" id="titlecount" readonly name="titlecount" style="width:50px; border:none;"> &nbsp; &nbsp; Are your keyword(s) included? <input type="text" id="keywordvantitle" name="keywordvantitle" readonly style="width:200px; border:none;" ></p><br>
                <?php } ?>
                <label>Page Type: <br><p style="font-size:12px; font-weight:normal;">Template to be used for the page. Unless instructed otherwise, this will always be "support."</p>
                <select title="What type of page is this?" name="type" class="dd">
                	<option	<?php if($record['pageType'] == 'support'){ echo ' selected'; }?> value="support">support</option>
                	<option	<?php if($record['pageType'] == 'home'){ echo ' selected'; }?> value="home">home</option>
                <?php foreach($pageTypes as $key => $value){
					if(!in_array($value,$specialPages) && $value != ''){
						echo '<option value="'.$value.'"';
						if($record['pageType'] == $value){
							echo ' selected';
						}
						echo ' >'.$value.'&nbsp;</option>';
					}
				}?>
                </select>
                </label>
                <?php if($hasHeadline){ ?>
                	<label>Headline:<br><p style="font-size:12px; font-weight:normal;">This the title(H1) of the page.</p>
                	<input title="This the title(H1) of the page." type="text" name="headline" class="text" value="<?php echo $record['headline']; ?>" /></label>
                <?php } ?>
                
                <?php if($hasPostedDates){ ?>
                	<label>Posted On:<br>
                	<input type="text" name="postedon" class="text" value="<?php echo $record['postedon']; ?>" /></label>
                <?php } ?>
                
                <label>Content: <?php if($archives){ ?><span class="small">[<a href="/?cmd=pages&action=archive&id=<?php echo $record['pageID']; ?>">manage previous versions of this page</a>]</span><?php } ?><br />
                <textarea id="pageContent" name="pageContent"><?php echo stripslashes($record['pageContent']); ?></textarea></label>
                <textarea name="archive" style="display: none;"><?php echo stripslashes($record['pageContent']); ?></textarea> 
                <label style=" display:inline-block; width: 500px;">Is this page in the main navigation?</label> <label class="radio">yes <input  title="Do not hide this page" type="radio" name="inNav" <?php if($record['inNav'] == '1'){ echo 'checked ';} ?>value="1" /></label> <label class="radio">no <input title="Hide this page" type="radio" name="inNav" <?php if($record['inNav'] != '1'){ echo 'checked ';} ?>value="0" /></label>
                <label style=" display:inline-block; width: 500px;">Is this page searchable?</label> <label class="radio">yes <input title="This page is searchable" type="radio" name="searchIndex" <?php if($record['searchIndex'] == '1'){ echo 'checked ';} ?>value="1" /></label> <label class="radio">no <input title="This page is not searchable" type="radio" name="searchIndex" <?php if($record['searchIndex'] != '1'){ echo 'checked ';} ?>value="0" /></label>
                <?php if($loginSystem){ ?>
                <label style=" display:inline-block; width: 500px;">Does this page require log in?</label> <label class="radio">yes <input title="Visitors are required to log in" type="radio" name="loginRequired" <?php if($record['loginRequired'] == '1'){ echo 'checked ';} ?>value="1" /></label> <label class="radio">no <input title="Visitors are not required to log in" type="radio" name="loginRequired" <?php if($record['loginRequired'] != '1'){ echo 'checked ';} ?>value="0" /></label>
                <?php } ?>
                <br><br>
                 
                <?php if($hasImg){ ?>
                <label>Featured Image: <br />
                <input type="file" id="image" name="image" /></label>
                	<?php if($record['featuredImage'] != ''){ ?>
                    <br>Current featured image:<br>
                    <img src="<?php echo $site; ?>/images/uploads/<?php echo $record['featuredImage']; ?>" style="max-height:200px;">
               		<?php } ?>
                    <br><br>
           		<?php } ?>

                <label>Page Description: <br><p style="font-size:12px; font-weight:normal;">While not important to search engine rankings, this is extremely important for gaining user click-throughs from search engines. Explain what the page contains in 150 - 160 characters. <strong>Avoid Duplicate Meta Descriptions!</strong><br> <a target="_blank" href="http://moz.com/learn/seo/meta-description">Read more about Meta Descriptions</a></p>
                <textarea id="metaDesc" class="textarea" name="metaDesc" onKeyUp="count(this,'metacount',160);keywordIn(this,'keywordverified');" onClick="count(this,'metacount',160);keywordIn(this,'keywordverified');" ><?php echo $meta[0]; ?></textarea></label>
                <p style="font-weight:normal;">Characters Left: <input type="text" id="metacount" readonly name="metacount" style="width:50px; border:none;"> &nbsp; &nbsp; Are your keyword(s) included? <input type="text" id="keywordverified" name="keywordverified" readonly style="width:200px; border:none;" ></span><br>

                <label>Page Keywords: <br><p style="font-size:12px; font-weight:normal;">Not important to search engine rankings, but we use this information to show you how well you've used your keywords on this page. Use one or two main words.</p>
                <textarea id="metaKeys" class="textarea" name="metaKeys"><?php echo $meta[1]; ?></textarea></label>
                <label>Addtl. Header info: <br><p style="font-size:12px; font-weight:normal;">code to appear inside the &lt;head&gt; tag</p>
                <textarea id="headers" class="textarea" name="headers"><?php echo $record['headers']; ?></textarea></label>
                <input type="hidden" name="action" value="page" />
                <input type="hidden" name="id" value="<?php echo $record['pageID']; ?>" />
                <input type="submit" class="submit" value="Submit" />
            </form>
            <form name="deleteSite" id="deleteSite" action="/?cmd=pages&action=page" onSubmit="return confirm('Are you sure you want to delete this page?');" method="post">
                <input type="hidden" name="id" value="<?php echo $record['pageID']; ?>"/>
                <input type="hidden" name="action" value="page" />
                <input type="hidden" name="cmd" value="delete" />
                <input type="submit" class="delete button-fix" value="Delete" id="page-delete" />
            </form>
            <?php
        }else{// they wanna see what pages they've got
            ?>
            <div id="tabs">
                <ul>
                    <li><a href="#manage">Manage Existing Pages</a></li>
                    <li><a href="#order">Update Page Order</a></li>
                    <li><a href="#add">Add a New Page</a></li>
                </ul>
                <div id="manage">
            <ul class="page-list">
            	<p>Edit a page by clicking below.</p>
                <?php
				if($pageArray){
					foreach($pageArray as $key => $value){
						$k = explode('~',$key);
						$id = $k[0];
						$cont = $k[1];
						$pageList .= '<li><a href="/?cmd=pages&id='.$id.'">'.crop($value,70).'</a></li>';
						$query = "SELECT * FROM pageTable WHERE parent='$cont' ORDER BY navOrder ASC";
						if($result = mysql_query($query)){
							while($record = mysql_fetch_assoc($result)){
								$pageList .= '<ul><li><a href="/?cmd=pages&id='.$record['pageID'].'">'.$record['title'].'</a></li></ul>';
							}
						}
					}
				}else{
					$pageList = '<li>You don\'t appear to have any top-level pages.</li>';
				}
                echo $pageList;
                ?>
            </ul>
                </div>
                <div id="order">
                    <p>Click and drag the pages below into the order you'd like them to be in. Once finished, click submit.</p>
                    <form action="/?cmd=pages" method="post" enctype="multipart/form-data">
                    	<ul id="sortable" class="navorder">
                            <?php
							$pageSorters = array('sortable');
							$pageList = '';
                            if($pageArray){
                                foreach($pageArray as $key => $value){
                                    $k = explode('~',$key);
                                    $id = $k[0];
                                    $cont = $k[1];
                                    $query = "SELECT navOrder FROM pageTable WHERE pageID='$id' AND parent is NULL";
                                    $result = mysql_query($query);
                                    $record = mysql_fetch_assoc($result);
                                    $pageList .= '<li class="ui-state-default" id="'.$id.'">'.crop($value,40).'<input type="hidden" name="navOrder[]" value="'.$id.'" />';
                                    $sql = "SELECT * FROM pageTable WHERE parent='$cont' ORDER BY navOrder ASC";
                                    $res = mysql_query($sql);
                                    if(mysql_num_rows($res)!=0){
										$pageList .= '<script type="text/javascript"> $(function(){ $( "#sortable'.$id.'" ).sortable(); }); </script> <ul id="sortable'.$id.'">';
										$pageSorters[] = 'sortable'.$id;
                                        while($rec = mysql_fetch_assoc($res)){
                                            $pageList .= '<li class="ui-state-default" id="'.$rec['pageID'].'">&nbsp;&nbsp;&nbsp;&nbsp;'.$rec['title'].'<input type="hidden" name="subNavOrder[]" value="'.$rec['pageID'].'" /></li>';
                                        }
                                        $pageList .= '</ul>'."\r\n";
                                    }
									$pageList .= '</li>'."\r\n";
                                }
                            }else{
                                $pageList = '<li>You don\'t appear to have any top-level pages.</li>'."\r\n";
                            }
                            echo $pageList;
                            ?>
                        </ul>
                        <input type="hidden" name="action" value="navOrder" />
                        <input type="submit" class="submit" value="Submit" />
                    </form>
                </div>
                <div id="add">
                    <form id="page" class="site" action="/?cmd=pages&action=page" method="post" enctype="multipart/form-data">
                        <label>Page Name: <br><p style="font-size:12px; font-weight:normal;">This the name of the page used in the navigation area.</p>
                        <input title="This will be how the page shows in the navigation." type="text" name="title" class="text" value="<?php echo $_POST['title']; ?>" onKeyUp="showurl(this,'urlexample');" /></label>
                        <p style="font-weight:normal;">The shortlink for the page will be: <input type="text" id="urlexample" name="urlexample" style="color:#06F; border:none; width:300px;" ></p><br>

                         <label>Page Keywords: <br><p style="font-size:12px; font-weight:normal;">Not important to search engine rankings, but we use this information to show you how well you've used your keywords on this page. Use one or two main words.</p>
                		<textarea id="metaKeys" class="textarea" name="metaKeys" style="height:50px;"><?php if($_POST['metaKeys'] != ''){ echo $_POST['metaKeys']; } ?></textarea></label>
                
                        <!--<label>Controller: <br />
<span class="small">(Unique identifier for the page used in the URL / address bar.)</span><br />-->
                        <input title="This will be how the page shows in the URL / address bar." type="hidden" name="controller" class="text" value="auto" /><!--</label>-->
                        <?php if($hasVanTitle){ ?>
                            <label>Page Title: <p style="font-size:12px; font-weight:normal;">This the text used in the tab or title bar of the browser. Anything over 70 characters will be shortened. We recommend using keywords in the beginning.</p>
                            <input title="This is the page title used by the browser." onKeyUp="count(this,'titlecount',70);keywordIn(this,'keywordvantitle');" onClick="count(this,'titlecount',70);keywordIn(this,'keywordvantitle');" type="text" name="vanTitle" class="text" value="<?php echo $_POST['vanTitle']; ?>" /></label>
                            <p style="font-weight:normal;">Characters Left: <input type="text" id="titlecount" readonly name="titlecount" style="width:50px; border:none;"> &nbsp; &nbsp; Are your keyword(s) included? <input type="text" id="keywordvantitle" name="keywordvantitle" readonly style="width:200px; border:none;" ></p><br>
                        <?php } ?>
                        <label style=" display:inline-block; width: 320px;">Page Type:<br><p style="font-size:12px; font-weight:normal;">Template to be used for the page. Unless instructed otherwise, this will always be "support."</p>
                        <select title="What type of page is this?" name="type" class="dd">
                            <option	<?php if($_POST['type'] == 'support'){ echo ' selected'; }?> value="support">support</option>
                            <option	<?php if($_POST['type'] == 'home'){ echo ' selected'; }?> value="home">home</option>
                        <?php foreach($pageTypes as $key => $value){
                            if(!in_array($value,$specialPages) && $value != ''){
                                echo '<option value="'.$value.'"';
                                if($_POST['type'] == $value){
                                    echo ' selected';
                                }
                                echo ' >'.$value.'&nbsp;</option>';
                            }
                        }?>
                        </select>
                        </label>
                        <label style=" display:inline-block; width: 260px;">Parent Page:<br><p style="font-size:12px; font-weight:normal;">Category or Navigation Parent.</p>
                        <select title="Is this page a sub-page?" name="parent" class="dd">
                        <?php
                        $pageList = '<option value="">No Parent Page</option>';
                        array_unshift($pageList,'home','support');
                        foreach($pageArray as $key => $value){
                            $k = explode('~',$key);
                            $key = $k[1];
                            $pageList .= '<option value="'.$key.'"';
                            if($_POST['parent'] == $key){
                                $pageList .= ' selected';
                            }
                            $pageList .= '>'.crop($value,40).'</option>';
                        }
                        echo $pageList;
                        ?>
                        </select>
                        </label>
                        <?php if($hasHeadline){ ?>
                            <label>Headline: <br><p style="font-size:12px; font-weight:normal;">This the title(H1) of the page.</p>
                            <input title="This the title(H1) of the page." type="text" name="headline" class="text" value="<?php echo $_POST['headline']; ?>" /></label>
                        <?php } ?>
                        <?php if($hasPostedDates){ ?>
                            <label>Posted On:<br>
                            <input type="text" name="postedon" class="text" value="<?php echo $_POST['postedon']; ?>" /></label>
                        <?php } ?>
                        <label>Content:<br />
                        <textarea class="pageContent" name="pageContent"><?php echo stripslashes($_POST['pageContent']); ?></textarea></label>
                        <label style=" display:inline-block; width: 500px;">Is this page in the main navigation?</label> <label class="radio">yes <input title="Do not hide this page" type="radio" name="inNav" <?php if($_POST['inNav'] != '0'){ echo 'checked ';} ?>value="1" /></label> <label class="radio">no <input title="Hide this page" type="radio" name="inNav" <?php if($_POST['inNav'] == '0'){ echo 'checked ';} ?>value="0" /></label>
                        <label style=" display:inline-block; width: 500px;">Is this page searchable?</label> <label class="radio">yes <input title="This page is searchable" type="radio" name="searchIndex" <?php if($_POST['searchIndex'] != '0'){ echo 'checked ';} ?>value="1" /></label> <label class="radio">no <input title="This page is not searchable" type="radio" name="searchIndex" <?php if($_POST['searchIndex'] == '0'){ echo 'checked ';} ?>value="0" /></label>
                        <?php if($loginSystem){ ?>
                        <label style=" display:inline-block; width: 500px;">Does this page require log in?</label> <label class="radio">yes <input title="Visitors are required to log in" type="radio" name="loginRequired" <?php if($_POST['loginRequired'] == '1'){ echo 'checked ';} ?>value="1" /></label> <label class="radio">no <input title="Visitors are not required to log in" type="radio" name="loginRequired" <?php if($_POST['loginRequired'] != '1'){ echo 'checked ';} ?>value="0" /></label>
                        <?php } ?>
                        <br>
                         <br>
                         <?php if($hasImg){ ?>
                        <label>Featured Image: <br />
                        <input type="file" id="image" name="image" /></label> 
                          <br><br> 
                        <?php } ?>
                <label>Page Description:<br><p style="font-size:12px; font-weight:normal;">While not important to search engine rankings, this is extremely important for gaining user click-throughs from search engines. Explain what the page contains in 150 - 160 characters. <strong>Avoid Duplicate Meta Descriptions!</strong><br> <a target="_blank" href="http://moz.com/learn/seo/meta-description">Read more about Meta Descriptions</a></p>
                <textarea id="metaDesc" class="textarea" name="metaDesc"><?php if($_POST['metaDesc'] != ''){ echo $_POST['metaDesc']; } ?></textarea></label>

                <label>Addtl. Header info: <br><p style="font-size:12px; font-weight:normal;">code to appear inside the &lt;head&gt; tag</p>
                <textarea id="headers" class="textarea" name="headers"><?php if($_POST['headers'] != ''){ echo $_POST['headers']; } ?></textarea></label>
                
                        <input type="hidden" name="action" value="page" />
                        <input type="hidden" name="id" value="<?php echo $_POST['id']; ?>" />
                        <div class="clear"></div>
                        <input type="submit" class="submit" value="Submit" />
                    </form>
                </div>
            </div>
            <?php
        }
    }
		//GET THE WYSISYG 
	?>
    </div>
    <script>
    CKEDITOR.replace( 'pageContent', {
		<?php //if($siteID =='14' || $siteID =='5'){?>
		filebrowserBrowseUrl: '/filemanager/index.php?filemanager=1&site=|<?php echo $siteID; ?>|',
		<?php //} ?>
    });
	</script>
    
<?php	
}else{
	echo '<p>You have no CMS pages on your site, please <a href="/?cmd='.$cmsPages[0].'">click here</a> to continue.</p>';
}
if($siteID =='14'){
parse_str($_SERVER['REQUEST_URI'], $output);
//print_r($output);
echo $output['id'].'<br>';
echo $output['site'].'<br>';
echo $output['filemanager'].'<br>';
}
?>
</div>