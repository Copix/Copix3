<?php
/**
* @package	cms
* @subpackage news
* @author	Bertrand Yan, Croes Gérald
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @package	cms
 * @subpackage news
 * ZoneNewsEdit
 */
class ZoneNewsEdit extends CopixZone {
   function _createContent (&$toReturn) {
      $tpl = & new CopixTpl ();
      $dao = CopixDAOFactory::getInstanceOf ('news');
      
      $workflow = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
      if (CopixConfig::get ('news|easyWorkflow') == 1){
         $bestActionCaption = $workflow->getCaption($workflow->getBest($this->_params['toEdit']->id_head,'news'));
      }else{
         $bestActionCaption = $workflow->getCaption($workflow->getNext($this->_params['toEdit']->id_head,'news',$this->_params['toEdit']->status_news));
      }
      $tpl->assign ('WFLBestActionCaption', $bestActionCaption);

      $tpl->assign ('showErrors' , $this->getParam ('e', false));
      $tpl->assign ('errors'     , $dao->check ($this->_params['toEdit']));
      $tpl->assign ('toEdit'     , $this->_params['toEdit']);
      $tpl->assign ('kind'       , $this->getParam ('kind', 0));
      
      $toReturn = $tpl->fetch ('news.edit.tpl');
      return true;
   }
}
?>
