<?php
/**
* @package   copix
* @subpackage core
* @author   Croes Gérald
* @copyright CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Classe de base pour les plugins
* 
* @package copix
* @subpackage core
*/
abstract class CopixPlugin {
    /**
    * Objet de configuration dont la classe à pour nom  nom.plugin.conf.php (nommage par défaut)
    * @var object
    */
    protected $config;
    
    /**
     * Récupération de l'objet de configuration du plugin
     * @return PluginConf
     */
    public function getConfig (){
    	return $this->config;
    }

    /**
    * constructeur
    * @param   object   $config      objet de configuration du plugin
    */
    public function __construct ($config = null){
        $this->config = $config;
    }

    /**
    * Méthode exécutée avant l'appel à session_start
    */
    public function beforeSessionStart (){}

    /**
    * traitements à faire avant execution de l'action demandée
    * @param   CopixAction   $action   le descripteur de l'action demandée.
    */
    public function beforeProcess (& $action){}

    /**
    * traitements à faire apres execution de l'action
    * @param CopixActionReturn      $actionreturn
    */
    public function afterProcess ($actionreturn){}

    /**
    * Traitements à effectuer sur le contenu qui s'appréte à être affiché
    */
    public function beforeDisplay (& $display){}
}

/**
* Fabrique de plugin
* @package copix
* @subpackage core
*/
class CopixPluginRegistry {
	/**
	 * Les plugins créés
	 * @var array
	 */
	private static $_plugins = array ();

	/**
	 * Récupération d'un plugin
	 * @param	string	$pPluginName	Nom du plugin
	 * @param 	boolean	$pRequired		Si le plugin est nécessaire au fonctionnement de la suite (génère une exception si le plugin n'est pas trouvé)
	 * @return CopixPlugin
	 */
	public static function get ($pPluginName, $pRequired = false){
		$pPluginName = strtolower ($pPluginName);
    	
		if (!self::isRegistered ($pPluginName)){
			if ($pRequired){
				throw new Exception ('Plugin '.$pPluginName.' requis');
			}else{
				return null;
			}
		}

		if (!isset (self::$_plugins[$pPluginName])){
			self::$_plugins[$pPluginName] = self::_create ($pPluginName);
		}
		return self::$_plugins[$pPluginName];
	}
	
	/**
	 * Récupération de la configuration pour un plugin donné
	 * 
	 * @param	string	$pPluginName	Nom du plugin
	 * @param 	boolean	$pRequired		Si le plugin est nécessaire au fonctionnement de la suite (génère une exception si le plugin n'est pas trouvé)
	 * @return CopixPluginConfig	 
	 */
	public static function getConfig ($pPluginName, $pRequired = false){
		if ($element = self::get ($pPluginName, $pRequired)){
			return $element->getConfig ();
		}
		return null;
	}

    /**
    * instanciation d'un objet plugin.
    * instancie également l'objet de configuration associé
    * @param   string   $name   nom du plugin
    * @param string $conf   nom d'un fichier de configuration alternatif. si chaine vide = celui par défaut
    * @return   CopixPlugin      le plugin instancié
    */
    private static function _create ($name){
    	$fic  = new CopixModuleFileSelector ($name);
    	$nom  = strtolower ($fic->fileName);
    	
    	$path = $fic->getPath (COPIX_PLUGINS_DIR) .$nom.'/';
    	$path_plugin = $path . $nom . '.plugin.php';
    	if (!Copix::RequireOnce ($path_plugin)){
			throw new Exception ($path_plugin);    		
    	}    	
    	
    	$config = self::_loadConfig($name);
    	$pluginClassName = 'Plugin'.$fic->fileName;
    	return new $pluginClassName ($config);//nouvel objet plugin, on lui passe en paramètre son objet de configuration.
    }
    
