<?php
/**
* @package   standard
* @subpackage plugin_theme_module
* @author   Croes Gérald, Salleyron Julien
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Classe de configuration pour le plugin
 * @package   standard
 * @subpackage plugin_theme_module
 */
class PluginConfigTheme_Module {
	private $_themeForModule = array ();
	
	public function __construct (){
		$this->_themeForModule = array ('bench_news'=>'bench', 'simpletest'=>'ete');
	}
	
	/**
	 * Récupération du thème configuré pour le module donné
	 */
	public function getThemeFor ($pModule){
		$pModule = strtolower ($pModule);
		return isset ($this->_themeForModule[$pModule]) ? $this->_themeForModule[$pModule] : null;
	}
}
?>
