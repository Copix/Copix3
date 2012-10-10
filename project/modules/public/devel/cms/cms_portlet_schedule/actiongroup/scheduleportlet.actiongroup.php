<?php
/**
* @package	cms
* @subpackage cms_portlet_schedule
* @author	Bertrand Yan, Ferlet Patrice see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
CopixClassesFactory::fileInclude ('cms|cmsportlettools');
PortletFactory::includePortlet ('schedule');


/**
* @package	cms
* @subpackage cms_portlet_schedule
* Page concernant la manipulation des portlets
*/
class ActionGroupSchedulePortlet extends CopixActionGroup {
    /**
    * Page de modification de la portlet.
    */
    function getEdit (){
        //Vérification des données à éditer.
        if (! ($toEdit = CMSPortletTools::getSessionPortlet ())){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get('schedule.unable.to.get'),
            'back'=>CopixUrl::get ('cms|admin|edit')));
        }

        //récup° de la page
        if ( isset($this->vars['id']) ){
            $toEdit->id_page_subscribe = $this->vars['id'] ;
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
        $tpl = & new CopixTpl ();
        $tpl->assign ('MAIN', CopixZone::process ('EditSchedule', array ('toEdit'=>$toEdit,
        'kind'=>$this->vars['kind'])));
        $tpl->assign('TITLE_PAGE',CopixI18N::get('schedule.titlePage.edit'));
        return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
    }

    /**
    * Validation définitive de la portlet.
    */
    function doValid (){
        $this->_validFromPost ();
        if (! ($toEdit = CMSPortletTools::getSessionPortlet ())){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get('schedule.unable.to.get.edited'),
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
        return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get('cms_portlet_schedule||edit', array('kind'=>$this->vars['kind'])));
    }


    function doSelectPage (){
        //Vérification des données à éditer.
        if (! ($toEdit = CMSPortletTools::getSessionPortlet ())){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get('schedule.unable.to.get.informations'),
            'back'=>CopixUrl::get ('cms|admin|edit')));
        }

        // attribution des variables du formulaire à $toEdit
        $this->_validFromPost( $toEdit );


        $tpl = & new CopixTpl ();
        $tpl->assign('TITLE_PAGE', CopixI18N::get ('schedule.title.selectDetailPage'));
        $tpl->assign ('MAIN', CopixZone::process ('cms|SelectPage', array (
                           'onlyLastVersion'=>1, 
                           'select'=>CopixUrl::get('cms_portlet_schedule||edit'), 
                           'back'=>CopixUrl::get('cms_portlet_schedule||edit')) ));
        return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
    }

    function doSelectHeading (){
        //Vérification des données à éditer.
        if (! ($toEdit = CMSPortletTools::getSessionPortlet ())){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get('schedule.unable.to.get.informations'),
            'back'=>CopixUrl::get ('cms|admin|edit')));
        }

        // attribution des variables du formulaire à $toEdit
        $this->_validFromPost ($toEdit);

        $tpl = & new CopixTpl ();
        $tpl->assign ('TITLE_PAGE',CopixI18N::get('schedule.selectHeading'));
        $tpl->assign ('MAIN', CopixZone::process ('copixheadings|SelectHeading', array ('select'=>CopixUrl::get('cms_portlet_schedule||edit'), 'back'=>CopixUrl::get('cms_portlet_schedule||edit')) ));
        return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
    }

    /**

    */
    function _validFromPost (){
        $data = CMSPortletTools::getSessionPortlet ();
		if (array_key_exists ('template', $this->vars)){
   			$data->setTemplate ($this->vars['template']);
		}
        //définition des éléments a vérifier
        $toCheck = array ( 'urldetail', 'id_head');

        //parcours des éléments à vérifier.
        foreach ($toCheck as $varToCheck){
            if (isset ($this->vars[$varToCheck])){
                $data->$varToCheck = $this->vars[$varToCheck];
            }
        }
        CMSPortletTools::setSessionPortlet ($data);
    }
}
?>