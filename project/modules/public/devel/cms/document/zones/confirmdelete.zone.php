<?php
/**
* @package cms
* @subpackage document
* @author	Bertrand Yan, Croes Gérald
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* @package	cms
* @subpackage document
* Zone for delete validation
*/
class ZoneConfirmDelete extends CopixZone {
   function _createContent (&$toReturn){
      $tpl = & new CopixTpl ();

      $dao = CopixDAOFactory::getInstanceOf ('Document');
      $tpl->assign('toDelete',$dao->get($this->_params['id_doc'], $dao->getLastVersion($this->_params['id_doc'])));

      $toReturn = $tpl->fetch ('confirm.delete.tpl');

      return true;
   }
}
?>
