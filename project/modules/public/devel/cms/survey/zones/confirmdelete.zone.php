<?php
/**
* @package	cms
* @subpackage survey
* @author	Bertrand Yan
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* @package	cms
* @subpackage survey
* Zone for delete validation
*/
class ZoneConfirmDelete extends CopixZone {
   function _createContent (&$toReturn){
      $tpl = & new CopixTpl ();

      $dao = CopixDAOFactory::getInstanceOf ('Survey');
      $tpl->assign('toDelete',$dao->get($this->_params['id_svy']));

      $toReturn = $tpl->fetch ('confirm.delete.tpl');

      return true;
   }
}
?>
