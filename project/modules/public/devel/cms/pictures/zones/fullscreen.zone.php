<?php
/**
* @package	cms
* @subpackage pictures
* @author	Bertrand Yan
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package	cms
* @subpackage pictures
* Zone d'affichage pour une image en plein ecran.
*/
class ZoneFullScreen extends CopixZone {
    function _createContent (&$toReturn){
        $tpl = & new CopixTpl ();
        $servicesHeading   = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        $dao               = & CopixDAOFactory::getInstanceOf ('pictures|pictures');
        $picture           = $dao->get ($this->_params['id_pict']);

        $tpl->assign ('moderateEnabled', CopixUserProfile::valueOf ($servicesHeading->getPath ($picture->id_head),'pictures') >= PROFILE_CCV_PUBLISH);
        $tpl->assign ('id_head'  ,$this->_params['id_head']);
        $tpl->assign ('picture'  ,$picture);
        $toReturn = $tpl->fetch ('fullscreen.tpl');
        return true;
    }
}
?>