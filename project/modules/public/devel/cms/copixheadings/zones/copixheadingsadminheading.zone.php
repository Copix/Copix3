<?php
/**
* @package	 cms
* @subpackage copixheadings
* @author	Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* @package	 cms
* @subpackage copixheadings
* Admin view of the headings.
*  we assume the given level exists.
*   give "null"
*/
class ZoneCopixHeadingsAdminHeading extends CopixZone {
    function _createContent (& $toReturn) {
        //we get the path.
        $headingServices        = CopixClassesFactory::getInstanceOf ('CopixHeadingsServices');
        $headingProfileServices = CopixClassesFactory::getInstanceOf ('CopixProfileForHeadingServices');

        //profile information appending.
        $arHeadings = $headingServices->getLevel ($this->_params['id_head']);
        $headingProfileServices->filter ($arHeadings, PROFILE_CCV_SHOW);

        //now we're gonna add the "canDelete" information.
        foreach ((array) $arHeadings as $key=>$heading) {
            $canDelete = false;
            if ($heading->profileInformation >= PROFILE_CCV_WRITE){
                $response = CopixEventNotifier::notify(new CopixEvent ('HasContentRequest', array ('id'=>$heading->id_head)));
                $who = array ();
                if (! $response->inResponse ('hasContent', true, $who)){
                    $canDelete = true;
                }
            }
            $arHeadings[$key]->canDelete = $canDelete;
        }

        //assign data to the template.
        $tpl = & new CopixTpl ();
        $tpl->assignByRef ('arHeadings', $arHeadings);

        //may we write in this level ? (for update, create or so things in here)
        $tpl->assign ('writeEnabled', $writeEnabled = CopixUserProfile::valueOf ($headingProfileServices->getPath($this->_params['id_head']), 'copixheadings') >= PROFILE_CCV_WRITE);
        $tpl->assign ('currentLevel', $this->_params['id_head']);
        $tpl->assign ('cutLevel', $this->getParam ('cutLevel'));
        $tpl->assign ('pasteEnabled', CopixActionGroup::process ('AdminHeading::canPaste', array ('id_head'=>$this->_params['id_head'])) && $writeEnabled);

        $toReturn = $tpl->fetch ('copixheadings.adminheading.tpl');
        return true;
    }
}
?>