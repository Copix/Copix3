<?php
/**
* @package	cms
* @author	Croes Gérald
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
CopixClassesFactory::fileInclude ('cms|ServicesCMSPage');

/**
* @package cms 
* Administration pannel
* @param id_head // the current copixheading indice can be null if racine
*/
class ZoneCmsAdminHeading extends CopixZone {
   function _createContent (&$toReturn) {
      //Getting the user.
      CopixClassesFactory::fileInclude ('cms|CMSAuth');
      $user = CMSAuth::getUser ();

      //Create Services, and DAO
      $tpl = new CopixTpl ();
      $workflow         = CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
      $dao              = CopixDAOFactory::getInstanceOf ('CMSPage');

      // assign id of the current CopixHeadings
      $tpl->assign ('id_head', $this->_params['id_head']);

      //capability
      $contribEnabled  = CMSAUTH::canWrite ($this->_params['id_head']);
      $publishEnabled  = CMSAuth::canPublish ($this->_params['id_head']);
      $validEnabled    = CMSAUTH::canValidate ($this->_params['id_head']);
      $moderateEnabled = CMSAuth::canModerate ($this->_params['id_head']);

      $tpl->assign ('contribEnabled' , $contribEnabled);
      $tpl->assign ('publishEnabled' , $publishEnabled);
      $tpl->assign ('validEnabled'   , $validEnabled);
      $tpl->assign ('moderateEnabled', $moderateEnabled);
      $tpl->assign ('pasteEnabled',    CopixActionGroup::process ('cms|CMSAdmin::canPaste', array ('id_head'=>$this->_params['id_head'])));

      if ($moderateEnabled) {
         $tpl->assign ('arCMSPageDraft'  , $dao->findByStatusIn ($workflow->getDraft ()  ,$this->_params['id_head']));
         $tpl->assign ('arCMSPageTrash'  , $dao->findByStatusIn ($workflow->getTrash ()  ,$this->_params['id_head']));
         $tpl->assign ('arCMSPageRefuse' , $dao->findByStatusIn ($workflow->getRefuse () ,$this->_params['id_head']));
         $tpl->assign ('arCMSPagePropose', $dao->findByStatusIn ($workflow->getPropose (),$this->_params['id_head']));
         $tpl->assign ('arCMSPageValid'  , $dao->findByStatusIn ($workflow->getValid ()  ,$this->_params['id_head']));
      }elseif($publishEnabled) {
         $tpl->assign ('arCMSPageDraft'  , $dao->findByAuthorStatusIn ($user->login, $workflow->getDraft ()  ,$this->_params['id_head']));
         $tpl->assign ('arCMSPageTrash'  , $dao->findByAuthorStatusIn ($user->login, $workflow->getTrash ()  ,$this->_params['id_head']));
         $tpl->assign ('arCMSPageRefuse' , $dao->findByStatusIn ($workflow->getRefuse () ,$this->_params['id_head']));
         $tpl->assign ('arCMSPagePropose', $dao->findByStatusIn ($workflow->getPropose (),$this->_params['id_head']));
         $tpl->assign ('arCMSPageValid'  , $dao->findByStatusIn ($workflow->getValid ()  ,$this->_params['id_head']));
      }elseif($validEnabled) {
         $tpl->assign ('arCMSPageDraft'  , $dao->findByAuthorStatusIn ($user->login, $workflow->getDraft ()  ,$this->_params['id_head']));
         $tpl->assign ('arCMSPageTrash'  , $dao->findByAuthorStatusIn ($user->login, $workflow->getTrash ()  ,$this->_params['id_head']));
         $tpl->assign ('arCMSPageRefuse' , $dao->findByStatusIn ($workflow->getRefuse () ,$this->_params['id_head']));
         $tpl->assign ('arCMSPagePropose', $dao->findByStatusIn ($workflow->getPropose (),$this->_params['id_head']));
         $tpl->assign ('arCMSPageValid'  , $dao->findByAuthorStatusIn ($user->login, $workflow->getValid ()  ,$this->_params['id_head']));
      }elseif($contribEnabled) {
         $tpl->assign ('arCMSPageDraft'  , $dao->findByAuthorStatusIn ($user->login, $workflow->getDraft ()  ,$this->_params['id_head']));
         $tpl->assign ('arCMSPageTrash'  , $dao->findByAuthorStatusIn ($user->login, $workflow->getTrash ()  ,$this->_params['id_head']));
         $tpl->assign ('arCMSPageRefuse' , $dao->findByAuthorStatusIn ($user->login, $workflow->getRefuse () ,$this->_params['id_head']));
         $tpl->assign ('arCMSPagePropose', $dao->findByAuthorStatusIn ($user->login, $workflow->getPropose (),$this->_params['id_head']));
         $tpl->assign ('arCMSPageValid'  , $dao->findByAuthorStatusIn ($user->login, $workflow->getValid ()  ,$this->_params['id_head']));
      }else{
         $toReturn = '';
         return true;
      }
      $tpl->assign ('arCMSPagePublish',    $dao->findByStatusIn ($workflow->getPublish(), $this->_params['id_head']));
      $toReturn = $tpl->fetch ('cms.adminheading.tpl');
      return true;
   }
}
?>