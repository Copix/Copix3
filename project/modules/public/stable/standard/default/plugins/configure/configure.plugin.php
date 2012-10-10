<?php
/**
* @package		standard
* @subpackage	plugin_configure
* @author		Croës Gérald
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Plugin qui permet de vérfier / d'aider à la configuration de Copix
 * @package standard
 * @subpackage plugin_configure
 */
class PluginConfigure extends CopixPlugin {
	/**
	 * Traitements avant le demarrage de la session
	 */
	function beforeSessionStart (){
		if (is_array ($errors = $this->_checkDirectories ())){
			$this->_showDie ($errors);
		}
	}
	
	/**
	 * Vérification des répertoires en écriture
	 */
	private function _checkDirectories (){
		$badfiles   = array();
		foreach($this->config->getDirectories () as $file){
			if (!file_exists ($file)){
				@mkdir($file, 0771);
			}

			if(!is_writable($file)){
				$badfiles[]= $file;
			}
		}
		return count ($badfiles) > 0 ? $badfiles : true;
	}
	
	/**
	 * Vérifie si la version de PHP est correcte pour pouvoir exécuter Copix
	 */
	private function checkVersion (){
		if( (!function_exists('version_compare')) || version_compare(PHP_VERSION, '5.1') == -1 ){
			$canInstall = false;
			$messages[]='La version de PHP minimale à utiliser avec Copix 3.0 est la 5.1';
		}
	}
	
	/**
	 * Vérifie si PDO est activé
	 * @todo à implémenter
	 */
	private function checkPDO (){
	}
	
	/**
	 * Affiche une liste d'erreur et quitte l'application
	 *
	 * @param array $pArrayOfErrors tableau d'erreurs à afficher
	 */
	private function _showDie ($pArrayOfErrors){
		echo "Pour pouvoir utiliser Copix, veuillez corriger ces problèmes avant de continuer :";
		echo "<ul>";
		foreach ($pArrayOfErrors as $error){
			echo "<li>$error</li>";
		}
		echo "</ul>";
		exit;
	}
}
?>