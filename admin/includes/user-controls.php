<div id="header">
    <div id="header-left" class="content-block res-50">
        <p id="welcome">Welcome, <?php echo $currentUserName; ?> | <a href="/password.php" onClick="return popup(this,'help')">Update Your Password</a> | <a href="/?cmd=logout">Log Out</a></p>
    </div>
    <div id="header-right" class="content-block res-50">
        <p align="right">
        <?php
        if($isSuperUser){
            
            //$siteArray = get_site_array(); //now located in config.php file
            //print_r($siteArray);
           /* $siteMenu = '<form style="display: block; text-align: right;" action="/?cmd='.$cont.'" method="post">'."\r\n";
            $siteMenu .='<a style="margin:3px 5px; font-size:13px; display:inline-block; border-radius:4px; border:1px solid #999; background:#ccc; text-decoration:none; padding:5px; " href="/?cmd=super-user" >Super User Menu</a> ';
            $siteMenu .='<select name="changeSiteID" class="dd" onChange="form.submit();">'."\r\n";
            foreach($siteArray as $key => $value){
                $siteMenu .= '<option value="'.$key.'" ';
                if($key == $_COOKIE['siteID']){
                    $siteMenu .= 'selected ';
                }
                $siteMenu .= '>'.$value.'</option>'."\r\n";
            }
            $siteMenu .= '</select>'."\r\n";
            $siteMenu .= '<noscript><input style="" type="submit" value="Change" /></noscript>'."\r\n";
            $siteMenu .= '</form>'."\r\n";
            echo $siteMenu;*/
        }
        ?></p>
    </div>
</div>
<div id="top-texture"></div>