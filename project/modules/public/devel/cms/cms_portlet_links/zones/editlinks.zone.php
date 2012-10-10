<?php
/**
* @package	cms
* @subpackage cms_portlet_links
* @author	Bertrand Yan, Croes Gérald
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package	cms
* @subpackage cms_portlet_links
* show the list of the known pages.
*/
class ZoneEditLinks extends CopixZone {
    /**
	* Attends un objet de type textpage en paramètre.
	*/
    function _createContent (&$toReturn){
        $tpl = new CopixTpl ();
        $tpl->assign ('toEdit', $this->_params['toEdit']);

        //recherche de templates.
		$tpl->assign ('possibleKinds', CopixTpl::find ('cms_portlet_links', '.portlet.?tpl'));

        switch ($this->_params['kind']){
            case 0:
            $kind = "general";
            break;

            case 1:
            $kind = "preview";
            break;

            default:
            $kind = "general";
            break;
        }

        $tpl->assign ('show', $this->_params['toEdit']->getParsed ('content'));
        $tpl->assign ('kind', $kind);

        //appel du template.
        $toReturn = $tpl->fetch ('cms_portlet_links|links.edit.tpl');
        return true;
    }
}
?>