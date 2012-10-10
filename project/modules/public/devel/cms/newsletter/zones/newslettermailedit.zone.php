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
* ZoneNewsLetterMailEdit
*/

class ZoneNewsletterMailEdit extends CopixZone {
	function _createContent (&$toReturn) {
		$tpl = & new CopixTpl ();

		$dao = & CopixDAOFactory::getInstanceOf ('NewsletterGroups');

		$tpl->assign ('toEdit'    , $this->_params['toEdit']);
		if ($this->_params['e']) {
			$tpl->assign ('errors' ,$this->_params['toEdit']->check ());
		}
		$tpl->assign ('showErrors', $this->_params['e']);
		$tpl->assign ('groups'    , $this->_getGroups ());

		$toReturn = $tpl->fetch ('newslettermail.edit.tpl');
		return true;
	}

	function _getGroups () {
		$dao    = & CopixDAOFactory::getInstanceOf ('NewsletterGroups');
		$groups = $dao->findAll ();
		foreach ($groups as $key=>$group){
			$groups[$key]->checked = false;
			if (count($this->_params['toEdit']->id_nlg)) {
				foreach ($this->_params['toEdit']->id_nlg as $id){
					if ($group->id_nlg == $id) {
						$groups[$key]->checked = true;
					}
				}
			}
		}
		return $groups;
	}
}
?>