<?php
/**
* @package	cms
* @subpackage pictures
* @author	Bertrand Yan
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package	cms
* @subpackage pictures
* shows what the current user can publish.
*/
class ZonePicturesQuickAdmin extends CopixZone {
    function _createContent (& $toReturn) {
        $tpl = & new CopixTpl ();
        $workflow        = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
        $servicesHeading = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        $toPublish       = array ();
        
        $toPublish = $this->_getPictures($workflow->getValid());
        foreach ((array)$toPublish as $key=>$picture){
            if (!(CopixUserProfile::valueOf ($servicesHeading->getPath ($picture->id_head), 'pictures') >= PROFILE_CCV_PUBLISH)) {
               unset($toPublish[$key]);
            }
        }
        $tpl->assign ('toPublish', $toPublish);
        
        $toValid = $this->_getPictures($workflow->getPropose());
        foreach ((array)$toValid as $key=>$picture){
            if (!(CopixUserProfile::valueOf ($servicesHeading->getPath ($picture->id_head), 'pictures') >= PROFILE_CCV_PUBLISH)) {
               unset($toValid[$key]);
            }
        }
        $tpl->assign ('toValid', $toValid);

        $toReturn = $toPublish === array () && $toValid === array() ? '' : $tpl->fetch ('pictures.quickadmin.tpl');

        return true;
    }

    function _getPictures ($status){
        $daoPicture = & CopixDAOFactory::getInstanceOf ('pictures');
        $daoHeadings= & CopixDAOFactory::getInstanceOf ('copixheadings|copixheadings');
        $sp         = & CopixDAOFactory::createSearchParams ();
        
        //ajout de la condition sur le worklow pour avoir les images en propositions
        $sp->addCondition ('status_pict', '=', $status);
        $sp->orderBy ('id_head');
        //Envoie de la liste des categories et format au template
        $pictures = $daoPicture->findBy ($sp);
        foreach ($pictures as $key=>$picture){
            $pictures[$key]->theme = $this->_getThemes($picture->id_pict);
            $pictures[$key]->caption_head = ($heading = $daoHeadings->get($picture->id_head)) != false ? $heading->caption_head : CopixI18N::get('copixheadings|headings.message.root');
        }
        return $pictures;
    }

    function _getThemes ($id_pict){
        $daoPictureLinkTheme = & CopixDAOFactory::getInstanceOf ('pictureslinkthemes');
        $daoPictureTheme     = & CopixDAOFactory::getInstanceOf ('picturesthemes');
        $sp                  = & CopixDAOFactory::createSearchParams ();
        $sp->addCondition ('id_pict','=',$id_pict);
        $tabIdTheme = $daoPictureLinkTheme->findBy($sp);
        $tab = array ();
        foreach ($tabIdTheme as $object){
            $tab[] = $object->id_tpic;
        }
        if (count ($tab)){
            $sp = & CopixDAOFactory::createSearchParams ();
            $sp->addCondition ('id_tpic','=',$tab);
            $tabTheme = $daoPictureTheme->findBy($sp);
            $tab = array();
            foreach ($tabTheme as $object){
                $tab[] = $object->name_tpic;
            }
            return $tab;
        }else{
            return null;
        }

    }

}
?>