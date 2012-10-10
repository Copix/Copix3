<?php
/**
* @package cms
* @subpackage	document
* @author	Bertrand Yan, Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
 * 
 * @package cms
 * @subpackage	document
* shows what the current user can / should do all Heading.
*/
class ZoneDocumentQuickAdmin extends CopixZone {
    function _createContent (& $toReturn) {
        $servicesHeading = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        $tpl = & new CopixTpl ();

        $toPublish  = array ();
        $toValid    = array ();
        $workflow   = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
        $daoHead    = & CopixDAOFactory::getInstanceOf ('copixheadings|copixheadings');
        $dao        = & CopixDAOFactory::getInstanceOf ('document|document');
        $arHeadings = $daoHead->findAll ();
        //add the root heading
        $arHeadings[]->id_head = null;
        foreach ($arHeadings as $heading){
            $capability = CopixUserProfile::valueOf ($servicesHeading->getPath ($heading->id_head), 'document');
            if ($capability >= PROFILE_CCV_PUBLISH) {
               $toPublish = array_merge($toPublish,$dao->getDocumentByStatus ($workflow->getValid ()  ,$heading->id_head));
            }
            if($capability >= PROFILE_CCV_VALID) {
               $toValid   = array_merge($toValid  ,$dao->getDocumentByStatus ($workflow->getPropose (),$heading->id_head));
            }
        }

        $tpl->assign ('toPublish', $toPublish);
        $tpl->assign ('toValid'  , $toValid);

	     $toReturn = $toPublish === array () && $toValid === array() ? '' : $tpl->fetch ('document.quickadmin.tpl');

        return true;
    }

}
?>
