<?php
/**
* @package cms
* @subpackage	 cms_portlet_document
* @author	Bertrand Yan, Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @package cms
 * @subpackage	cms_portlet_document
 * ZoneEditDocument
 */
class ZoneEditDocument extends CopixZone {
    function _createContent (&$ToReturn){
        $tpl = new CopixTpl ();

        $objDocs = $this->_params['toEdit'];

        switch ($this->_params['kind']){
            case 0:
            $kind = "general";
            break;

            case 1:
            $kind = "preview";
            $tpl->assign ('preview', $objDocs->getParsed ("content"));
            break;

            default:
            $kind = "general";
            break;
        }
        $tpl->assign ('objDocs', $objDocs);
        $tpl->assign ('arDocs', $objDocs->getDocs ());
        $tpl->assign ('kind', $kind);
		$tpl->assign ('possibleKinds', CopixTpl::find ('cms_portlet_document', '.portlet.?tpl'));

        //appel du template.
        $ToReturn = $tpl->fetch ('cms_portlet_document|document.edit.tpl');
        return true;
    }
}
?>