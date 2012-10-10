<?php
/**
* @package cms
* @subpackage schedule
* @author	Bertrand Yan, Croes Gérald see copix.org for other contributors.
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
 * @ignore
 */
CopixClassesFactory::fileInclude ('schedule|scheduleworkflow');
CopixClassesFactory::fileInclude ('schedule|scheduleservice');
CopixClassesFactory::fileInclude ('cms|Servicescmspage');

/**
* @package	cms
* @subpackage schedule
* ZoneAdmin
*/
class ZoneAdmin extends CopixZone {
    var $id_head;
    function _createContent (&$toReturn) {
        $this->id_head = isset ($_SESSION['MODULE_SCHEDULE_CURRENT_HEADING']) ? unserialize ($_SESSION['MODULE_SCHEDULE_CURRENT_HEADING']) : null;

        $servicesHeading = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        $valueHeadingPath = $servicesHeading->getPath ($this->id_head);

        //echo 'Heading'. $this->id_head;

        $services     = & new ScheduleService ();
        //$arCategories = $services->getCategoriesIdArray ();

        $tpl = new CopixTpl ();

        // assign id of the current CopixHeadings
        $tpl->assign ('id_head', $this->id_head);
        $dao = CopixDAOFactory::create ('copixheadings|CopixHeadings');
        if ($heading = $dao->get( $this->id_head )) {
            $caption_head = $heading->caption_head;
        } else {
            $caption_head = CopixI18N::get('copixheadings|headings.message.root');
        }
        $tpl->assign ('caption_head', $caption_head);

        $tpl->assign ('contribEnabled', CopixUserProfile::valueOfIn ('schedule', $valueHeadingPath) >= PROFILE_CCV_WRITE);

        $tmp = array();
        $totalOnline = 0;
        $totalOffline = 0;

        $tpl->assign ('validEnabled', CopixUserProfile::valueOfIn ('schedule', $valueHeadingPath) >= PROFILE_CCV_VALID);
        $tmp = $this->_getScheduleStatus (ScheduleWorkflow::getCreate ());
        $tpl->assign ('arScheduleValid', $tmp );
        $totalOffline += count($tmp);

        $tpl->assign ('publishEnabled', CopixUserProfile::valueOfIn ('schedule', $valueHeadingPath) >= PROFILE_CCV_PUBLISH);
        $tmp = $this->_getScheduleStatus (ScheduleWorkflow::getValid ());
        $tpl->assign ('arSchedulePublish', $tmp);
        $totalOffline += count($tmp);

        $tpl->assign ('moderateEnabled', CopixUserProfile::valueOf ($valueHeadingPath , 'schedule') >= PROFILE_CCV_MODERATE);
        $tmp = $this->_getScheduleStatus (ScheduleWorkflow::getTrash ());
        $tpl->assign ('arScheduleTrash', $tmp);
        $totalOffline += count($tmp);

        $tpl->assign ('publishEnabled', CopixUserProfile::valueOfIn ('schedule', $valueHeadingPath) >= PROFILE_CCV_PUBLISH);
        $tmp = $this->_getScheduleStatus (ScheduleWorkflow::getPublish ());
        $tpl->assign ('arScheduleOnline', $tmp);
        $totalOnline = count($tmp);

        $tpl->assign ('adminEnabled', CopixUserProfile::valueOf ($valueHeadingPath , 'schedule') >= PROFILE_CCV_ADMIN);
        $tpl->assign('totalOffline',$totalOffline);
        $tpl->assign('totalOnline',$totalOnline);

        if($totalOffline == 0 && $totalOnline==0) {
            CopixHTMLHeader::addOthers ('<meta http-equiv="refresh"content="0;URL=index.php?module=copixheadings&desc=admin">');
        }

        $toReturn = $tpl->fetch ('admin.main.tpl');
        return true;
    }

    /**
    * gets the event by status
    */
    function _getScheduleStatus ($status){
        $dao = & CopixDAOFactory::create ('ScheduleEvents');
        $sp  = CopixDAOFactory::createSearchParams ();

        $sp->addCondition ('id_head', '=',$this->id_head);
        $sp->addCondition ('status_evnt', '=', $status);

        $results = $dao->findBy ($sp);
        return $results;
    }
}
?>
