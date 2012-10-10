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
require_once (COPIX_UTILS_PATH.'CopixPager.class.php');
/**
* @package	cms
* @subpackage survey
* get all the surveys online in this heading
*/
class ZoneOnlineSurvey extends CopixZone {
	function _createContent (&$toReturn){
		$tpl = & new CopixTpl ();

		$dao = CopixDAOFactory::getInstanceOf ('Survey');
		$sp  = CopixDAOFactory::createSearchParams ();
		$sp->addCondition ('id_head', '=',$this->_params['id_head']);
		$arSurveys = $dao->findBy($sp);
		if (count($arSurveys)>0) {
			$params = Array(
			'perPage'    => 10,
			'delta'      => 5,
			'recordSet'  => $arSurveys,
			);
			$Pager = CopixPager::Load($params);
			$tpl->assign ('pager'     , $Pager->GetMultipage());
			$tpl->assign ('arSurveys' , $Pager->data);
		}

		if ($this->_params['manage']) {
			$toReturn = $tpl->fetch ('surveys.manage.tpl');
		}else{
			$toReturn = $tpl->fetch ('surveys.online.tpl');
		}
		return true;
	}
}
?>