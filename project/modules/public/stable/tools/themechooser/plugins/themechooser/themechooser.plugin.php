<?php
/**
 * @package		tools
 * @subpackage	themechooser
 * @author		Salleyron Julien
 * @copyright	CopixTeam
 * @link			http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Plugin qui permet d'associer un thème à un utilisateur
 * @package		tools
 * @subpackage	themechooser
 */
class PluginThemeChooser extends CopixPlugin {
	public function beforeProcess (){
		if (in_array (CopixSession::get ('themechooser|theme'), $this->config->getThemeList ())) {
			CopixTpl::setTheme (CopixSession::get ('themechooser|theme'));
		}
	}
}
?>