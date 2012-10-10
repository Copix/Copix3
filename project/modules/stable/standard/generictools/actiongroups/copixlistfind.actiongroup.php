<?php
/**
 * @package		standard 
 * @subpackage	generictools
* @author	Salleyron Julien
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
* @experimental
*/

/**
 * Actiongroup pour gérer les envois de CopixList (extends de CopixFormActionGroup car il récupère le formulaire
 * @package		standard 
 * @subpackage	generictools
 */
class ActionGroupCopixListFind extends CopixFormActionGroup {
	public function processFind () {
		$ppo = new CopixPPO ();
		$list = CopixListFactory::get (_request ('currentList'));
		$list->setPage ('first');
		if (CopixAJAX::isAJAXRequest()) {
			$ppo->MAIN = $list->getTable ();
			return _arDirectPpo ($ppo, 'generictools|blank.tpl');
		} else {
			return _arRedirect ($list->getListUrl ());
		}
	}
	
	
}

?>