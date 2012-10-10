<?php
/**
* @package		cms
* @subpackage	cms_portlet_flash
* @author		Croës Gérald, Salleyron Julien
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/


/**
 * @ignore
 */
CopixClassesFactory::fileInclude ('cms|CMSPortletTools');
PortletFactory::includePortlet ('flash');

/**
 * Classe facilitant les tests sur les droits pour le module flash
 * @package		cms
 * @subpackage	cms_portlet_flash 
 */
class ActionGroupDefault extends CopixActionGroup {
	/**
	 * Page de modification d'une portlet
	 */
	public function edit (){
        //Vérification des données à éditer.
        if (! ($toEdit = CMSPortletTools::getSessionPortlet ())){
            return CopixActionGroup::process ('genericTools|Messages::getError', 
                      array ('message'=>CopixI18N::get('document.title.unable.to.get'), 
                      'back'=>CopixUrl::get ('cms|admin|edit')));
        }

        $kind = in_array (($kind = CopixRequest::get ('kind', 0)), array (0, 1)) ? $kind : 0;
        
        $this->_validFromPost ($toEdit);

        //appel de la zone dédiée.
        $tpl = new CopixTpl ();
        $tpl->assign ('MAIN', CopixZone::process ('EditFlash', array ('toEdit'=>$toEdit, 'kind'=>$kind)));
        return new CopixActionReturn (CopixActionReturn::DISPLAY, $tpl);
    }

    /**
    * Validation définitive de la portlet.
    */
    function valid (){
        if (! ($toEdit = CMSPortletTools::getSessionPortlet ())){
            return CopixActionGroup::process ('genericTools|Messages::getError', 
                array ('message'=>CopixI18N::get('cms|portlet.error.unableToGet'), 
                'back'=>CopixUrl::get ('cms|admin|edit')));
        }
        $this->_validFromPost ($toEdit);
        return new CopixActionReturn (CopixActionReturn::REDIRECT,  CopixUrl::get('cms|admin|validPortlet'));
    }

    /**
     *
    */
    function _validFromPost ($toEdit){
        //définition des éléments a vérifier
        $toCheck = array ('id_flash');

        //parcours des éléments à vérifier.
        foreach ($toCheck as $varToCheck){
            if (($value = CopixRequest::get ($varToCheck)) !== null){
                $toEdit->$varToCheck = $value;
            }
        }
        CMSPortletTools::setSessionPortlet ($toEdit);
    }

    /**
    * validation temporaire, reste sur la page d'édition.
    */
    function doValidEdit (){
        if (! ($toEdit = CMSPortletTools::getSessionPortlet ())){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get('document.title.unable.to.get.infos'),
            'back'=>CopixUrl::get ('cms|admin|edit')));
        }

        $this->_validFromPost ($toEdit);

        return new CopixActionReturn (CopixActionReturn::REDIRECT,  CopixUrl::get('cms_portlet_document||edit'));
    }
    
    /**
    * Select a new document
    */
    function getSelectDocument () {
        if (! ($toEdit = CMSPortletTools::getSessionPortlet ())){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get('document.title.unable.to.get.infos'),
            'back'=>CopixUrl::get ('cms|admin|edit')));
        }

        // attribution des variables du formulaire à $toEdit
        $this->_validFromPost($toEdit);

        $tpl = new CopixTpl ();
        $tpl->assign('TITLE_PAGE',CopixI18N::get('document.titlePage.selectDocument'));
        $tpl->assign ('MAIN', CopixZone::process ('document|SelectDocument', array ('select'=>CopixUrl::get('cms_portlet_document|default|add'), 'back'=>CopixUrl::get('cms_portlet_document|default|edit')) ));
        return new CopixActionReturn (CopixActionReturn::DISPLAY, $tpl);
    }
}
?>