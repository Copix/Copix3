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
CopixClassesFactory::fileInclude ('cms|cmsportlettools');
PortletFactory::includePortlet ('survey');


/**
* @package	 cms
* @subpackage cms_portlet_survey
* Page concernant la manipulation de pages composed
*/
class ActionGroupSurveyPortlet extends CopixActionGroup {
	/**
    * Gets the edit screen.
    */
	function getEdit (){
		//Vérification des données à éditer.
		if (! ($toEdit = CMSPortletTools::getSessionPortlet ())){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get('survey.unable.to.get'),
			'back'=>CopixUrl::get ('cms|admin|edit')));
		}

		//récup° de la rubrique
		if ( isset($this->vars['id_head']) ){
			$toEdit->id_head = $this->vars['id_head'] ;
			CMSPortletTools::setSessionPortlet ($toEdit) ;
		}

		//récup° de la page
		if ( isset($this->vars['id']) ){
			$toEdit->urllist = $this->vars['id'] ;
			CMSPortletTools::setSessionPortlet ($toEdit) ;
		}
		//appel de la zone dédiée.
		$tpl = & new CopixTpl ();
		$tpl->assign ('MAIN', CopixZone::process ('EditSurvey', array ('toEdit'=>$toEdit)));
		return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
	}

	/**
    * Validation définitive de la portlet.
    */
	function doValid (){
		$this->_validFromPost ();

		if (! ($toEdit = CMSPortletTools::getSessionPortlet ())){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get('survey.unable.to.get.edited'),
			'back'=>CopixUrl::get ('cms|admin|edit')));
		}
		return new CopixActionReturn (CopixactionReturn::REDIRECT,
		CopixUrl::get('cms|admin|validPortlet'));
	}

	/**

    */
	function _validFromPost (){
		$data = CMSPortletTools::getSessionPortlet ();
		//définition des éléments a vérifier
		$toCheck = array ('id_svy', 'template');

		//parcours des éléments à vérifier.
		foreach ($toCheck as $varToCheck){
			if (isset ($this->vars[$varToCheck])){
				$data->$varToCheck = $this->vars[$varToCheck];
			}
		}
		CMSPortletTools::setSessionPortlet ($data);
	}

	/**
    * Selects the heading
    */
	function doSelectHeading (){
		//Vérification des données à éditer.
		if (! ($toEdit = CMSPortletTools::getSessionPortlet ())){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get('survey.unable.to.get.informations'),
			'back'=>CopixUrl::get ('cms|admin|edit')));
		}

		// attribution des variables du formulaire à $toEdit
		$this->_validFromPost( $toEdit );

		$tpl = & new CopixTpl ();
		$tpl->assign('TITLE_PAGE',CopixI18N::get('survey.titlePage.selectHeading'));
		$tpl->assign ('MAIN', CopixZone::process ('copixheadings|SelectHeading', array ('select'=>copixurl::get('cms_portlet_survey||edit'), 'back'=>CopixUrl::get('cms_portlet_survey||action=edit')) ));
		return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
	}

	/**
    * Gets the select page screen
    */
	function doSelectPage (){
		//Vérification des données à éditer.
		if (! ($toEdit = CMSPortletTools::getSessionPortlet ())){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get('survey.unable.to.get.informations'),
			'back'=>CopixUrl::get ('cms|admin|edit')));
		}

		// attribution des variables du formulaire à $toEdit
		$this->_validFromPost( $toEdit );


		$tpl = & new CopixTpl ();
		$tpl->assign ('MAIN', CopixZone::process ('cms|SelectPage',
		array ('TITLE_PAGE'=>CopixI18N::get('survey.messages.listPage'),
		'onlyLastVersion'=>1,
		'select'=>CopixUrl::get ('cms_portlet_survey||edit'),
		'back'=>CopixUrl::get ('cms_portlet_survey||edit'))));
		return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
	}
}
?>