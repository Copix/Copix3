<?php
/**
* @package cms
* @subpackage schedule
* @author	Bertrand Yan, Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
CopixClassesFactory::fileInclude ('cms|ServicesCMSPage');
CopixClassesFactory::fileInclude ('schedule|scheduleworkflow');

/**
* @package	cms
* @subpackage schedule
* Enables the user to define the main model
*/
class ZoneCMSIndexHeadingSchedule extends CopixZone {
    function _createContent (& $toReturn){
        $tpl = & new CopixTpl ();
        $tpl->assign ('id_head', $this->_params['id_head']);

        // On retrouve tous les evnts de cette rubrique
        $dao = & CopixDAOFactory::create ('schedule|scheduleevents');
        $sp  = & CopixDAOFactory::createSearchParams();
        $sp->addCondition ('id_head', '=' , $this->_params['id_head']);
        $tabEvents = $dao->findBy ($sp);

        // On parcour tous les evnts pour trouver le nombre dans la rub ainsi que leur état
        $SW = & new ScheduleWorkflow();
        $status_pub = $SW->getPublish();
        $nbEventsPublished=0;
        $nbEvents = 0;
        foreach ($tabEvents as $Events) {
            if ($Events->status_evnt == $status_pub) {
                $nbEventsPublished++;
            }
            $nbEvents++;
        }

        $servicesHeading = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        $tpl->assign ('manageEnabled', $nonEmpty = CopixUserProfile::valueOfIn ('schedule', $servicesHeading->getPath ($this->_params['id'])) >= PROFILE_CCV_WRITE);
        $tpl->assign ('publishEnabled', CopixUserProfile::valueOfIn ('schedule', $servicesHeading->getPath ($this->_params['id'])) >= PROFILE_CCV_PUBLISH);

        $tpl->assign ('nbEvents', $nbEvents);
        $tpl->assign ('nbEventsPublished', $nbEventsPublished);
        $tpl->assign ('nbEventsToPublish', $nbEvents - $nbEventsPublished);

        $toReturn = $tpl->fetch ('copixheadings.admin.tpl');
        return true;
    }
}
?>
