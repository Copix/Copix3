<?php
/**
 * @package cms
* @subpackage survey
 * @author Bertrand Yan
 * @copyright 2001-2005 CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */
/**
 * @package	cms
 * @subpackage survey
 * shows what the current user can / should do in the given CopixHeading.
 */
class ZoneSurveyAdminHeading extends CopixZone {
	/**
    * @param int $this ->_params['id'] the CopixHeading id.
    */
	function _createContent (&$toReturn) {
		$dao = CopixDAOFactory::getInstanceOf ('Survey');
		$sp  = CopixDAOFactory::createSearchParams ();
		$sp->addCondition ('id_head', '=',$this->_params['id_head']);
		$arSurvey = $dao->findBy($sp);

		$servicesHeading = &CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');

		$tpl = & new CopixTpl ();
		$tpl->assign ('id_head', $this->_params['id_head']);
		$tpl->assign ('writeEnabled', $nonEmpty = CopixUserProfile::valueOfIn ('survey', $servicesHeading->getPath ($this->_params['id_head'])) >= PROFILE_CCV_WRITE);
		$tpl->assign ('arSurvey' , $arSurvey);
		$toReturn = $nonEmpty === false ? '' : $tpl->fetch ('survey.adminheading.tpl');

		return true;
	}
}
?>