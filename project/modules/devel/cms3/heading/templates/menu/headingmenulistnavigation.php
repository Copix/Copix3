<div id="menu" class="headingmenu" <?php if( $type ){ echo 'caption="'.$type.'"'; } ?>>
	<ul>
	<?php
		foreach ($tree as $item){
			echo '<li><a href="'.$item->path.'">'.$item->caption_hei.'</a></li>';
		}
		?>
	</ul>
</div>