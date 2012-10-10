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
* Zone d'affichage pour la confirmation de suppression d'un theme.
*/
class ZoneDeleteTheme extends CopixZone {
    function _createContent (&$toReturn){
        $tpl = & new CopixTpl ();
        //Creation des DAO factory
        $daoPicturesThemes    = & CopixDAOFactory::getInstanceOf ('picturesthemes');
        $daoPicturesLinkThemes = & CopixDAOFactory::getInstanceOf ('pictureslinkthemes');
        //envoie de tous les themes, categories et formats
        $tpl->assign('count_pict',$daoPicturesLinkThemes->getCountPictures($this->getParam ('id_tpic')));
        $tpl->assign('themeList' ,$daoPicturesThemes->findAll ());
        $tpl->assign('id_head'   ,$this->getParam ('id_head', null));
        $tpl->assign('delTheme'  ,$daoPicturesThemes->get ($this->getParam ('id_tpic')));
        $toReturn = $tpl->fetch ('deletetheme.tpl');
        return true;
    }
}
?>