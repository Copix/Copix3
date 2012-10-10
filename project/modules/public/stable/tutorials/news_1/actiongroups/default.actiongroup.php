<?php
/**
 * @package		tutorials
 * @subpackage 	news_1
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Groupe d'action appelé par défaut dans le module.
 * @package	tutorials
 * @subpackage	news_1
 */
class ActionGroupDefault extends CopixActionGroup {
	/**
	 * Page appelée par défaut dans le module
	 */
	public function processDefault (){
		return _arPpo (new CopixPpo (array ('TITLE_PAGE'=>'Bienvenue')), 'default.tpl');
	}
}
?>