<?php
/**
* @package	 cms
* @subpackage cms_portlet_picture
* @author	Bertrand Yan
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
 * @ignore
 */
CopixClassesFactory::fileInclude ('cms|cmsportlettools');
PortletFactory::includePortlet ('picture');
/**
 * @package	 cms
 * @subpackage cms_portlet_picture
* Page concernant la manipulation des portlets
*/
class ActionGroupPicturePortlet extends CopixActionGroup {
	/**
    * accès à la page de selection d'une photo
    */
	function getSelectPicture (){
		return CopixActionGroup::process ('pictures|Browser::getBrowser',
		array ('select'=>CopixUrl::get ('cms_portlet_picture||edit'),
		'back'=>CopixUrl::get ('cms_portlet_picture||edit')));
	}

	/**
    * Page de modification de la portlet.
    */
	function getEdit (){
		//Vérification des données à éditer.
		if (! ($toEdit = CMSPortletTools::getSessionPortlet ())){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get('cms_portlet_picture.unable.to.get'),
			'back'=>CopixUrl::get ('cms|admin|edit')));
		}

		//récup° de la photo
		if ( isset($this->vars['id']) ){
			$toEdit->id_pict = $this->vars['id'];
			CMSPortletTools::setSessionPortlet ($toEdit);
			session_unregister('MODULE_PICTURES_SELECT');
		}

		//appel de la zone dédiée.
		$tpl = & new CopixTpl ();
		$tpl->assign ('MAIN', CopixZone::process ('EditPicture', array ('toEdit'=>$toEdit)));
		return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
	}

	/**
    * Validation définitive de la portlet.
    */
	function doValid (){
		$this->_validFromPost ();
		if (! ($toEdit = CMSPortletTools::getSessionPortlet ())){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get('cms_portlet_picture.unable.to.get.edtied'),
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
		$toCheck = array ('width','height');
		//parcours des éléments à vérifier.
		foreach ($toCheck as $varToCheck){
			if (isset ($this->vars[$varToCheck])){
				$data->$varToCheck = $this->vars[$varToCheck];
			}
		}
		$data->force = (isset ($this->vars['force'])) ? 0 : 1;
		CMSPortletTools::setSessionPortlet ($data);
	}
}
?>