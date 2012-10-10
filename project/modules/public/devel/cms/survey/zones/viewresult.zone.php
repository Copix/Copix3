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
 * @ignore
 */
CopixClassesFactory::fileInclude ('survey|surveyoption');

/**
* @package	cms
* @subpackage survey
* get result for a given survey
*/
class ZoneViewResult extends CopixZone {
	function _createContent (&$toReturn){
		$dao    = CopixDAOFactory::getInstanceOf ('survey|survey');
		$survey = $dao->get ($this->_params['id_svy']);
		$survey->option_svy = unserialize($survey->option_svy);

		$tpl = new CopixTpl ();
		$tpl->assign ('survey', $survey);

		$toReturn = $tpl->fetch ('surveys.results.tpl');
		return true;
	}
}
?>