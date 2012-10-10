<?php 
	foreach ($ppo->elements as $element){
		echo "<a href='"._url('heading||', array('public_id'=>$element->public_id_hei))."'>".$element->caption_hei."</a><br />"; 
	}
?>