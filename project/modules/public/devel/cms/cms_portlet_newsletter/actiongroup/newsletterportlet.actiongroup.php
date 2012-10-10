<?php
/**
* @package	cms
* @subpackage cms_portlet_newsletter
* @author	Croes Gérald, Cédric Vallat, Yan Bertrand
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
CopixClassesFactory::fileInclude ('cms|CmsPortletTools');
PortletFactory::includePortlet ('newsletter');
/**
* @package	cms
* @subpackage cms_portlet_newsletter
* Gestion de la portlet newsletter
*/
class ActionGroupNewsletterPortlet extends CopixActionGroup {
	/**
    * Page de modification de la portlet.
    */
	function getEdit (){
		//Vérification des données à éditer.
		if (! ($toEdit = CMSPortletTools::getSessionPortlet ())){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get('newsletter.unable.to.get'),
			'back'=>CopixUrl::get('cms|admin|edit')));
		}

		if (!isset ($this->vars['kind'])){
			$this->vars['kind'] = 0;
		}

		//appel de la zone dédiée.
		$tpl = & new CopixTpl ();
		$tpl->assign ('MAIN', CopixZone::process ('EditNewsletterPortlet', array ('toEdit'=>$toEdit,
		'kind'=>$this->vars['kind'])));
		$tpl->assign('TITLE_PAGE',CopixI18N::get ('newsletter.title'));
		return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
	}

	/**
    * Validation définitive de la portlet.
    */
	function doValid (){
		$this->_validFromPost ();
		if (! ($toEdit = CMSPortletTools::getSessionPortlet ())){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get('newsletter.unable.to.get.edited'),
			'back'=>CopixUrl::get('cms|portlet|edit')));
		}
		return new CopixActionReturn (CopixactionReturn::REDIRECT,CopixUrl::get('cms|admin|validPortlet'));
	}

	/**
    * validation temporaire, reste sur la page d'édition.
    */
	function doValidEdit (){
		$this->_validFromPost ();
		return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get('cms_portlet_newsletter||edit', array('kind'=>$this->vars['kind'])));
	}

	/**
	* Validation des données reçues du formulaire d'inscription

    */
	function _validFromPost (){
		$data = CMSPortletTools::getSessionPortlet ();
		//définition des éléments a vérifier
		$toCheck = array ('title', 'id_group');
		//parcours des éléments à vérifier.
		foreach ($toCheck as $varToCheck){
			if (isset ($this->vars[$varToCheck])){
				$data->$varToCheck = $this->vars[$varToCheck];
			}
		}
		if (array_key_exists ('template', $this->vars)){
			$data->setTemplate ($this->vars['template']);
		}
		CMSPortletTools::setSessionPortlet ($data);
	}
}
?>