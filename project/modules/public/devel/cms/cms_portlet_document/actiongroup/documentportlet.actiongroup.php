<?php
/**
* @package cms
* @subpackage cms_portlet_document
* @author	Bertrand Yan, Croes Gérald
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
CopixClassesFactory::fileInclude ('cms|CMSPortletTools');
PortletFactory::includePortlet ('document');

/**
 * @package cms
 * @subpackage	cms_portlet_document
* Page concernant la manipulation de pages composed
*/
class ActionGroupDocumentPortlet extends CopixActionGroup {
    function getEdit (){
        //Vérification des données à éditer.
        if (! ($toEdit = CMSPortletTools::getSessionPortlet ())){
            return CopixActionGroup::process ('genericTools|Messages::getError', 
                      array ('message'=>CopixI18N::get('document.title.unable.to.get'), 
                      'back'=>CopixUrl::get ('cms|admin|edit')));
        }
        if ((!isset ($this->vars['kind'])) || (($this->vars['kind'] != 0) && ($this->vars['kind'] != 1))){
            $this->vars['kind'] = 0;
        }

        //appel de la zone dédiée.
        $tpl = new CopixTpl ();
        $tpl->assign ('MAIN', CopixZone::process ('EditDocument', array ('toEdit'=>$toEdit, 'kind'=>$this->vars['kind'])));
        return new CopixActionReturn (CopixActionReturn::DISPLAY, $tpl);
    }

    /**
    * Validation définitive de la portlet.
    */
    function doValid (){
        if (! ($toEdit = CMSPortletTools::getSessionPortlet ())){
            return CopixActionGroup::process ('genericTools|Messages::getError', 
                array ('message'=>CopixI18N::get('cms|portlet.error.unableToGet'), 
                'back'=>CopixUrl::get ('cms|admin|edit')));
        }
        $this->_validFromPost ($toEdit);
        return new CopixActionReturn (CopixActionReturn::REDIRECT,  CopixUrl::get('cms|admin|validPortlet'));
    }

    /**

    */
    function _validFromPost (& $toEdit){
        //définition des éléments a vérifier
        $toCheck = array ('subject');

        //parcours des éléments à vérifier.
        foreach ($toCheck as $varToCheck){
            if (isset ($this->vars[$varToCheck])){
                $toEdit->$varToCheck = $this->vars[$varToCheck];
            }
        }
        if (array_key_exists ('template', $this->vars)){
        	$toEdit->setTemplate ($this->vars['template']);
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
    
    
    /**
    * Delete the selected document given by the parameters
    * @param $this->vars['index'];
    */
    function doDeleteDocument () {
         if (! (isset($this->vars['index']))){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get('document.title.unable.to.get.param.index'),
            'back'=>CopixUrl::get ('cms|admin|edit')));
        }
        if (! ($toEdit = CMSPortletTools::getSessionPortlet ())){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get('document.title.unable.to.get.infos'),
            'back'=>CopixUrl::get ('cms|admin|edit')));
        }

        unset ($toEdit->arDocs[$this->vars['index']]);
        $this->_validFromPost ($toEdit);

        // attribution des variables du formulaire à $toEdit
        return new CopixActionReturn (CopixActionReturn::REDIRECT,  CopixUrl::get('cms_portlet_document||edit'));
    }
    
    /**
    * Adds a document
    * @param $this->vars['id_doc']
    */
    function doAddDocument (){
        //Vérification des données à éditer.
        if (! ($toEdit = CMSPortletTools::getSessionPortlet ())){
            return CopixActionGroup::process ('genericTools|Messages::getError', 
                      array ('message'=>CopixI18N::get('document.title.unable.to.get'), 
                      'back'=>CopixUrl::get ('cms|admin|edit')));
        }

        //récup° du nouveau doc
        if ( (isset($this->vars['id_doc'])) && (strlen($this->vars['id_doc'])>0)){
            $toEdit->arDocs[] = $this->vars['id_doc'] ;
            CMSPortletTools::setSessionPortlet ($toEdit);
        }

        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('cms_portlet_document||edit'));
    }
    
    /**
    * Moves a document up
    * @param $this->vars['id'] the document to moves up
    */
    function doMoveUp (){
        //Vérification des données à éditer.
        if (! ($toEdit = CMSPortletTools::getSessionPortlet ())){
            return CopixActionGroup::process ('genericTools|Messages::getError', 
                      array ('message'=>CopixI18N::get('document.title.unable.to.get'), 
                      'back'=>CopixUrl::get ('cms|admin|edit')));
        }
        $this->_validFromPost ($toEdit);
        $toEdit->moveUp ($this->vars['id']);
        CMSPortletTools::setSessionPortlet ($toEdit);
        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('cms_portlet_document||edit'));
    }

    /**
    * Moves a document down
    * @param $this->vars['id'] the document to moves down
    */
    function doMoveDown (){
        //Vérification des données à éditer.
        if (! ($toEdit = CMSPortletTools::getSessionPortlet ())){
            return CopixActionGroup::process ('genericTools|Messages::getError', 
                      array ('message'=>CopixI18N::get('document.title.unable.to.get'), 
                      'back'=>CopixUrl::get ('cms|admin|edit')));
        }
        $this->_validFromPost ($toEdit);
        $toEdit->moveDown ($this->vars['id']);
        CMSPortletTools::setSessionPortlet ($toEdit);
        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('cms_portlet_document||edit'));
    }
}
?>