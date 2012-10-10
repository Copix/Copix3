<?php
/**
 * @package		tools
 * @subpackage	menu
 * @author	 	Steevan BARBOYON
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Zone qui affiche un menu
 * @package	tools
 * @subpackage menu
 */
class ZoneMenu extends CopixZone {
    function _createContent (&$toReturn){
		$tpl = new CopixTpl ();
		$tpl->assign ('arMenuItems', _class ('ItemsServices')->getMenu ($this->getParam ('id_menu')));
		$tpl->assign ('paste', $this->getParam ('paste'));
		$tpl->assign ('id_menu', $this->getParam ('id_menu'));
    	$toReturn = $tpl->fetch ($this->getParam ('admin', false) ? 'menu.admin.php' : 'menu.show.php');
        return true;
    }
}
?>