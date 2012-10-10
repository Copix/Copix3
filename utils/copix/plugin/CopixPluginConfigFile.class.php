<?php
/**
 * @package copix
 * @subpackage log
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Gère le fichier de configuration des plugins
 *
 * @package copix
 * @subpackage logs
 */
class CopixPluginConfigFile extends CopixConfigFile {
	/**
	 * Variables définies dans le fichier de config
	 * 
	 * @var array
	 */
	private static $_vars = array ('_plugins');

	/**
	 * Retourne le chemin vers le fichier de configuration
	 *
	 * @return string
	 */
	public static function getPath () {
		return COPIX_VAR_PATH . 'config/plugins.conf.php';
	}

	/**
	 * Définit les plugins activés
	 *
	 * @param array $pPlugins Clef : index, valeur : module|plugin
	 */
	public static function set ($pPlugins) {
		self::_edit (self::getPath (), '_plugins', $pPlugins, self::$_vars);
	}

	/**
	 * Désactive le plugin
	 *
	 * @param string $pName Nom
	 */
	public static function disable ($pName) {
		if (!in_array ($pName, CopixPluginregistry::getAvailable ())) {
			throw new CopixConfigFileException (_i18n ('copix:copix.error.plugin.notFound', $pName));
		}
		
		$plugins = self::_getValue (self::getPath (), '_plugins');
		$newPlugins = array ();
		foreach ($plugins as $plugin) {
			if ($plugin != $pName) {
				$newPlugins[] = $plugin;
			}
		}
		self::_edit (self::getPath (), '_plugins', $newPlugins, self::$_vars);
	}

	/**
	 * Active le plugin
	 *
	 * @param string $pName Nom
	 */
	public static function enable ($pName) {
		if (!in_array ($pName, CopixPluginregistry::getAvailable ())) {
			throw new CopixConfigFileException (_i18n ('copix:copix.error.plugin.notFound', $pName));
		}

		$plugins = self::_getValue (self::getPath (), '_plugins');
		if (!in_array ($pName, $plugins)) {
			$plugins[] = $pName;
		}
		self::_edit (self::getPath (), '_plugins', $plugins, self::$_vars);
	}

	/**
	 * Retourne la liste des plugins activés
	 *
	 * @return array
	 */
	public static function getList () {
		return self::_getValue (self::getPath (), '_plugins', array ());
	}
}