<?php

/**
* @package	cms
* @subpackage newsletter
* @author	???
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package	cms
* @subpackage newsletter
* ZoneUnsubscribe
*/
class ZoneUnsubscribe extends CopixZone {
	function _createContent (&$toReturn) {
		$tpl = & new CopixTpl ();

		$tpl->assign ('mail'  , $this->_params['mail']->mail_nlm);
		$tpl->assign ('groups', $this->_getGroups());

		$toReturn = $tpl->fetch ('unsubscribe.tpl');
		return true;
	}

	function _getGroups () {
		$daoLink  = & CopixDAOFactory::getInstanceOf ('NewsletterMailLinkGroups');
		$daoGroup = & CopixDAOFactory::getInstanceOf ('NewsletterGroups');
		$sp       = CopixDAOFactory::createSearchParams ();
		$sp->addCondition ('mail_nlm','=',$this->_params['mail']->mail_nlm);
		$tabIdGroup = $daoLink->findBy($sp);
		$tab = array ();
		foreach ((array)$tabIdGroup as $object){
			$tab[] = $object->id_nlg;
		}
		$sp = CopixDAOFactory::createSearchParams ();
		$sp->addCondition ('id_nlg','=',$tab);

		return $daoGroup->findBy($sp);
	}
}
?>