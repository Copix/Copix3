<?php
/**
 * @package standard
 * @subpackage	plugin_autoloadEZ
 * @author		Favre Brice
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * @package standard
 * @subpackage	plugin_autoloadEZ
 */
class PluginAutoloadEz extends CopixPlugin {
	/**
	 * Inclusion du package EZ avant le démarrage de la session
	 */
	public function beforeSessionStart () {
		if (@include_once ('ezc/Base/base.php')){
		   spl_autoload_register(array('ezcBase', 'autoload'));
		}
	}
}
?>