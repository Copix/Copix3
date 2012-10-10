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
   		$path_config = $path . $nom . '.plugin.conf.php';
   		if (file_exists ($path_config)){
   			Copix::RequireOnce ($path_config);
	    	$configClassName = 'PluginConfig'.$fic->fileName; //nom de la classe de configuration.
    	}else{
    		$configClassName = null;
    		$config = null;
    	}
    	if (!Copix::RequireOnce ($path_plugin)){
			throw new Exception ($path_plugin);    		
    	}
		if ($configClassName !== null){
			$config    = new $configClassName ();//en deux étapes, impossible de mettre la ligne dans les paramètres du constructeur.		
		}
    	$pluginClassName = 'Plugin'.$fic->fileName;
    	return new $pluginClassName ($config);//nouvel objet plugin, on lui passe en paramètre son objet de configuration.
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
		
		//recherche des plugins dans les répertoires configurés à cet effet.
   		foreach ($conf->arPluginsPath as $path){
   		    if (substr ($path, -1) != '/') {
   		       $path .= '/';
   		    }
   			foreach (self::_findPluginsIn ($path) as $pluginName){
				$toReturn[] = $pluginName;
   			}
		}
		
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