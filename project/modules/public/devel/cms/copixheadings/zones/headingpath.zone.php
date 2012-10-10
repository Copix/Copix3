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
class ZoneHeadingPath extends CopixZone {
    function _createContent (& $toReturn) {
        //we get the path.
        $headingServices        = & CopixClassesFactory::getInstanceOf ('CopixHeadingsServices');
        $headingProfileServices = & CopixClassesFactory::getInstanceOf ('CopixProfileForHeadingServices');

        //we check if the path exists.
        $path = $headingServices->getPath ($this->_params['id_head']);
        if ($path === null){
            return false;//no more explainations, may add some in the future.
        }

        //we get the subpaths
        $arHeadings = $headingServices->getLevel ($this->_params['id_head']);

        //profile information appending.
        $headingProfileServices->filter ($arHeadings, PROFILE_CCV_SHOW);
        $headingProfileServices->appendProfileInformation ($path);

        //assign data to the template.
        $tpl = & new CopixTpl ();
        $tpl->assignByRef ('root', CopixI18n::get('copixheadings|copixheadings.message.root'));
        $tpl->assignByRef ('path', $path);
        $tpl->assignByRef ('arHeadings', $arHeadings);

        $toReturn = $tpl->fetch ('headingpath.tpl');
        return true;
    }
}
?>