<?php
/**
 * @package cms3
 * @subpackage form
 * @author nicolas bastien
 */

/**
 * Affichage du menu d'édition des formulaires
 * @package cms3
 * @subpackage form
 * @author nicolas bastien
 */
class ZoneFormMenu extends CopixZone {
	
	public function _createContent (&$toReturn){
		if (_request('editId') == null) {
			//Mode visualisation
			return true;
		}
		CopixHTMLHeader::addJSLink(_resource('heading|js/portalgeneralmenu.js'));
		$tpl = new CopixTpl ();
		
		$mode = $this->getParam('mode', 'main');
		
		//Le mode sert à l'affichage du bouton édition contenu / info générales
		$tpl->assign ('mode', $mode);
		
		$toReturn = $tpl->fetch ('form.menu.tpl');		
		return true;
	}
}
?>