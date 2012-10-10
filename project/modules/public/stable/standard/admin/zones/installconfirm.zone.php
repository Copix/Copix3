<?php
/**
 * @package standard
 * @subpackage admin 
* 
* @author	Bertrand Yan, Croes Gérald
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * zone installfront, formulaire pour renseigner la connexion à la base de donnée.
 * @package standard
 * @subpackage admin 
 * 
 */
class ZoneInstallConfirm extends CopixZone {
	function _createContent (&$toReturn) {
		$tpl = new CopixTpl ();
		$toAdd = $this->_params['toAdd'];
		$toDelete = $this->_params['toDelete'];
		
      foreach($toAdd as $key=>$elem) {
         $toAdd[$key] = CopixModule::getInformations($elem);
      }

      foreach($toDelete as $key=>$elem) {
         $toDelete[$key] = CopixModule::getInformations($elem);
      }
      
      $tpl->assign('toAdd',$toAdd);
      $tpl->assign('toDelete',$toDelete);
      $toReturn = $tpl->fetch ('install.confirm.tpl');
		return true;
	}
}
?>