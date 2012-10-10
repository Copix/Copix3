<ul class="headingmenu" <?php if( $type ){ echo 'caption="'.$type.'"'; } ?>>
<?php
	$i = 1;
	foreach ($tree as $item){	
		$classa = ($item->public_id_hei == _request("public_id", false) ? "class='selected'" : "");
		$classli = (count($tree) == $i ? "class='end'" : "");
		echo '<li '.$classli.'><a '.$classa.' href="'.$item->path.'">'.$item->caption_hei.'</a></li>';
		$i++;
	}
	?>
</ul>