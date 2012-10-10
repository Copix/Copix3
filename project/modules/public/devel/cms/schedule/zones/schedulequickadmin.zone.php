<?php
/**
* @package	cms
* @subpackage schedule
* @version	$Id: schedulequickadmin.zone.php,v 1.1 2007/04/08 18:08:13 gcroes Exp $
* @author	Bertrand Yan, Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam

* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* @package	cms
* @subpackage schedule
* shows what the current user can / should do all Heading.
*/
class ZoneScheduleQuickAdmin extends CopixZone {
    function _createContent (& $toReturn) {
        $servicesHeading = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        $tpl = & new CopixTpl ();

        $toPublish  = array ();
        $toValid    = array ();
        $workflow   = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
        $daoHead    = & CopixDAOFactory::getInstanceOf ('copixheadings|copixheadings');
        $dao        = & CopixDAOFactory::getInstanceOf ('schedule|scheduleevents');
        $arHeadings = $daoHead->findAll ();
        //add the root heading
        $arHeadings[]->id_head = null;
        foreach ($arHeadings as $heading){
            $capability = CopixUserProfile::valueOf ($servicesHeading->getPath ($heading->id_head), 'news');
            if ($capability >= PROFILE_CCV_PUBLISH) {
                $toPublish = array_merge($toPublish,$dao->getEventByStatus ($workflow->getValid ()  ,$heading->id_head));
            }
            if($capability >= PROFILE_CCV_VALID) {
                $toValid   = array_merge($toValid  ,$dao->getEventByStatus ($workflow->getPropose (),$heading->id_head));
            }
        }
        //could contrib at least ?
        $tpl->assign ('toPublish', $toPublish);
        $tpl->assign ('toValid'  , $toValid);

        $toReturn = ($toPublish === array ()) && ($toValid === array ()) ? '' : $tpl->fetch ('schedule.quickadmin.tpl');

        return true;
    }
}
?>