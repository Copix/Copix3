<?php
/**
* @package	cms
* @author	Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* @package cms
* Show the page history
*/
class ZonePageHistory extends CopixZone {
   function _createContent (& $toReturn){
      $dao = CopixDAOFactory::getInstanceOf ('CMSPage');
      $tpl = new CopixTpl ();
      $tpl->assign ('arVersions', $dao->getHistoryOf ($this->_params['id']));
      $toReturn = $tpl->fetch ('pages.history.tpl');
      return true;
   }
}
?>