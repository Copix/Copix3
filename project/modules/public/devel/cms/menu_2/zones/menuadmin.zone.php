<?php
/**
 * @package	cms
 * @subpackage menu_2
 * @author	Sylvain DACLIN
 * @copyright 2001-2006 CopixTeam
 * @link		http://copix.org
 * @license  http://www.gnu.org/licenses/lgpl.htmlGNU Leser General Public Licence, see LICENCE file
 */
/**
 * @package	cms
 * @subpackage menu_2
 * Zone that handles the menu administration.
 */
class ZoneMenuAdmin extends CopixZone {
   function _createContent (&$toReturn) {
      $tpl = new CopixTpl ();
      
      $dao = CopixDAOFactory::getInstanceOf ('Menu');
      if ($this->_params['id_menu']->id_menu!='') {
      	$tpl->assign ('pasteEnabled', (isset($_SESSION['MODULE_MENU_CUTEDMENU'])) ? true : false);
      }
      $tpl->assign ('pathMenu', $dao->getPath($this->_params['id_menu']));
      $tpl->assign ('currentMenu', $dao->getWithProfile($this->_params['id_menu']));
      $tpl->assign ('arChilds', $dao->getMenu($this->_params['id_menu'],array('depth'=>1,'isOnline'=>0)));
      $tpl->assign ('adminValue', PROFILE_CCV_ADMIN);
      
      $toReturn = $tpl->fetch ('menu.admin.tpl');
      return true;
   }
}
?>