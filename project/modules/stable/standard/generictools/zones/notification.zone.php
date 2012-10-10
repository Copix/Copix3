<?php
/**
 * Affiche des informations s'il y en a
 *
 */

class ZoneNotification extends CopixZone {
	function _createContent (& $toReturn){
		$toReturn = '';
		$ppo = new CopixPpo ();
		$tpl = new CopixTpl ();
		$data = $this->asArray();
		if ( get_class( $data ) == 'Messages' ) {
			$data = $data->asArray();
		}
		foreach( $data as $level => $items ){
			if( !$items ){
				continue;
			}
			if ( get_class( $items ) == 'CopixErrorObject' ) {
				$items = $items->asArray ();
			} else {
				$items = (array)$items;
			}
			if( count($items) ){
				$ppo->items = $items;
				$ppo->level = $level;
				$tpl->assign ('ppo', $ppo);
				$toReturn .= $tpl->fetch ( 'zone.notification.php' );
			}
		}
		return true;
	}
}