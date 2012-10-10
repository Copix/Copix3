<?php
/**
 * Affichage d'un niveau de menu
 *
 * @param array $pElements	les éléments de menu à afficher
 */
function _showLevel ($pElements, $pLevel = 0, $paste = null){
	$toReturn = "\n\r";
	if ($pLevel==0){
	   	$toReturn .= '<table class="CopixTable">';
	}    	
	foreach ($pElements as $item_index => $item_infos) {
		$toReturn .= '<tr'._tag ('cycle', array ('name'=>'adminmenu', 'values'=>', class="alternate" ')).'><td>' . str_repeat ('&nbsp;', $pLevel) . $item_infos->name_item . '</td><td>';

		//editer
		$toReturn .= '&nbsp;'._tag ('copixicon', array ('type'=>'update', 'href'=>_url ('menu|adminitems|edit', array('id_menu' => _request ('id_menu'), 'id_item' => $item_infos->id_item))));
		//ajouter
		$toReturn .= '&nbsp;'._tag ('copixicon', array ('type'=>'add', 'href'=>_url ('menu|adminitems|edit', array('id_menu' => _request ('id_menu'), 'id_parent' => $item_infos->id_item))));
		//supprimer 
		$toReturn .= '&nbsp;'._tag ('copixicon', array ('type'=>'delete', 'href'=>_url ('menu|adminitems|delete', array('id_menu' => _request ('id_menu'), 'id_item' => $item_infos->id_item))));
	 	//couper
		$toReturn .= '&nbsp;'._tag ('copixicon', array ('type'=>'cut', 'href'=>_url ('menu|adminitems|cut', array('id_menu' => _request ('id_menu'), 'id_item' => $item_infos->id_item))));
		//coller
		if ($paste !== null){
			$toReturn .= '&nbsp;'._tag ('copixicon', array ('type'=>'paste', 'href'=>_url ('menu|adminitems|paste', array('id_menu' => $item_infos->id_menu, 'id_item' => $item_infos->id_item))));
		}
		// 	image monter
		if ($item_index > 0) {
			$toReturn .= '&nbsp;'._tag ('copixicon', array ('type'=>'move_up', 'href'=>_url ('menu|adminitems|up', array ('id_menu' => _request ('id_menu'), 'id_item' => $item_infos->id_item))));			
		}
		// image descendre rubrique
		if ($item_index < count ($pElements) - 1) {
			$toReturn .= '&nbsp;'._tag ('copixicon', array ('type'=>'move_down', 'href'=>_url ('menu|adminitems|down', array('id_menu' => _request ('id_menu'), 'id_item' => $item_infos->id_item))));			
		}
		$toReturn .= '</td></tr>';
		if (count ($item_infos->childs)){
			$toReturn .= _showLevel ($item_infos->childs, $pLevel+1, $paste);   			
		}
	}
	if ($pLevel==0){
		$toReturn .= '</table>';
	}
	return $toReturn;
}

echo _showLevel ($arMenuItems, 0, $paste);
echo '<a href="' . _url ('menu|adminitems|edit', array('id_menu' => _request ('id_menu'))) . '">';
echo '<img src="' . _resource ('img/tools/add.png') . '" alt="' . _i18n ('copix:common.buttons.add') . '" title="' . _i18n ('copix:common.buttons.add') . '" />';
echo '</a><br />';
if ($paste !== null){
	echo _tag ('copixicon', array ('type'=>'paste', 'href'=>_url ('menu|adminitems|paste', array('id_menu' => $id_menu, 'id_item' => null))));
}
?>