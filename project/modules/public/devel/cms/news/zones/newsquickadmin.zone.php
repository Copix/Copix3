<?php
/**
* @package	cms
* @subpackage news
* @author	Bertrand Yan, Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* @package	cms
* @subpackage news
* shows what the current user can / should do all Heading.
*/
class ZoneNewsQuickAdmin extends CopixZone {
    function _createContent (& $toReturn) {
        $servicesHeading = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        $tpl = & new CopixTpl ();

        $toPublish  = array ();
        $toValid    = array ();
        $workflow   = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
        $daoHead    = & CopixDAOFactory::getInstanceOf ('copixheadings|copixheadings');
        $dao        = & CopixDAOFactory::getInstanceOf ('news|news');
        $arHeadings = $daoHead->findAll ();
        //add the root heading
        $arHeadings[]->id_head = null;
        foreach ($arHeadings as $heading){
            $capability = CopixUserProfile::valueOf ($servicesHeading->getPath ($heading->id_head), 'news');
            if ($capability >= PROFILE_CCV_PUBLISH) {
               $toPublish = array_merge($toPublish,$dao->getNewsByStatus ($workflow->getValid ()  ,$heading->id_head));
            }
            if($capability >= PROFILE_CCV_VALID) {
               $toValid   = array_merge($toValid  ,$dao->getNewsByStatus ($workflow->getPropose (),$heading->id_head));
            }
        }
        //could contrib at least ?
        $tpl->assign ('toPublish', $toPublish);
        $tpl->assign ('toValid'  , $toValid);

	     $toReturn = ($toPublish === array ()) && ($toValid === array ()) ? '' : $tpl->fetch ('news.quickadmin.tpl');

        return true;
    }

}
?>