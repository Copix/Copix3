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
 * Actiongroup pour gérer les envois de CopixList
 * @package		standard 
 * @subpackage	generictools
 */
class ActionGroupCopixList extends CopixActionGroup {
	public function processOrderBy () {
		$ppo = new CopixPPO ();
		$list = CopixListFactory::get (_request ('currentList'));
		$list->setOrderBy (_request ('field'));
		$list->setPage ('first');
		if (CopixAJAX::isAJAXRequest()) {
			$ppo->MAIN = $list->getTable ();
			return _arDirectPpo ($ppo, 'generictools|blank.tpl');
		} else {
			return _arRedirect ($list->getListUrl ());
		}
	}
	
	public function processGoTo () {
		$ppo = new CopixPPO ();
		$list = CopixListFactory::get (_request ('currentList'));
		$list->setPage (_request ('kind'));
		if (CopixAJAX::isAJAXRequest()) {
			$ppo->MAIN = $list->getTable ();
			return _arDirectPpo ($ppo, 'generictools|blank.tpl');
		} else {
			return _arRedirect ($list->getListUrl ());
		}
	}
	
}