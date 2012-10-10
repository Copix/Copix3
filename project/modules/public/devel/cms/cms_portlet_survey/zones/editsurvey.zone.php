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
* @package	 cms
* @subpackage cms_portlet_survey
* ZoneEditSurvey
*/
class ZoneEditSurvey extends CopixZone {
	/**
	* Attends un objet de type textpage en paramètre.
	*/
	function _createContent (& $toReturn){
		//Type d'édition: 
		$kind = $this->getParam ('kind', 'general') == 'preview' ? 'preview' : 'general';

		$tpl = & new CopixTpl ();
		$tpl->assign ('objSurvey', $this->_params['toEdit']);

		$dao       = & CopixDAOFactory::getInstanceOf ('copixheadings|copixheadings');
		$daoSurvey = & CopixDAOFactory::getInstanceOf ('survey|survey');
		$sp        = & CopixDAOFactory::createSearchParams ();

		if ($this->_params['toEdit']->id_head > 0) {
			$heading = $dao->get ($this->_params['toEdit']->id_head);
			$heading = $heading->caption_head;
			$sp->addCondition ('id_head','=',$this->_params['toEdit']->id_head);
		}else{
			$this->_params['toEdit'] = 'root';
			$heading = CopixI18N::get ('copixheadings|headings.message.root');
			$sp->addCondition ('id_head','=',null);
		}

		//save id_head on the choosen headin in Session
		//in order to allow user to go to the heading survey administration page
		if (isset($this->_params['toEdit']->id_head)) $this->_setSessionHeading ($this->_params['toEdit']->id_head);

		$tpl->assign ('arSurvey'      , $daoSurvey->findBy($sp));
		$tpl->assign ('headingName'   , $heading);
		$tpl->assign ('possibleKinds', CopixTpl::find ('cms_portlet_survey', '.portlet.?tpl'));
		if (isset($this->_params['toEdit']->urllist)) $tpl->assign ('pageName'      , $this->_getNamePage($this->_params['toEdit']->urllist));

		//appel du template.
		$toReturn = $tpl->fetch ('cms_portlet_survey|survey.edit.tpl');
		return true;
	}

	/**
	* Récupération du nom de la page.
	* TODO: Création d'un service capable de faire ce genre d'opérations
	*/
	function _getNamePage ($id) {
		$dao = & CopixDAOFactory::getInstanceOf ('cms|cmspage');
		$sp  = & CopixDAOFactory::createSearchParams();
		$sp->addCondition ('publicid_cmsp', '=', $id);
		$sp->orderBy (array('version_cmsp', 'DESC'));
		$data =  $dao->findBy( $sp ) ;
		return (isset($data[0])) ? $data[0]->title_cmsp : null;
	}

	/**
    * sets the current heading.

    */
	function _setSessionHeading ($toSet){
		$_SESSION['MODULE_SURVEY_CURRENT_HEADING'] = $toSet !== null ? serialize($toSet) : null;
	}
}
?>