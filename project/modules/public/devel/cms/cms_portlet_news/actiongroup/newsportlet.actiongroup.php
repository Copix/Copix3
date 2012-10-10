<?php
/**
* @package	cms
* @subpackage cms_portlet_news
* @author	Bertrand Yan, Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */

CopixClassesFactory::fileInclude ('cms|CmsPortletTools');
PortletFactory::includePortlet ('news');

/**
 * @package cms
 * @subpackage cms_portlet_news
 */
class ActionGroupNewsPortlet extends CopixActionGroup {
    /**
     * 
    * Edit form for the portlet
    */
    function getEdit (){
        //Vérification des données à éditer.
        if (! ($toEdit = CMSPortletTools::getSessionPortlet ())){
            return CopixActionGroup::process ('genericTools|Messages::getError',
                array ('message'=>CopixI18N::get('news.unable.to.get.news'),
                'back'=>CopixUrl::get ('cms|admin|edit')));
        }

        //récup° de la page
        if ( isset($this->vars['id']) ){
            $toEdit->urldetail = $this->vars['id'] ;
            CMSPortletTools::setSessionPortlet ($toEdit) ;
        }

        //récup° de la rubrique
        if ( isset($this->vars['id_head']) ){
            $toEdit->id_head = $this->vars['id_head'] ;
            CMSPortletTools::setSessionPortlet ($toEdit) ;
        }

        if (!isset ($this->vars['kind'])){
            $this->vars['kind'] = 0;
        }

        //appel de la zone dédiée.
        $tpl = new CopixTpl ();
        $tpl->assign ('MAIN', CopixZone::process ('EditNews', array ('toEdit'=>$toEdit,
                              'kind'=>$this->vars['kind'])));
        $tpl->assign('TITLE_PAGE', CopixI18N::get ('cms_portlet_news|news.title.edit'));

        return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
    }

    /**
    * Validation définitive de la portlet.
    */
    function doValid (){
        $this->_validFromPost ();
        if (! ($toEdit = CMSPortletTools::getSessionPortlet ())){
            return CopixActionGroup::process ('genericTools|Messages::getError',
                array ('message'=>CopixI18N::get('news.unable.to.get.edited'),
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
        return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get('cms_portlet_news||edit', 
              array('kind'=>$this->vars['kind'])));
    }

    /**
    * Selects a page
    */
    function doSelectPage (){
        //Vérification des données à éditer.
        if (! ($toEdit = CMSPortletTools::getSessionPortlet ())){
        return CopixActionGroup::process ('genericTools|Messages::getError',
                array ('message'=>CopixI18N::get('news.unable.to.get.informations'),
                'back'=>CopixUrl::get ('cms|admin|edit')));
        }

        // attribution des variables du formulaire à $toEdit
        $this->_validFromPost( $toEdit );

        $tpl = new CopixTpl ();
        $tpl->assign('TITLE_PAGE', CopixI18N::get ('cms_portlet_news|news.title.selectPage'));
        $tpl->assign ('MAIN', CopixZone::process ('cms|SelectPage', 
                                                    array ('onlyLastVersion'=>1, 
                                                           'draft'=>1,
                                                           'select'=>CopixUrl::get ('cms_portlet_news||edit'), 
                                                           'back'=>CopixUrl::get ('cms_portlet_news||edit')) ));
        return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
    }

    /**
    * Select the heading
    */
    function doSelectHeading (){
        //Vérification des données à éditer.
        if (! ($toEdit = CMSPortletTools::getSessionPortlet ())){
                return CopixActionGroup::process ('genericTools|Messages::getError',
                array ('message'=>CopixI18N::get('news.unable.to.get.informations'),
                'back'=>CopixUrl::get ('cms|admin|edit')));
        }

        // attribution des variables du formulaire à $toEdit
        $this->_validFromPost( $toEdit );

        $tpl = new CopixTpl ();
        $tpl->assign('TITLE_PAGE',CopixI18N::get('news.selectHeading'));
        $tpl->assign ('MAIN', CopixZone::process ('copixheadings|SelectHeading', array ('select'=>CopixUrl::get ('cms_portlet_news||edit'), 'back'=>CopixUrl::get ('cms_portlet_news||edit'))));
        return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
    }

    /**
    * Applying changes in the fields

    */
    function _validFromPost (){
        $data = CMSPortletTools::getSessionPortlet ();

        //définition des éléments a vérifier
        $toCheck = array ('subject', 'numToShow', 'urldetail','id_head', 'fromCountLastNews');

        //parcours des éléments à vérifier.
        foreach ($toCheck as $varToCheck){
            if (isset ($this->vars[$varToCheck])){
                $data->$varToCheck = CopixRequest::get ($varToCheck, null, true);
            }
        }
		if (array_key_exists ('template', $this->vars)){
   			$data->setTemplate ($this->vars['template']);
		}
        CMSPortletTools::setSessionPortlet ($data);
    }
}
?>
