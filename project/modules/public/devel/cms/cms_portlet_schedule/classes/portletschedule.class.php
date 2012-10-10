<?php
/**
* @package cms
* @subpackage cms_portlet_schedule
* @author	Bertrand Yan, Ferlet Patrice see copix.org for other contributors.
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
CopixClassesFactory::fileInclude ('cms|portlet');
CopixClassesFactory::fileInclude ('cms|cmsparsecontext');


/**
* @package	cms
* @subpackage cms_portlet_schedule
* Portlet qui affiche la liste des événements
*/
class PortletSchedule extends Portlet {
    /**
    * Rubrique que l'on souhaite utiliser pour l'affichage des événements
    */
    var $id_head;
    
    /**
     * Page permettant l'inscription à l'événement 
     */
    var $id_page_subscribe; 

    /**
    * Constructeur
    */
    function PortletSchedule ($id) {
        parent::Portlet ($id);
    }

    /**
    * Récupération du code HTML correspondant à la portlet
    */
    function getParsed ($context) {
        //only in front
        if ($context==CMSParseContext::front) {
           //first page call, save url
           if ($this->_getSessionUrl ($this->id) == null) {
					$this->_setSessionUrl (CopixUrl::get('cms||get',array('id'=>$this->_page->publicid_cmsp)), $this->id);
           }
        }

        $tpl = & new CopixTpl ();
		$heading = $this->id_head;
        $workflow = CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
        $tpl->assign ('tabEvents', $this->_getRestrictedListByHeading ($heading, $workflow->getPublish ()));
        $tpl->assign ('id_page_subscribe', $this->id_page_subscribe);
        if(isset($this->params['id'])) {
	        $tpl->assign ('url', CopixURL::get("cms||get", array("id"=>$this->params['id'])));
	    } else {
        	  $tpl->assign ('url'  , CopixURL::get().$this->_getSessionUrl ($this->id));
        }
        return $tpl->fetch ($this->templateId ? $this->getTemplateSelector() : 'cms_portlet_schedule|normal.schedule.tpl');
    }

    /**
    * get schedule events by date and heading
    * @param object $date date of events
    * @param int $id_head heading identifier
    * @param int $status events status
    * @return array

    */
    function _getRestrictedListByHeading ($id_head, $status) {
        $dao = CopixDAOFactory::create ('schedule|ScheduleEvents');
        return $dao->findByDate (date ("Ymd"), $id_head);
    }

    /**
    * gets the current url.

    */
    function _getSessionUrl ($id) {
        return isset ($_SESSION['MODULE_CMS_PORTLET_SCHEDULE_URL_'.$id]) ? unserialize ($_SESSION['MODULE_CMS_PORTLET_SCHEDULE_URL_'.$id]) : null;
    }

    /**
    * sets the current url.

    */
    function _setSessionUrl ($toSet, $id){
        $_SESSION['MODULE_CMS_PORTLET_SCHEDULE_URL_'.$id] = $toSet !== null ? serialize($toSet) : null;
    }

    /**
    * Récupération du groupe de la portlet
    */
    function getGroup (){
        return 'general';
    }

    /**
    * Récupération du nom de la portlet
    */
    function getI18NKey (){
        return 'cms_portlet_schedule|schedule.description';
    }

    /**
    * Récupération du nom du groupe
    */
    function getGroupI18NKey (){
        return 'cms_portlet_schedule|schedule.group';
    }
}
/**
* @package	cms
* @subpackage cms_portlet_schedule
*/
class SchedulePortlet extends PortletSchedule {}
?>
