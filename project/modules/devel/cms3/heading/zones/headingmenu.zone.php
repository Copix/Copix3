<?php
/**
 * @package     cms
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      Gérald Croës
 */


/**
 * Affiche les enfants rubrique d'une rubrique. S'il n'existe aucun enfant, n'affiche rien
 * @package     cms
 * @subpackage  heading
 */
class ZoneHeadingMenu extends CopixZone {
	function _createContent (& $toReturn) {
		$tpl = new CopixTpl ();
		$tpl->assign ('heading', $heading = $this->getParam ('heading'));
		$tpl->assign ('arHeadings', _ioClass ('headingelementinformationservices')->getHeadingChildren ($heading, true));
		$toReturn = $tpl->fetch ('headingmenu.tpl');
		return true;
	}
}