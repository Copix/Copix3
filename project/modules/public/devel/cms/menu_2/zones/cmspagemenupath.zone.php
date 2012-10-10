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
 * show navigate link.
 */
class ZoneCmsPageMenuPath extends CopixZone {
   function _createContent (& $toReturn) {
      $tpl      = & new CopixTpl ();
      if (isset($this->_params['id_cmsp'])) {
      
      }
      $menu    = & CopixClassesFactory::createDAO ('menu_2|Menu');
      $currentMenu = $menu->findMenuByIdCMSP($this->_params['id_cmsp']);
      $arPath = $menu->getPath($currentMenu->id_menu);
      
      $toReturn = $tpl->fetch ('menu_2|arianelink.ptpl');
   }
}
?>