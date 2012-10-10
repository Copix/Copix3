<?php
/**
 * @package copix
 * @subpackage plugin
 * @author Croes Gérald, Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Fabrique de plugin
 * 
 * @package copix
 * @subpackage plugin
 */
class CopixPluginRegistry {
	/**
	 * Plugins créés
	 * 
	 * @var array
	 */
	private static $_plugins = array ();

	/**
	 * Retourne un plugin
	 * 
	 * @param string $pPluginName Nom du plugin
	 * @param boolean $pRequired Si le plugin est nécessaire au fonctionnement de la suite (génère une exception si le plugin n'est pas trouvé)
	 * @return CopixPlugin
	 * @throws CopixPluginException Le plugin est requis, mais non enregistré, code CopixPluginException::REQUIRED
	 */
	public static function get ($pPluginName, $pRequired = false) {
		$pPluginName = strtolower ($pPluginName);

		if (!self::isRegistered ($pPluginName)) {
			if ($pRequired) {
				throw new CopixPluginException (_i18n ('copix:copixplugin.error.required', $pPluginName), CopixPluginException::REQUIRED);
			} else {
				return null;
			}
		}

		if (!isset (self::$_plugins[$pPluginName])) {
			self::$_plugins[$pPluginName] = self::_create ($pPluginName);
		}
		return self::$_plugins[$pPluginName];
	}
	
	/**
	 * Retourne la configuration pour un plugin donné
	 * 
	 * @param string $pPluginName Nom du plugin
	 * @param boolean $pRequired Si le plugin est nécessaire au fonctionnement de la suite (génère une exception si le plugin n'est pas trouvé)
	 * @return CopixPluginConfig	 
	 */
	public static function getConfig ($pPluginName, $pRequired = false) {
		if ($element = self::get ($pPluginName, $pRequired)) {
			return $element->getConfig ();
		}
		return null;
	}

	/**
	 * Instanciation d'un objet plugin et l'objet de configuration associé
	 * 
	 * @param string $pPluginName nom du plugin
	 * @return CopixPlugin
	 * @throws CopixPluginException Plugin non trouvé, code CopixPluginException::NOT_FOUND
	 */
	private static function _create ($pPluginName) {
		$fic = new CopixModuleFileSelector ($pPluginName);
		$nom = strtolower ($fic->fileName);
		
		$path = $fic->getPath (COPIX_PLUGINS_DIR) . $nom . '/';
		$path_plugin = $path . $nom . '.plugin.php';
		if (!Copix::RequireOnce ($path_plugin)) {
			throw new CopixPluginException (
				_i18n ('copix:copixplugin.error.notFound', array ($path_plugin, $fic->module)),
				CopixPluginException::NOT_FOUND
			);			
		}		
		
		$config = self::_loadConfig ($pPluginName);
		$pluginClassName = 'Plugin' . $fic->fileName;
		//nouvel objet plugin, on lui passe en paramètre son objet de configuration.
		return new $pluginClassName ($config);
	}
	
	/**
	 * Charge la configuration d'un plugin 
	 * S'il n'existe pas de configuration par défaut, renvoie null
	 * Sinon, cherche le fichier de configuration dans var/config/plugins/MODULE/PLUGIN.plugin.conf.php
	 * Si le fichier n'existe pas il est créé avec un contenu par défaut
	 *
	 * @param string $pPluginName Sélecteur du plugin
	 * @return mixed L'objet configuration ou NULL si le plugin n'a pas de configuration.
	 */
	private static function _loadConfig ($pPluginName) {
		$fic = new CopixModuleFileSelector ($pPluginName);
		$nom = strtolower ($fic->fileName);
		
		$config_class = 'PluginConfig' . $fic->fileName; 
		
		// Vérifie la présence d'une configuration "old school"
		$old_config_path = $fic->getPath (COPIX_PLUGINS_DIR) . $nom . '/' . $nom . '.plugin.conf.php';
		if (file_exists ($old_config_path)) {
			// Ce plugin utilise l'ancien système de configuration
			_log ($pPluginName." utilise un fichier de configuration dans les sources !", "plugin", CopixLog::WARNING);
			Copix::RequireOnce ($old_config_path);
			$config = new $config_class ();
			return $config;
		}
	
		// Vérifie la présence d'une configuration par défaut
		$default_config_path = self::_getDefaultConfigPath ($pPluginName);
		$default_config_class = 'PluginDefaultConfig' . $fic->fileName; 
		if (!file_exists ($default_config_path)) {
			// Pas de configuration
			return null;
		}
			
		// Cherche la configuration actuelle
		$config_path = $fic->getOverloadedPath (COPIX_VAR_PATH . 'config/plugins/') . $nom . '.plugin.conf.php';
		if (!file_exists ($config_path)) {
			// Génère une configuration par défaut
			_log ($pPluginName.": création de la configuration par défaut ($config_path)", "plugin");			
			CopixFile::write ($config_path, 
				"<?php\n".
				"CopixPluginRegistry::requireDefaultConfig ('$pPluginName');\n".
				"class $config_class extends $default_config_class {\n".
				"\t// Surchargez la configuration ici.\n".
				"}\n".
				"?>"
			);
		}
			
		// Charge la configuration
		Copix::RequireOnce ($config_path);
		$config = new $config_class ();
		return $config;
	}
	
