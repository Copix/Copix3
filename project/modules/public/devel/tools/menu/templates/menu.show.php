<?php
/**
 * Affichage d'un niveau de menu
 *
 * @param array $pElements	les éléments de menu à afficher
 */
function _showLevel ($pElements, $pLevel = 0, $modeEdit = false){
	$toReturn = "\n\r";
   	$toReturn .= '<ul>';    	
	foreach ($pElements as $item_index => $item_infos) {
		$toReturn .= '<li><a href="' . _url ($item_infos->link_item) . '">' . $item_infos->rub_name . '</a></li>';
		$toReturn .= _showLevel ($item_infos->childs, $pLevel+1, $modeEdit);   			
   	}
	$toReturn .= '</ul>';
	return $toReturn;
}

echo _showLevel ($arMenuItems, 0, $writeEnabled);
if ($writeEnabled) {
	echo '<a href="' . _url ('menu|adminitems|edit', array('id_menu' => _request ('id_menu'), 'id_parent' => 'null')) . '">';
	echo '<img src="' . _resource ('img/tools/add.png') . '" alt="' . _i18n ('copix:common.buttons.add') . '" title="' . _i18n ('copix:common.buttons.add') . '" />';
	echo '</a><br />';
}
?>