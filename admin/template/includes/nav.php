<div id="nav">
        <ul>
            <?php
            foreach($pages as $key => $value){
				if(in_array($value,$cmsPages)){
					echo '<li><a href="/?cmd='.$value.'"';
					if($cont == $value){ echo 'class="id"'; }
					echo '>'.$key.'</a></li>'."\r\n";
				}
            }
            foreach($specialPages as $key => $value){
				if(in_array($value,$cmsPages)){
					echo '<li><a href="/?cmd='.$value.'"';
					if($cont == $value){ echo 'class="id"'; }
					echo '>'.$key.'</a></li>'."\r\n";
				}
            }
            ?>
        </ul>
</div>