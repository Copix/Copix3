<?php
/**
* @package	cms
* @subpackage document
* @author	Bertrand Yan, Croes Gérald
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
 * @package	cms
* @subpackage document
* shows what the current user can / should do in the given CopixHeading.
*/
class ZoneDocumentAdminHeading extends CopixZone {
    /**
    * @param int $this->_params['id'] the CopixHeading id.
    */
    function _createContent (& $toReturn) {
        $tpl = & new CopixTpl ();
        $servicesHeading  = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        $workflow         = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
        $dao              = & CopixDAOFactory::getInstanceOf ('Document');

        // assign id of the current CopixHeadings
        $tpl->assign ('id_head', $this->_params['id_head']);
        //capability
        $contribEnabled  = CopixUserProfile::valueOf ($servicesHeading->getPath ($this->_params['id_head']), 'document') >= PROFILE_CCV_WRITE;
        $publishEnabled  = CopixUserProfile::valueOf ($servicesHeading->getPath ($this->_params['id_head']), 'document') >= PROFILE_CCV_PUBLISH;
        $validEnabled    = CopixUserProfile::valueOf ($servicesHeading->getPath ($this->_params['id_head']), 'document') >= PROFILE_CCV_VALID;
        $moderateEnabled = CopixUserProfile::valueOf ($servicesHeading->getPath ($this->_params['id_head']), 'document') >= PROFILE_CCV_MODERATE;

        $tpl->assign ('contribEnabled' , $contribEnabled);
        $tpl->assign ('publishEnabled' , $publishEnabled);
        $tpl->assign ('validEnabled'   , $validEnabled);
        $tpl->assign ('moderateEnabled', $moderateEnabled);
        $tpl->assign ('pasteEnabled'   , CopixActionGroup::process ('document|DocumentAdmin::canPaste', array ('id_head'=>$this->_params['id_head'])));

        if ($moderateEnabled) {
            $tpl->assign ('arDocumentDraft'  , $dao->getDocumentByStatus ($workflow->getDraft ()  ,$this->_params['id_head']));
            $tpl->assign ('arDocumentTrash'  , $dao->getDocumentByStatus ($workflow->getTrash ()  ,$this->_params['id_head']));
            $tpl->assign ('arDocumentRefuse' , $dao->getDocumentByStatus ($workflow->getRefuse () ,$this->_params['id_head']));
            $tpl->assign ('arDocumentPropose', $dao->getDocumentByStatus ($workflow->getPropose (),$this->_params['id_head']));
            $tpl->assign ('arDocumentValid'  , $dao->getDocumentByStatus ($workflow->getValid ()  ,$this->_params['id_head']));
        }elseif($publishEnabled) {
            $tpl->assign ('arDocumentDraft'  , $dao->getDocumentByUser ($workflow->getDraft ()  ,$this->_params['id_head']));
            $tpl->assign ('arDocumentTrash'  , $dao->getDocumentByStatusAuthor ($workflow->getTrash ()  ,$this->_params['id_head']));
            $tpl->assign ('arDocumentRefuse' , $dao->getDocumentByUser ($workflow->getRefuse () ,$this->_params['id_head']));
            $tpl->assign ('arDocumentPropose', $dao->getDocumentByStatus ($workflow->getPropose (),$this->_params['id_head']));
            $tpl->assign ('arDocumentValid'  , $dao->getDocumentByStatus ($workflow->getValid ()  ,$this->_params['id_head']));
        }elseif($validEnabled) {
            $tpl->assign ('arDocumentDraft'  , $dao->getDocumentByUser ($workflow->getDraft ()  ,$this->_params['id_head']));
            $tpl->assign ('arDocumentTrash'  , $dao->getDocumentByStatusAuthor ($workflow->getTrash ()  ,$this->_params['id_head']));
            $tpl->assign ('arDocumentRefuse' , $dao->getDocumentByUser ($workflow->getRefuse () ,$this->_params['id_head']));
            $tpl->assign ('arDocumentPropose', $dao->getDocumentByStatus ($workflow->getPropose (),$this->_params['id_head']));
            $tpl->assign ('arDocumentValid'  , $dao->getDocumentByUser ($workflow->getValid ()  ,$this->_params['id_head']));
        }elseif($contribEnabled) {
            $tpl->assign ('arDocumentDraft'  , $dao->getDocumentByUser ($workflow->getDraft ()  ,$this->_params['id_head']));
            $tpl->assign ('arDocumentTrash'  , $dao->getDocumentByStatusAuthor ($workflow->getTrash ()  ,$this->_params['id_head']));
            $tpl->assign ('arDocumentRefuse' , $dao->getDocumentByUser ($workflow->getRefuse () ,$this->_params['id_head']));
            $tpl->assign ('arDocumentPropose', $dao->getDocumentByUser ($workflow->getPropose (),$this->_params['id_head']));
            $tpl->assign ('arDocumentValid'  , $dao->getDocumentByUser ($workflow->getValid ()  ,$this->_params['id_head']));
        }else{
            $toReturn = '';
            return true;
        }

        $tpl->assign ('arDocumentPublish', $dao->getDocumentByStatus ($workflow->getPublish (),$this->_params['id_head']));
        $toReturn = $tpl->fetch ('document.adminheading.tpl');
        return true;
    }
}
?>
