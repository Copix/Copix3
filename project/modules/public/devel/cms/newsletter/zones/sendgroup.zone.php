<?php
/**
* @package	cms
* @subpackage newsletter
* @author	Bertrand Yan
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package	cms
* @subpackage newsletter
* ZoneSendGroup
*/
class ZoneSendGroup extends CopixZone {
	function _createContent (&$toReturn) {
      $tpl = & new CopixTpl ();

      Copixcontext::push('cms');
      $tpl->assign ('newsletter', ServicesCMSPage::getOnline($this->_params['id']));
      Copixcontext::pop();
      
      $daoGroup      = & CopixDAOFactory::getInstanceOf ('NewsletterGroups');
      $daoCopixGroup = & CopixDAOFactory::getInstanceOf ('copix:CopixGroup');
      $tpl->assign ('groups'     , $daoGroup->findAll ());
      $tpl->assign ('copixGroups', $daoCopixGroup->findAll ());
      $tpl->assign ('error' , $this->_params['error']);
      $tpl->assign ('online', $this->_params['online']);

      $toReturn = $tpl->fetch ('send.group.tpl');
      return true;
	}
}
?>
