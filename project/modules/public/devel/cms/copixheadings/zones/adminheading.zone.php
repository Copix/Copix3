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
* @package cms
* @subpackage copixheadings
* Admin view of the headings.
*  we assume the given level exists.
*   give "null"
*/
class ZoneAdminHeading extends CopixZone {
    function _createContent (& $toReturn) {
        //we get the path.
        $headingServices        = CopixClassesFactory::getInstanceOf ('CopixHeadingsServices');
        $headingProfileServices = CopixClassesFactory::getInstanceOf ('CopixProfileForHeadingServices');

        //we check if the path exists.
        $path = $headingServices->getPath ($this->_params['id_head']);
        if ($path === null){
            return false;//no more explainations, may add some in the future.
        }

        //we get the subpaths
        $arHeadings         = $headingServices->getLevel ($this->_params['id_head']);
        $arCompleteHeadings = $headingServices->getFlatOrderedList ();
        $headingProfileServices->appendProfileInformation ($path);

        //profile information appending.
        $headingProfileServices->filter ($arHeadings, PROFILE_CCV_SHOW);

        //assign data to the template.
        $tpl = new CopixTpl ();
        $tpl->assignByRef ('path'      , $path);
        $tpl->assignByRef ('arHeadings', $arHeadings);
        $tpl->assignByRef ('modules'     , $this->_params['modules']);
        $tpl->assignByRef ('browse'      , $this->_params['browse']);
        $tpl->assignByRef ('currentLevel', $this->_params['id_head']);

        $tpl->assign ('moduleLineBreakCount'       , CopixConfig::get ('moduleLineBreakCount'));
        $tpl->assign ('headingsTree'               , CopixZone::process ('copixheadings|SelectHeading',
        array ('select'=>CopixUrl::get ("admin|",  array ('browse'=>$this->getParam('browse'))),
        'back'=>CopixUrl::get ('cms_portlet_news||edit'),
        'mini'=>1,
        'selected'=>$this->_params['id_head'])));

        $tpl->assign ('moduleZone', CopixZone::process ($this->_params['browse'].'|'.$this->_params['browse'].'AdminHeading', $this->_params));

        if (CopixConfig::get('copixheadings|adminHeadingPosition') == 'v') {
            $toReturn = $tpl->fetch ('copixheadings.admin.vertical.tpl');
        }elseif (CopixConfig::get('copixheadings|adminHeadingPosition') == 'h'){
            $toReturn = $tpl->fetch ('copixheadings.admin.tpl');
        }else{
            $toReturn = $tpl->fetch ('copixheadings.admin.tpl');
        }

        //les éléments de réponses dont on a besoin:
        //Noms des modules : Caption, Url ? (si non donné, alors action standard, title)
        return true;
    }
}
?>
