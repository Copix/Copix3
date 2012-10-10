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
* Zone d'affichage pour la proposition d'image.
*/
class ZoneEditPicture extends CopixZone {
   function _createContent (&$toReturn){
      $tpl = & new CopixTpl ();
      $dao = & CopixDAOFactory::getInstanceOf ('picturesheadings');
      
      $workflow = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
      if (CopixConfig::get ('pictures|easyWorkflow') == 1){
         $bestActionCaption = $workflow->getCaption($workflow->getBest($this->_params['toEdit']->id_head,'pictures'));
      }else{
         $bestActionCaption = $workflow->getCaption($workflow->getNext($this->_params['toEdit']->id_head,'pictures',$this->_params['toEdit']->status_pict));
      }
      $tpl->assign ('WFLBestActionCaption', $bestActionCaption);
      
      //envoie de tous les themes, categories et formats
      $tpl->assign ('themeList' ,$this->_getThemes ());
      $tpl->assign ('showErrors',$this->_params['e']);
      $tpl->assign ('headingProperties' ,$dao->get ($this->_params['toEdit']->id_head));
      $tpl->assign ('formatList' ,explode (';', CopixConfig::get ('pictures|format')));
      $tpl->assign ('heading'   ,$this->_params['heading']);
      //dao error or something else
      if ($this->_params['e']) {
         if (isset($this->_params['toEdit']->errors) && count($this->_params['toEdit']->errors)) {
            $tpl->assign ('errors' ,$this->_params['toEdit']->errors);
         }else{
         	$dao = & CopixDAOFactory::getInstanceOf ('pictures');
            $tpl->assign ('errors' ,$dao->check ($this->_params['toEdit']));
         }
      }
      $tpl->assign ('toEdit'    ,$this->_params['toEdit']);
      $tpl->assign ('max_upload_size',ini_get('upload_max_filesize'));

      $toReturn = $tpl->fetch ('pictures.edit.tpl');
      return true;
   }
   
   function _getThemes () {
      $daoPictureThemes   = & CopixDAOFactory::getInstanceOf ('picturesthemes');
      return $daoPictureThemes->findAll ();
   }
}
?>