	/**
	 * Charge la configuration par défaut d'un plugin, surtout destinée à être appelée depuis les fichiers de configuration réels
	 * 
	 * @param string $pPluginName Sélecteur du plugin
	 */
	public static function requireDefaultConfig ($pPluginName) {
		Copix::RequireOnce (self::_getDefaultConfigPath ($pPluginName));
	}
	
	/**
	 * Retourne le chemin de la configuration par défaut d'un plugin
	 * 
	 * @param string $pPluginName Sélecteur du plugin.
	 * @return Le chemin de la configuration par défaut, généralement MODULE/plugins/PLUGIN/PLUGIN.pluigin.default.conf.php.
	 */
	private static function _getDefaultConfigPath ($pPluginName) {
		$fic = new CopixModuleFileSelector ($pPluginName);
		$nom = strtolower ($fic->fileName);		
		return $fic->getPath (COPIX_PLUGINS_DIR) . $nom . '/' . $nom . '.plugin.default.conf.php';	
	}
	
	/**
	 * Retourne la liste des plugins enregistrés
	 * 
	 * @return CopixPlugin[]
	 */
	public static function getRegistered () {
		$arPlugins = array ();
		foreach (CopixConfig::instance ()->plugin_getRegistered () as $name) {
			$arPlugins[] = self::get ($name, true);
		}
		return $arPlugins;
	}
	
	/**
	 * Indique si un plugin est enregistré
	 *
	 * @param string $pPluginName Plugin à tester
	 * @return boolean
	 */
	public static function isRegistered ($pPluginName) {
		return in_array (strtolower ($pPluginName), CopixConfig::instance ()->plugin_getRegistered ());
	}
	
	/**
	 * Retourne la liste des plugins que l'on peut enregistrer
	 * 
	 * @return array	 
	 */
	public static function getAvailable () {
		$conf = CopixConfig::instance ();
		$toReturn = array ();
		
		/* TODO: arPluginsPath désactivé jusqu'à ce qu'on l'implémente vraiment, cf #151.
		//recherche des plugins dans les répertoires configurés à cet effet.
		foreach ($conf->arPluginsPath as $path){
			if (substr ($path, -1) != '/') {
				$path .= '/';
			}
			foreach (self::_findPluginsIn ($path) as $pluginName){
				$toReturn[] = $pluginName;
			}
		}
		*/
		
		// recherche des plugins configurés dans les répertoires de modules
		foreach (CopixModule::getList () as $moduleName) {
			foreach (self::_findPluginsIn (CopixModule::getPath ($moduleName) . COPIX_PLUGINS_DIR, $moduleName) as $pluginName) {
				$toReturn[] = $pluginName;
			}
		}

		return $toReturn;
	}
	
	/**
	 * Retourne les plugins dans un répertoire donné
	 * 
	 * @param string $pPath Chemin dans lequel on va chercher les plugins
	 * @param string $pModuleName Module à qui corresponds le répertoire de recherche, si donné on préfixera le nom du plugin trouvé par $pModuleName|
	 * @return array
	 */
	private static function _findPluginsIn ($pPath, $pModuleName = null) {
		//On indique quel est le module
		if ($pModuleName !== null) {
			$pModuleName .= '|';
		} else {
			$pModuleName = '';
		}

		//Parcours du répertoire à la recherche des fichiers .plugin.php
		$toReturn = array ();
		if ($dir = @opendir ($pPath)) {
			while (false !== ($file = readdir ($dir))) {
				if (file_exists ($pPath.$file . '/' . $file . '.plugin.php')) {
					$toReturn[] = $pModuleName . $file; 
				}
			}
			closedir ($dir);
		}
		clearstatcache ();
		return $toReturn;
	}
}