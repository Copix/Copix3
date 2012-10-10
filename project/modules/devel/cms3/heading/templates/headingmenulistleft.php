<?php
if (!function_exists('formatAsListLeft')){
	function formatAsListLeft( $items ){
		$toReturn = '<ul>';
		foreach( $items as $item ){
			$toReturn .= '<li><a href="'.$item->path.'">'.$item->caption_hei.'</a>';
			if( $item->children ){
				$toReturn .= formatAsListLeft( $item->children );
			}
			$toReturn .= '</li>';
		}
		$toReturn .= '</ul>';
		return $toReturn;
	}
}
echo formatAsListLeft ($tree);
?>