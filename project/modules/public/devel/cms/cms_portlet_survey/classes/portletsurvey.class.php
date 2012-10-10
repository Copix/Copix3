<?php
/**
* @package	 cms
* @subpackage cms_portlet_survey
* @author	Bertrand Yan
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
CopixClassesFactory::fileInclude ('cms|portlet');
CopixClassesFactory::fileInclude ('survey|SurveyOption');
//require_once (COPIX_UTILS_PATH.'CopixUtils.lib.php');


/**
 * @package cms
 * @subpackage cms_portlet_survey
 * PortletSurvey
 */
class PortletSurvey extends Portlet {
	var $id_svy;//survey identifier
	var $template;//the kind of the article, id of the template.
	var $id_head;//the heading identifier of the news
	var $urllist;//url of survey list page

	/**
    * gets the parsed article.
    */
	function getParsed ($context) {
		$tpl = & new CopixTpl ();

		$dao = & CopixDAOFactory::getInstanceOf ('survey|survey');
		$survey = $dao->get ($this->id_svy);
		//if no survey return blanck
		if ($survey == null) {
			return '';
		}
		$survey->option_svy = unserialize($survey->option_svy);

		if (strlen($this->urllist) > 0) {
			$tpl->assign ('urllist', CopixUrl::get ('cms||get', array('id'=>$this->urllist)));
		}
		$tpl->assign ('survey' , $survey);
		$tpl->assign ('url'    , CopixUrl::getCurrentUrl());
		$result = false;
		//test if users should be authentified to vote
		if ($survey->authuser_svy == 1) {
			$plugAuth = & CopixController::instance ()->getPlugin ('auth|auth');
			$user = & $plugAuth->getUser();
			if (!$user->isConnected ()){
				$result = true;
			}
		}

		//if user ask for result
		if (isset($this->params['forceResult'])) {
			$result = true;
		}

		if (!(($this->_getCookie ($this->id_svy) === null) && ($this->_getSesssionCookie ($this->id_svy) === null))) {
			$result = true;
		}

		$tpl->assign ('result' , $result);
        return $tpl->fetch ($this->templateId ? $this->getTemplateSelector() : 'cms_portlet_survey|normal.survey.tpl');
	}

	/**
    * gets the survey flag (in session).
    * @param int id survey identifier

    */
	function _getSesssionCookie ($id) {
		return isset ($_SESSION['MODULE_SURVEY_VOTED_'.$id]) ? $_SESSION['MODULE_SURVEY_VOTED_'.$id] : null;
	}

	/**
    * gets the survey flag (in cookie).
    * @param int id survey identifier

    */
	function _getCookie ($id) {
		return isset ($_COOKIE['MODULE_SURVEY_VOTED_'.$id]) ? $_COOKIE['MODULE_SURVEY_VOTED_'.$id] : null;
	}

	function getGroup (){
		return 'general';
	}
	function getI18NKey (){
		return 'cms_portlet_survey|survey.portletdescription';
	}
	function getGroupI18NKey (){
		return 'cms_portlet_survey|survey.group';
	}
}
/**
* @package cms
* @subpackage cms_portlet_survey
* Pour des raisons de compatibilité, l'ancienne convention étant de nommer les portlet XXXPortlet et non PortletXXX
*/
class SurveyPortlet extends PortletSurvey {}
?>