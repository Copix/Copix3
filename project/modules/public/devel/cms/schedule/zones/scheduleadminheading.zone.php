<?php
/**
* @package	cms
* @subpackage schedule
* @version	$Id: scheduleadminheading.zone.php,v 1.1 2007/04/08 18:08:13 gcroes Exp $
* @author	Bertrand Yan, Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam

* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
CopixClassesFactory::fileInclude ('cms|ServicesCMSPage');

/**
* @package	cms
* @subpackage schedule
* Enables the user to define the main model
*/
class ZoneScheduleAdminHeading extends CopixZone {
    function _createContent (& $toReturn){
        $servicesHeading = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        $tpl             = & new CopixTpl ();
        $workflow        = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
        $dao             = & CopixDAOFactory::create('ScheduleEvents');

        //capability
        $contribEnabled  = CopixUserProfile::valueOf ($servicesHeading->getPath ($this->_params['id_head']), 'schedule') >= PROFILE_CCV_WRITE;
        $publishEnabled  = CopixUserProfile::valueOf ($servicesHeading->getPath ($this->_params['id_head']), 'schedule') >= PROFILE_CCV_PUBLISH;
        $validEnabled    = CopixUserProfile::valueOf ($servicesHeading->getPath ($this->_params['id_head']), 'schedule') >= PROFILE_CCV_VALID;
        $moderateEnabled = CopixUserProfile::valueOf ($servicesHeading->getPath ($this->_params['id_head']), 'schedule') >= PROFILE_CCV_MODERATE;

        $tpl->assign ('contribEnabled' , $contribEnabled);
        $tpl->assign ('publishEnabled' , $publishEnabled);
        $tpl->assign ('validEnabled'   , $validEnabled);
        $tpl->assign ('moderateEnabled', $moderateEnabled);
        $tpl->assign ('id_head'        , $this->_params['id_head']);
        $tpl->assign ('pasteEnabled'   , CopixActionGroup::process ('schedule|AdminSchedule::canPaste', array ('level'=>$this->_params['id_head'])));

        if ($moderateEnabled) {
            $tpl->assign ('arEventDraft'  , $dao->getEventByStatus ($workflow->getDraft ()  ,$this->_params['id_head']));
            $tpl->assign ('arEventTrash'  , $dao->getEventByStatus ($workflow->getTrash ()  ,$this->_params['id_head']));
            $tpl->assign ('arEventRefuse' , $dao->getEventByStatus ($workflow->getRefuse () ,$this->_params['id_head']));
            $tpl->assign ('arEventPropose', $dao->getEventByStatus ($workflow->getPropose (),$this->_params['id_head']));
            $tpl->assign ('arEventValid'  , $dao->getEventByStatus ($workflow->getValid ()  ,$this->_params['id_head']));
        }elseif($publishEnabled) {
            $tpl->assign ('arEventDraft'  , $dao->getEventByUser ($workflow->getDraft ()  ,$this->_params['id_head']));
            $tpl->assign ('arEventTrash'  , $dao->getEventByStatusAuthor ($workflow->getTrash ()  ,$this->_params['id_head']));
            $tpl->assign ('arEventRefuse' , $dao->getEventByUser ($workflow->getRefuse () ,$this->_params['id_head']));
            $tpl->assign ('arEventPropose', $dao->getEventByStatus ($workflow->getPropose (),$this->_params['id_head']));
            $tpl->assign ('arEventValid'  , $dao->getEventByStatus ($workflow->getValid ()  ,$this->_params['id_head']));
        }elseif($validEnabled) {
            $tpl->assign ('arEventDraft'  , $dao->getEventByUser ($workflow->getDraft ()  ,$this->_params['id_head']));
            $tpl->assign ('arEventTrash'  , $dao->getEventByStatusAuthor ($workflow->getTrash ()  ,$this->_params['id_head']));
            $tpl->assign ('arEventRefuse' , $dao->getEventByUser ($workflow->getRefuse () ,$this->_params['id_head']));
            $tpl->assign ('arEventPropose', $dao->getEventByStatus ($workflow->getPropose (),$this->_params['id_head']));
            $tpl->assign ('arEventValid'  , $dao->getEventByUser ($workflow->getValid ()  ,$this->_params['id_head']));
        }elseif($contribEnabled) {
            $tpl->assign ('arEventDraft'  , $dao->getEventByUser ($workflow->getDraft ()  ,$this->_params['id_head']));
            $tpl->assign ('arEventTrash'  , $dao->getEventByStatusAuthor ($workflow->getTrash ()  ,$this->_params['id_head']));
            $tpl->assign ('arEventRefuse' , $dao->getEventByUser ($workflow->getRefuse () ,$this->_params['id_head']));
            $tpl->assign ('arEventPropose', $dao->getEventByUser ($workflow->getPropose (),$this->_params['id_head']));
            $tpl->assign ('arEventValid'  , $dao->getEventByUser ($workflow->getValid ()  ,$this->_params['id_head']));
        }else{
            $toReturn = '';
            return true;
        }

        $tpl->assign ('arEventPublish', $dao->getEventByStatus ($workflow->getPublish (),$this->_params['id_head']));

        $toReturn = $tpl->fetch ('schedule.adminheading.tpl');
        return true;
    }
}
?>
