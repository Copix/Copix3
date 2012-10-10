<?php
/**
 * @package standard
 * @subpackage admin 
 * 
 * @copyright CopixTeam
 * @license lgpl
 * @author Croës Gérald
 * @since 3.0.4 
 */

/**
 * Affiche un menu avec les liens d'administration disponibles pour l'utilisateur actuellement connecté
 * 
 * @package standard
 * @subpackage admin
 */
class ZoneAdminMenu extends CopixZone {
	protected function _createContent (& $pToReturn){
		$ppo = _ppo ();
		$ppo->links = _class ('adminmenu')->getLinks ();
		$pToReturn = $this->_usePPO ($ppo, 'adminmenu.php');
		return true;
	} 
}