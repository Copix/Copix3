<?php
/**
 * @package		standard
 * @subpackage	plugin_autoloadZF
 * @author		Favre Brice
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
* @package		standard
* @subpackage	plugin_autoloadZF
*/
class PluginAutoloadZf extends CopixPlugin {
	
	/**
	 * Inclusion de la fonction d'autoload ZF avant le démarrage de la session
	 */
	public function beforeSessionStart () {
		if (@include_once('Zend/Loader.php')){
		   spl_autoload_register(array('Zend_Loader', 'loadClass'));
		}
	}
	
}
?>