<?php
/**
* @package cms
* @subpackage	document
* @author	Bertrand Yan, Croes Gérald
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package	cms
* @subpackage document
* ZoneDocumentEdit
*/
class ZoneDocumentEdit extends CopixZone {
	function _createContent (&$toReturn) {
      $tpl = & new CopixTpl ();
      
      $workflow = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
      if (CopixConfig::get ('document|easyWorkflow') == 1){
         $bestActionCaption = $workflow->getCaption($workflow->getBest($this->_params['toEdit']->id_head,'document'));
      }else{
         $bestActionCaption = $workflow->getCaption($workflow->getNext($this->_params['toEdit']->id_head,'document',$this->_params['toEdit']->status_doc));
      }
      $tpl->assign ('WFLBestActionCaption', $bestActionCaption);
      
      $tpl->assign ('showErrors',$this->_params['e']);
      //dao error or something else
      if (isset($this->_params['toEdit']->errors)) {
         $tpl->assign ('errors' ,$this->_params['toEdit']->errors);
      }else{
      	$dao = CopixDAOFactory::getInstanceOf ('Document');
         $tpl->assign ('errors' ,$dao->check ($this->_params['toEdit']));
      }
      $tpl->assign ('toEdit'    ,$this->_params['toEdit']);
      $tpl->assign ('max_upload_size',ini_get('upload_max_filesize'));

      $toReturn = $tpl->fetch ('document.edit.tpl');
      return true;
	}

}
?>
