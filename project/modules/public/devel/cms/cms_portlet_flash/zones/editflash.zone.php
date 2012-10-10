<?php
/**
* @package		cms
* @subpackage	cms_portlet_flash
* @author		Croës Gérald, Salleyron Julien
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Modification d'un document flash
 * @package		cms
 * @subpackage	cms_portlet_flash 
 */
class ZoneEditFlash extends CopixZone {
    function _createContent (&$ToReturn){
        $tpl = new CopixTpl ();
        switch ($this->_params['kind']){
            case 0:
            $kind = "general";
            break;

            case 1:
            $kind = "preview";
            $tpl->assign ('preview', $this->getParam ('toEdit')->getParsed ("content"));
            break;

            default:
            $kind = "general";
            break;
        }
        $daoFlash = CopixDAOFactory::getInstanceOf ('flash|flash');
		$id_head = null;
        $arFlash = $daoFlash->findAllLastVersionByHeading ($id_head);
        
        $tpl->assign ('arFlash',$arFlash);
        $tpl->assign ('flash', $this->getParam ('toEdit'));
        $tpl->assign ('kind', $kind);
		$tpl->assign ('possibleKinds', CopixTpl::find ('cms_portlet_flash', '.portlet.?tpl'));

        //appel du template.
        $ToReturn = $tpl->fetch ('cms_portlet_flash|flash.edit.tpl');
        return true;
    }
}
?>