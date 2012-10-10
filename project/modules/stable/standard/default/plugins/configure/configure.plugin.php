<?php
/**
 * @package standard
 * @subpackage default
 * @author Croës Gérald
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Permet de vérfier / d'aider à la configuration de Copix
 * 
 * @package standard
 * @subpackage default
 */
class PluginConfigure extends CopixPlugin implements ICopixBeforeSessionStartPlugin {
	/**
	 * Retourne le libellé
	 *
	 * @return string
	 */
	public function getCaption () {
		return 'Vérification du serveur pour utiliser Copix';
	}

	/**
	 * Retourne la description
	 *
	 * @return string
	 */
	public function getDescription () {
		return 'Vérifie que le serveur est bien configuré pour que Copix fonctionne :<ul><li>Version de PHP minimum : 5.1</li><li>Droits spécifiques sur des répertoires</li></ul>';
	}

	/**
	 * Traitements avant le demarrage de la session
	 */
	public function beforeSessionStart () {
		$errors = array ();

		// version de PHP
		$errors = array_merge ($errors, $this->_checkVersion ());

		// vérification des répertoires qui demandent des droits spécifiques
		$errors = array_merge ($errors, $this->_checkDirectories ());

		if (count ($errors) > 0) {
			$this->_showDie ($errors);
		}
	}
	
	/**
	 * Vérification des répertoires en écriture
	 */
	private function _checkDirectories () {
		$badfiles = array ();
		foreach ($this->config->getDirectories () as $file) {
			if (!file_exists ($file)) {
				@mkdir ($file, 0771);
			}

			if (!is_writable($file)) {
				$badfiles[] = 'Droit d\'écriture : ' . $file;
			}
		}
		return $badfiles;
	}
	
	/**
	 * Vérifie si la version de PHP est correcte pour pouvoir exécuter Copix
	 */
	private function _checkVersion () {
		$toReturn = array ();
		if ((!function_exists ('version_compare')) || version_compare (PHP_VERSION, '5.1') == -1) {
			$toReturn[] = 'La version de PHP minimale à utiliser avec Copix ' . COPIX_VERSION . ' est la 5.1';
		}
		return $toReturn;
	}
	
	/**
	 * Affiche une liste d'erreur et quitte l'application
	 *
	 * @param array $pArrayOfErrors tableau d'erreurs à afficher
	 */
	private function _showDie ($pArrayOfErrors) {
		echo utf8_decode ("Pour pouvoir utiliser Copix, veuillez corriger ces problèmes avant de continuer :");
		echo "<ul>";
		foreach ($pArrayOfErrors as $error) {
			echo "<li>" . utf8_decode ($error) . "</li>";
		}
		echo "</ul>";
		exit ();
	}
}