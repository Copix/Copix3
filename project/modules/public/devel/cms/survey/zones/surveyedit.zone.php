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
* ZoneSurveyEdit
*/
class ZoneSurveyEdit extends CopixZone {
	function _createContent (&$toReturn) {
		$tpl = & new CopixTpl ();

		$tpl->assign ('showErrors',$this->_params['e']);
		//dao error or something else
		if (isset($this->_params['toEdit']->errors)) {
			$tpl->assign ('errors' ,$this->_params['toEdit']->errors);
		}else{
            $dao = & CopixDAOFactory::getInstanceOf ('Survey');
			$toCheck = clone($this->_params['toEdit']);
			$toCheck->option_svy = serialize($this->_params['toEdit']);
			$tpl->assign ('errors' ,$dao->check ($toCheck));
		}
//		$this->_params['toEdit']->option_svy=unserialize($this->_params['toEdit']->option_svy);
		//var_dump($this->_params['toEdit']);
		//exit;
		$tpl->assign ('toEdit',$this->_params['toEdit']);

		$toReturn = $tpl->fetch ('survey.edit.tpl');
		return true;
	}
}
?>