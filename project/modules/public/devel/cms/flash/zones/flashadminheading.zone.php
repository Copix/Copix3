<?php
/**
* @package		cms
* @subpackage	flash
* @author		Croës Gérald, Salleyron Julien
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package		cms
* @subpackage	flash
* Affichage des documents flash existant dans la rubrique pour en permettre l'administration
*/
class ZoneFlashAdminHeading extends CopixZone {
    function _createContent (& $toReturn) {
        $tpl = new CopixTpl ();
        $dao = CopixDAOFactory::getInstanceOf ('Flash');
        $tpl->assign ('id_head', $this->getParam ('id_head'));
        $tpl->assign ('arDocuments'  , $dao->findAllLastVersionByHeading ($this->getParam ('id_head')));
        $toReturn = $tpl->fetch ('flash.adminheading.tpl');
        return true;
    }
}
?>
