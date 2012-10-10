<?php
/**
* @package	cms
* @subpackage cms_portlet_links
* @author	Bertrand Yan, Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
CopixClassesFactory::fileInclude ('cms|cmsportlettools');
PortletFactory::includePortlet ('links');

/**
* @package	cms
* @subpackage cms_portlet_links
* Page concernant la manipulation des portlets
*/
class ActionGroupLinksPortlet extends CopixActionGroup {
    /**
    * Page de modification de la portlet.
    */
    function getEdit () {
        //Vérification des données à éditer.
        if (! ($toEdit = CMSPortletTools::getSessionPortlet ())){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get('links.error.unable.to.get'),
            'back'=>CopixUrl::get ('cms|admin|edit')));
        }

        if (!isset ($this->vars['kind'])){
            $this->vars['kind'] = 0;
        }

        //appel de la zone dédiée.
        $tpl = new CopixTpl ();
        $tpl->assign ('MAIN', CopixZone::process ('EditLinks', array ('toEdit'=>$toEdit,
        'kind'=>$this->vars['kind'])));
        $tpl->assign('TITLE_PAGE',CopixI18N::get('links.title.edit.group'));
        return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
    }

    /**
    * Validation définitive de la portlet.
    */
    function doValid (){
        $this->_validFromPost ();
        if (! ($toEdit = CMSPortletTools::getSessionPortlet ())){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get('cms|portlet.error.unableToGet'),
            'back'=>CopixUrl::get ('cms|admin|edit')));
        }
        return new CopixActionReturn (CopixactionReturn::REDIRECT,
        CopixUrl::get('cms|admin|validPortlet'));
    }

    /**
    * validation temporaire, reste sur la page d'édition.
    */
    function doValidEdit (){
        $this->_validFromPost ();
        if (CopixRequest::get ('next') == 'addLink') {
            $this->doAddLink ();
            return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get('cms_portlet_links||edit', array('kind'=>$this->vars['kind'])));
        }elseif (CopixRequest::get ('next') == 'selectPage') {
            return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get('cms_portlet_links||selectPage'));
        }else{
            return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get('cms_portlet_links||edit', array('kind'=>$this->vars['kind'])));
        }
    }

    /**
    * ajout d'un lien dans la portlet/
    */
    function doAddLink (){
        $toEdit = CMSPortletTools::getSessionPortlet ();
        $link   = new LinkForPortlet ($this->vars['linkName'], $this->vars['linkDestination']);
        $toEdit->addLink ($link);
        CMSPortletTools::setSessionPortlet ($toEdit);
    }

    /**
    * ajout d'un lien dans la portlet/
    */
    function doRemoveLink (){
        $toEdit = CMSPortletTools::getSessionPortlet ();
        $toEdit->removeLink ($this->vars['linkId']);
        CMSPortletTools::setSessionPortlet ($toEdit);

        return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get('cms_portlet_links||edit'));
    }

    /**
    * set a page from the cms
    */
    function doSetPage () {
        if (!isset ($this->vars['id'])){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get('error|error.specifyid'),
            'back'=>CopixUrl::get ('cms|admin|edit')));
        }
        $toEdit = CMSPortletTools::getSessionPortlet ();
        $toEdit->linkDestination = CopixUrl::get ('cms||get', array ('id'=>$this->vars['id']), true);
        CMSPortletTools::setSessionPortlet ($toEdit);
        return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get('cms_portlet_links||edit'));
    }

    /**

    */
    function _validFromPost (){
        $data = CMSPortletTools::getSessionPortlet ();

        //définition des éléments a vérifier
        $toCheck = array ('title', 'linkName');

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

    /**
    * moves a document up
    */
    function doMoveUp (){
        if (isset ($this->vars['id'])) {
            $document = CMSPortletTools::getSessionPortlet ();
            $this->_validFromPost ($document);
            $document->moveUp ($this->vars['id']);
            CMSPortletTools::setSessionPortlet ($document);
        }
        return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get('cms_portlet_links||edit'));
    }

    /**
    * moves a document down
    */
    function doMoveDown (){
        if (isset ($this->vars['id'])) {
            $document = CMSPortletTools::getSessionPortlet ();
            $this->_validFromPost ($document);
            $document->moveDown ($this->vars['id']);
            CMSPortletTools::setSessionPortlet ($document);
        }
        return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get('cms_portlet_links||edit'));
    }
}
?>