<?
/**
* @package	cms
* @subpackage schedule
* @version	$Id: admineditevent.zone.php,v 1.1 2007/04/08 18:08:13 gcroes Exp $
* @author	Bertrand Yan, Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package	cms
* @subpackage schedule
* ZoneAdminEditEvent
*/
class ZoneAdminEditEvent extends CopixZone {
	function _createContent (&$toReturn) {
		$tpl = & new CopixTpl ();
      $dao = & CopixDAOFactory::create ('ScheduleEvents');
      
      $workflow = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
      if (CopixConfig::get ('schedule|easyWorkflow') == 1){
         $bestActionCaption = $workflow->getCaption($workflow->getBest($this->_params['toEdit']->id_head, 'schedule'));
      }else{
         $bestActionCaption = $workflow->getCaption($workflow->getNext($this->_params['toEdit']->id_head, 'schedule', $this->_params['toEdit']->status_evnt));
      }
      $tpl->assign ('WFLBestActionCaption', $bestActionCaption);

      $tpl->assign ('showErrors', 	$this->_params['e']);
      $tpl->assign ('errors',     	$dao->check($this->_params['toEdit']));
      $tpl->assign ('toEdit',     	$this->_params['toEdit']);
      $tpl->assign ('editionKind',  $this->_params['toEdit']->editionkind_evnt);
      $tpl->assign ('subcribedValues', array ('1'=>CopixI18N::get ('copix:common.buttons.yes'), '0'=>CopixI18N::get ('copix:common.buttons.no')));

      $toReturn = $tpl->fetch ('editevent.admin.tpl');
      return true;
	}
}
?>
