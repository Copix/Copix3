<?php
/**
* @package	cms
* @subpackage cms_portlet_newsdetail
* @author	Bertrand Yan, Croes Gérald
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
CopixClassesFactory::fileInclude ('cms|cmsportlettools');
PortletFactory::includePortlet ('newsdetail');

/**
 * @package	cms
 * @subpackage cms_portlet_newsdetail
 * ActionGroupNewsDetailPortlet
 */
class ActionGroupNewsDetailPortlet extends CopixActionGroup {
    /**
    * Page de modification de la portlet.
    */
    function getEdit (){
        //Vérification des données à éditer.
        if (! ($toEdit = CMSPortletTools::getSessionPortlet ())){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get('cms_portlet_news.unable.to.get.informations'),
            'back'=>CopixUrl::get ('cms|admin|edit')));
        }

        //récup° des images ou pages
        if ( isset($this->vars['id']) ){
            switch( strtolower($this->vars['typeId']) ){
                case 'urlback':
                   $toEdit->detail_urlback = $this->vars['id'] ;
                break;
                default:
                break;
            }
            //stockage session
            CMSPortletTools::setSessionPortlet ($toEdit) ;
        }

        //affichage edit par defaut
        if (!isset ($this->vars['kind'])){
            $this->vars['kind'] = 0;
        }

        $tpl = & new CopixTpl ();
        $tpl->assign('TITLE_PAGE',CopixI18N::get('cms_portlet_newsdetail.title.edition'));
        $tpl->assign ('MAIN', CopixZone::process ('cms_portlet_newsdetail|edit', 
                                array( 'toEdit'=>$toEdit, 'kind'=>$this->vars['kind'])));
        return new CopixActionReturn (CopixActionReturn::DISPLAY, $tpl);

    }

    /**
    * Validation définitive de la portlet.
    */
    function doValid (){
        // attribution des variables du formulaire à $toEdit
        $this->_validPost();

        if (! ($toEdit = CMSPortletTools::getSessionPortlet ())){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get('cms_portlet_news.unable.to.get.portlet'),
            'back'=>CopixUrl::get ('cms|admin|edit')));
        }

        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get('cms|admin|validPortlet'));
    }

    /**
    * validation temporaire, reste sur la page d'édition.
    */
    function doValidEdit (){
        // attribution des variables du formulaire à $toEdit
        $this->_validPost();

        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get('cms_portlet_newsdetail||edit', array('kind'=>$this->vars['kind'])));
    }

    /**
    * Shows the select page tree
    */
    function doSelectPage (){
        //Vérification des données à éditer.
        if (! ($toEdit = CMSPortletTools::getSessionPortlet ())){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get('cms_portlet_news.unable.to.get.informations'),
            'back'=>CopixUrl::get ('cms|admin|edit')));
        }

        // attribution des variables du formulaire à $toEdit
        $this->_validPost( $toEdit );

        $tpl = & new CopixTpl ();
        $tpl->assign('TITLE_PAGE',CopixI18N::get('cms_portlet_newsdetail.title.edition'));
        $tpl->assign ('MAIN', CopixZone::process ('cms|SelectPage', 
                                       array ('TITLE_PAGE'=>'Choix de la page de retour', 
                                       'onlyLastVersion'=>1, 
                                       'draft'=>1,
                                       'select'=>CopixUrl::get ('cms_portlet_newsdetail||edit', array ('typeId'=>'urlBack')), 
                                       'back'=>CopixUrl::get ('cms_portlet_newsdetail||edit'))));
        return new CopixActionReturn (CopixActionReturn::DISPLAY, $tpl);
    }

    function _validPost (){
        $toUpdate = CMSPortletTools::getSessionPortlet ();
        $toCheck = array ('detail_urlback');
        foreach ( $toCheck as $elem ){
            if ( isset ($this->vars[$elem]) )
            $toUpdate->$elem = $this->vars[$elem];
        }

         if (array_key_exists ('template', $this->vars)){
         		$toUpdate->setTemplate ($this->vars['template']);
         }

        CMSPortletTools::setSessionPortlet ( $toUpdate );
    }
}
?>
