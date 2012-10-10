<?php
/**
 * @package		tutorials
 * @subpackage 	news_2
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Groupe d'action appelé par défaut dans le module.
 * @package	tutorials
 * @subpackage	news_2
 */
class ActionGroupDefault extends CopixActionGroup {
	/**
	 * Page appelée par défaut dans le module
	 */
	public function processDefault (){
		$ppo = new CopixPpo ();
		$ppo->TITLE_PAGE = 'Liste des nouvelles';
		$ppo->arNews = _dao ('news_2')->findAll ();

		return _arPpo ($ppo, 'news.list.tpl');
	}
}
?>