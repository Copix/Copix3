<?php

/**
 * @package cms
 * @subpackage cms_portlet_searchengine
 */
 
 /**
  * @ignore
  */
CopixClassesFactory::fileInclude ('cms|cmsportlettools');
PortletFactory::includePortlet ('searchengine');
/**
* @package cms
* @subpackage cms_portlet_searchengine
* Updates the portlet
*/
class ActionGroupSearchEngine extends CopixActionGroup {
	/**
    * Form to edit the portlet
    */
	function getEdit (){
		//Vérification des données à éditer.
		if (! ($toEdit = CMSPortletTools::getSessionPortlet ())){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get('searchengine.unable.to.get'),
			'back'=>CopixUrl::get('cms|admin|edit')));
		}

		if (!isset ($this->vars['kind'])){
			$this->vars['kind'] = 0;
		}

		//appel de la zone dédiée.
		$tpl = & new CopixTpl ();
		$tpl->assign ('MAIN', CopixZone::process ('EditSearch', array ('toEdit'=>$toEdit,
		'kind'=>$this->vars['kind'])));
		$tpl->assign('TITLE_PAGE',CopixI18N::get ('cms_portlet_searchengine|searchengine.title'));
		return new CopixActionReturn (CopixActionReturn::DISPLAY, $tpl);
	}

	/**
    * Validation définitive de la portlet.
    */
	function doValid (){
		$this->_validFromPost ();
		if (! ($toEdit = CMSPortletTools::getSessionPortlet ())){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get('searchengine.unable.to.get.edited'),
			'back'=>CopixUrl::get('cms|admin|edit')));
		}
		return new CopixActionReturn (CopixActionReturn::REDIRECT,
		CopixUrl::get('cms|admin|validPortlet'));
	}

	/**
    * validation temporaire, reste sur la page d'édition.
    */
	function doValidEdit (){
		if (!isset ($this->vars['kind'])){
			$this->vars['kind'] = 0;
		}
		$this->_validFromPost ();
		return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get('cms_portlet_searchengine||edit', array('kind'=>$this->vars['kind'])));
	}

	/**
    * Select a result page
    */
	function doSelectPage() {
		//validation de l'existent
		$this->_validFromPost ();

		//Vérification des données à éditer.
		if (! ($toEdit = CMSPortletTools::getSessionPortlet ())){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get('searchengine.unable.to.get.informations'),
			'back'=>CopixUrl::get('cms|admin|edit')));
		}

		$tpl = & new CopixTpl ();
		$tpl->assign('TITLE_PAGE','Edition des services');
		$tpl->assign ('MAIN', CopixZone::process ('cms|SelectPage', array ('TITLE_PAGE'=>'Choix de la page d\'affichage des résultats', 'onlyLastVersion'=>1, 'select'=>CopixUrl::get('cms_portlet_searchengine||validedit'), 'back'=>CopixUrl::get('cms_portlet_searchengine||edit')) ));
		return new CopixActionReturn (CopixActionReturn::DISPLAY, $tpl);
	}

	/**

    */
	function _validFromPost (){
		$data = CMSPortletTools::getSessionPortlet ();

		//définition des éléments a vérifier
		$toCheck = array ('presentation_text', 'template', 'idPortletResultPage', 'title', 'size');

		//parcours des éléments à vérifier.
		foreach ($toCheck as $varToCheck){
			if (isset ($this->vars[$varToCheck])){
				$data->$varToCheck = $this->vars[$varToCheck];
			}
		}

		if (array_key_exists ('template', $this->vars)){
			$data->setTemplate ($this->vars['template']);
		}

		//recuperat° de la page resultat
		if (isset ($this->vars['id']) ){
			$data->idPortletResultPage = $this->vars['id'] ;
		}
		CMSPortletTools::setSessionPortlet ($data);
	}
}
?>