    /**
     * Charge la configuration d'un plugin.
     * 
     * S'il n'existe pas de configuration par défaut, renvoie null. Sinon:
     * 
     * Cherche le fichier de configuration dans var/config/plugins/MODULE/PLUGIN.plugin.conf.php.
     * Si le fichier n'existe pas il est crée avec un contenu par défaut.
     *
     * @param string $pluginName Sélecteur du plugin.
     * @return mixed L'objet configuration ou NULL si le plugin n'a pas de configuration.
     */
    static private function _loadConfig($pluginName) {
		$fic  = new CopixModuleFileSelector ($pluginName);
		$nom  = strtolower ($fic->fileName);
		
		$config_class = "PluginConfig".$fic->fileName; 
		
		// Vérifie la présence d'une configuration "old school"
		$old_config_path = $fic->getPath (COPIX_PLUGINS_DIR) .$nom. '/' . $nom . '.plugin.conf.php';
		if(file_exists($old_config_path)) {
			// Ce plugin utilise l'ancien système de configuration
			_log($pluginName." utilise un fichier de configuration dans les sources !", "plugin", CopixLog::WARNING);
			Copix::RequireOnce($old_config_path);
			$config = new $config_class();
			return $config;
		}
	
		// Vérifie la présence d'une configuration par défaut
		$default_config_path = self::_getDefaultConfigPath($pluginName);
		$default_config_class = "PluginDefaultConfig".$fic->fileName; 
		if(!file_exists($default_config_path)) {
			// Pas de configuration
			return null;
		}
	    	
	    // Cherche la configuration actuelle  
		$config_path = $fic->getOverloadedPath(COPIX_VAR_PATH.'config/plugins/') . $nom . '.plugin.conf.php';
		if(!file_exists($config_path)) {
			// Génère une configuration par défaut
			_log($pluginName.": création de la configuration par défaut ($config_path)", "plugin");			
			CopixFile::write($config_path, 
				"<?php\n".
				"CopixPluginRegistry::requireDefaultConfig('$pluginName');\n".
				"class $config_class extends $default_config_class {\n".
				"\t// Surchargez la configuration ici.\n".
				"}\n".
				"?>"
			);
		}
	    	
		// Charge la configuration
		Copix::RequireOnce($config_path);
		$config = new $config_class ();

		return $config;
    }
    
    /**
     * Charge la configuration par défaut d'un plugin.
     * 
     * Cette méthode est surtout destinée à être appelée depuis les fichiers de configuration réels.
     * 
     * @param string $pluginName Sélecteur du plugin.
     */
    static public function requireDefaultConfig($pluginName) {
	Copix::RequireOnce(self::_getDefaultConfigPath($pluginName));
    }
    
    /**
     * Calcule le chemin de la configuration par défaut d'un plugin.
     * 
     * @param string $pluginName Sélecteur du plugin.
     * @return Le chemin de la configuration par défaut, généralement MODULE/plugins/PLUGIN/PLUGIN.pluigin.default.conf.php.
     */
    static private function _getDefaultConfigPath($pluginName) {
	    $fic  = new CopixModuleFileSelector ($pluginName);
	    $nom  = strtolower ($fic->fileName);    	
	    return $fic->getPath (COPIX_PLUGINS_DIR) . $nom. '/' . $nom . '.plugin.default.conf.php';    
    }
    
    /**
     * Retourne la liste des plugins enregistrés.
     * @return array of CopixPlugin
     */
    static public function getRegistered (){
    	$arPlugins = array ();
    	foreach (CopixConfig::instance ()->plugin_getRegistered() as $name){
    		$arPlugins [] = self::get ($name, true);
    	}
    	return $arPlugins;
    }
    
    /**
     * Permet de savoir si un plugin est register
     *
     * @param string $pPluginName le plugin a tester
     * @return bool
     */
    static public function isRegistered ($pPluginName) {
    	return in_array (strtolower ($pPluginName), 
    			CopixConfig::instance ()->plugin_getRegistered ());
    }
    
    /**
     * Retourne la liste des plugins que l'on peut enregistrer
     * @return	array	 
     */
    static public function getAvailable (){
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
		
		//recherche des plugins configurés dans les répertoires de modules
		foreach (CopixModule::getList () as $moduleName) {
			foreach (self::_findPluginsIn (CopixModule::getPath ($moduleName).'plugins/', $moduleName) as 
				$pluginName){
				$toReturn[] = $pluginName;
			}
		}

		return $toReturn;
    }
    
    /**
     * Cherche des plugins dans un répertoire donné.
     * @param	string	$pPath 	Le chemin dans lequel on va chercher les plugins
     * @param	string	$pModuleName	Le nom du module à qui corresponds le répertoire de recherche.
     * 			Si donné, alors on préfixera le nom du plugin trouvé par $pModuleName|
     * @return array	tableau de nom de plugins qui ont étés trouvé dans le chemin
     */
    static private function _findPluginsIn ($pPath, $pModuleName = null){
    	//On indique quel est le module
    	if ($pModuleName !== null){
    		$pModuleName .= '|';
    	}else{
    		$pModuleName = '';
    	}

		//Parcours du répertoire à la recherche des fichiers .plugin.php
    	$toReturn = array ();
		if ($dir = @opendir ($pPath)){
			while (false !== ($file = readdir($dir))) {
				if (file_exists ($pPath.$file.'/'.$file.'.plugin.php')){
					$toReturn[] = $pModuleName . $file; 
				}
			}
			closedir ($dir);
		}
		clearstatcache ();
		return $toReturn;
    }
}
?>
