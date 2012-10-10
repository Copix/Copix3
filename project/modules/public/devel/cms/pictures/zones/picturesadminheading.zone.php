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
* shows what the current user can / should do in the given CopixHeading.
*/
class ZonePicturesAdminHeading extends CopixZone {
    /**
    * @param int $this->_params['id_head'] the CopixHeading id.
    */
    function _createContent (& $toReturn) {
        $servicesHeading = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        $tpl             = & new CopixTpl ();
        $workflow         = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
        $dao             = & CopixDAOFactory::getInstanceOf('pictures');
        $daoPictHeadings = & CopixDAOFactory::getInstanceOf('picturesheadings');
        $daoTheme        = & CopixDAOFactory::getInstanceOf('picturesthemes');
        $spTheme         = & CopixDAOFactory::createSearchParams ();
        $spTheme->orderBy ('name_tpic');

        //capability
        $contribEnabled  = CopixUserProfile::valueOf ($servicesHeading->getPath ($this->_params['id_head']), 'pictures') >= PROFILE_CCV_WRITE;
        $publishEnabled  = CopixUserProfile::valueOf ($servicesHeading->getPath ($this->_params['id_head']), 'pictures') >= PROFILE_CCV_PUBLISH;
        $validEnabled    = CopixUserProfile::valueOf ($servicesHeading->getPath ($this->_params['id_head']), 'pictures') >= PROFILE_CCV_VALID;
        $moderateEnabled = CopixUserProfile::valueOf ($servicesHeading->getPath ($this->_params['id_head']), 'pictures') >= PROFILE_CCV_MODERATE;

        $tpl->assign ('contribEnabled' , $contribEnabled);
        $tpl->assign ('publishEnabled' , $publishEnabled);
        $tpl->assign ('validEnabled'   , $validEnabled);
        $tpl->assign ('moderateEnabled', $moderateEnabled);
        $tpl->assign ('id_head'        , $this->_params['id_head']);
        $tpl->assign ('themeList'      , $daoTheme->findBy ($spTheme));
        $tpl->assign ('pasteEnabled'   , CopixActionGroup::process ('pictures|Admin::_canPaste', array ('id_head'=>$this->_params['id_head'])));

        switch ($this->getParam ('kind', 0)){
            case 1:
            $kind   = "properties";
            $toEdit = $this->_getSessionTheme ();
            if ($this->getParam ('e', false)) {
                $tpl->assign ('errors' ,$daoTheme->check ($toEdit));
            }
            $tpl->assign ('showErrors'       , $this->getParam ('e', false));
            $tpl->assign ('headingProperties', $daoPictHeadings->get ($this->_params['id_head']));
            $tpl->assign ('toEdit'           , $toEdit);
            break;
			case 2:
				$kind = "import";
			break;
            case 0:
            default:
            $kind = "general";
            // assign id of the current CopixHeadings
            if ($moderateEnabled) {
                $tpl->assign ('arPictureDraft'  , $dao->getPictureByStatus ($workflow->getDraft ()  ,$this->_params['id_head']));
                $tpl->assign ('arPictureTrash'  , $dao->getPictureByStatus ($workflow->getTrash ()  ,$this->_params['id_head']));
                $tpl->assign ('arPictureRefuse' , $dao->getPictureByStatus ($workflow->getRefuse () ,$this->_params['id_head']));
                $tpl->assign ('arPicturePropose', $dao->getPictureByStatus ($workflow->getPropose (),$this->_params['id_head']));
                $tpl->assign ('arPictureValid'  , $dao->getPictureByStatus ($workflow->getValid ()  ,$this->_params['id_head']));
            }elseif($publishEnabled) {
                $tpl->assign ('arPictureDraft'  , $dao->getPictureByUser ($workflow->getDraft ()  ,$this->_params['id_head']));
                $tpl->assign ('arPictureTrash'  , $dao->getPictureByStatusAuthor ($workflow->getTrash ()  ,$this->_params['id_head']));
                $tpl->assign ('arPictureRefuse' , $dao->getPictureByUser ($workflow->getRefuse () ,$this->_params['id_head']));
                $tpl->assign ('arPicturePropose', $dao->getPictureByStatus ($workflow->getPropose (),$this->_params['id_head']));
                $tpl->assign ('arPictureValid'  , $dao->getPictureByStatus ($workflow->getValid ()  ,$this->_params['id_head']));
            }elseif($validEnabled) {
                $tpl->assign ('arPictureDraft'  , $dao->getPictureByUser ($workflow->getDraft ()  ,$this->_params['id_head']));
                $tpl->assign ('arPictureTrash'  , $dao->getPictureByStatusAuthor ($workflow->getTrash ()  ,$this->_params['id_head']));
                $tpl->assign ('arPictureRefuse' , $dao->getPictureByUser ($workflow->getRefuse () ,$this->_params['id_head']));
                $tpl->assign ('arPicturePropose', $dao->getPictureByStatus ($workflow->getPropose (),$this->_params['id_head']));
                $tpl->assign ('arPictureValid'  , $dao->getPictureByUser ($workflow->getValid ()  ,$this->_params['id_head']));
            }elseif($contribEnabled) {
                $tpl->assign ('arPictureDraft'  , $dao->getPictureByUser ($workflow->getDraft ()  ,$this->_params['id_head']));
                $tpl->assign ('arPictureTrash'  , $dao->getPictureByStatusAuthor ($workflow->getTrash ()  ,$this->_params['id_head']));
                $tpl->assign ('arPictureRefuse' , $dao->getPictureByUser ($workflow->getRefuse () ,$this->_params['id_head']));
                $tpl->assign ('arPicturePropose', $dao->getPictureByUser ($workflow->getPropose (),$this->_params['id_head']));
                $tpl->assign ('arPictureValid'  , $dao->getPictureByUser ($workflow->getValid ()  ,$this->_params['id_head']));
            }else{
                $toReturn = '';
                return true;
            }

            $tpl->assign ('arPicturePublish', $dao->getPictureByStatus ($workflow->getPublish (),$this->_params['id_head']));
            break;
        }

        $tpl->assign ('kind', $kind);
        $toReturn = $tpl->fetch ('pictures.adminheading.tpl');
        return true;
    }

    /**
    * gets the current edited theme.

    */
    function _getSessionTheme (){
        CopixDAOFactory::fileInclude ('picturesthemes');
        return isset ($_SESSION['MODULE_PICTURES_EDITED_THEME']) ? unserialize ($_SESSION['MODULE_PICTURES_EDITED_THEME']) : null;
    }
}
?>
