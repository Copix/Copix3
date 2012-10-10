<?php
/**
* @package	cms
* @author	Croës Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/


/**
 * @ignore
 */
CopixClassesFactory::fileInclude ('cms|PortletFactory');

/**
* @package cms 
* Show a portlet list.
*/
class ZonePortletChoice extends CopixZone {
   function _createContent (&$toReturn) {
		$tpl = new CopixTpl ();

                //sorting portlets by name
                $sort = array ();
                foreach ((array)PortletFactory::getList () as $elem){
                	PortletFactory::includePortlet ($elem);
                    $sort[CopixI18N::get(eval ('return '.$elem.'Portlet::getI18NKey ();'))] = $elem;
                }
                ksort ($sort); 

		//assignation des éléments.
		$tpl->assign ('arPortlet'  , $sort);
		$tpl->assign ('templateVar', $this->_params['templateVar']);
		$tpl->assign ('pasteEnable', $this->_params['pasteEnable']);

		//récupération de toutes les pages crées, par type.
		$toReturn = $tpl->fetch ('cms|portletchoice.tpl');
		return true;
	}
}
?>
