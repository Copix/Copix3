<?php
/**
* @package		copix
* @subpackage	core
* @author		Croës Gérald, Salleyron Julien
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Classe qui implémente diverses opérations sur les informations de module
 * @package copix
 * @subpackage core
 */
class CopixModule {
	/*
	 * Cache de recherche des modules sur le disque dur
	 * @var array
	 */
	private static $_hdCache = false;
	
	/**
	 * Liste des modules connus
	 * @var array
	 */
	private static $_arModuleList = false;
	
	/**
	 * Indique si une base à été configurée
	 * @return true
	 */
	private static function _dbConfigured (){
		try {
			CopixConfig::instance ()->copixdb_getDefaultProfileName ();
			return true;
		}catch (Exception $e){
			return false;
		}
	}

	/**
    * Supprime le cache de recherche des modules
    */
	public static function reset (){
		$cacheFile = self::_getCompiledFileName ();
		if (is_file($cacheFile)) {
			unlink($cacheFile);
		}
		if (self::_dbConfigured ()){
            _ioDAO ('copix:CopixModule')->deleteBy (_daoSP ());
		}
	}

	/**
    * Récupre la liste des modules
    * Le premier module trouv dans un rpertoire (dans l'ordre de dclaration des rpertoires fait foi) 
    * 
    * @param boolean $restrictedList Si l'on souhaites restreindre la liste des modules aux seuls modules installs
    * @return array (seuls les noms de module sont retourns, pas les chemins)
    */
	public static function getList ($pRestrictedList = true){
		//on ne souhaites que les noms de module, pas les chemins
		return array_keys (self::getFullList ($pRestrictedList));
	}
	
	/**
    * Le premier module trouv dans un répertoire (dans l'ordre de dclaration des rpertoires fait foi) 
    * 
    * @param boolean $restrictedList Si l'on souhaites restreindre la liste des modules aux seuls modules installs
    * @return array (nomModule=>cheminModule)
    */
	public static function getFullList ($pRestrictedList = true){
		$conf = CopixConfig::instance ();
		$toReturn = array ();

        if ($pRestrictedList === false) {
		if (self::$_hdCache !== false){
        		return self::$_hdCache;
        	}
		foreach ($conf->arModulesPath as $path){
	   		    if (substr($path,-1)!='/') $path.='/';
	   			foreach (self::_findModulesIn ($path) as $moduleName){
	   				if (!isset ($toReturn[$moduleName])){
	   				   $toReturn[$moduleName] = $path;
	   				}
	   			}
			}
			self::$_hdCache = $toReturn;
		}else{
			if (self::$_arModuleList !== false){
			   return self::$_arModuleList;	
			}
			$cacheFile = self::_getCompiledFileName();
			if (! is_readable ($cacheFile)) {
				self::_loadPHPCacheFromDatabase ();
			}

			$arModules = array ();
  			//le fichier cacheFile doit contenir la dclaration complète de arModules
  			include ($cacheFile);
			$toReturn = self::$_arModuleList = $arModules;
		}
		return $toReturn;
	}
	
	/**
	 * Recherche les modules dans le répertoire donné
	 * @param	string	$pPath 		le chemin dans lequel on va rechercher les modules
	 * @return	array 	tableau 	des noms de modules trouvs.
	 */
	private static function _findModulesIn ($pPath){
		$toReturn = array ();
		
		if ($dir      = @opendir ($pPath)){
			while (false !== ($file = readdir($dir))) {
				if (self::isValid ($file, $pPath)){
					$toReturn[] = $file;
				}
			}
			closedir ($dir);
		}
		clearstatcache();
		return $toReturn;
	}

	/**
    * Récupre la liste des modules depuis la base de donnes et création du fichier temporaire.
    * @return void
    */
	private static function _loadPHPCacheFromDatabase () {
		try {
		   $arTemp = _ioDAO ('copix:CopixModule')->findAll ();
		   $arModules = array();
		   foreach ($arTemp as $module){
   		      $arModules[$module->name_cpm] = $module->path_cpm;
		   }
		}catch (Exception $e){
			$arModules = array ();
		}
		self::_writeInPHPCache ($arModules);		
	}

	/**
    * Ecriture d'un fichier PHP dans lequel existera un tableau associatif (nommodule=>chemin)
    * @param array $arModules le tableau que l'on souhaites crire.
    */
	private static function _writeInPHPCache ($arModules) {
		$generator = new CopixPHPGenerator ();
		$PHPString = $generator->getPHPTags ($generator->getVariableDeclaration ('$arModules', $arModules));
		CopixFile::write (self::_getCompiledFileName (), $PHPString);
	}
	
	/**
    * Gets the compiled file name.
    */
	private static function _getCompiledFileName (){
		return COPIX_CACHE_PATH.'php/copixmodule.php';
	}
	
	

	/**
    * Gets the module info
    * 
    * @return object module informations
    */
	public static function getInformations ($moduleName){
		if (! self::isValid ($moduleName)){
			throw new CopixException ('Nom de module '.$moduleName.' invalide');
		}

		$toReturn = null;
		$parsedFile = simplexml_load_file (self::getPath ($moduleName).'module.xml');
		if (isset($parsedFile->general)) {
			$defaultAttr    = $parsedFile->general->default->attributes ();
			$toReturn = new StdClass ();
			$toReturn->name = _copix_utf8_decode ((string) $defaultAttr['name']);
			//Récupération de la version des sources
			$toReturn->version = null;
			if (isset($defaultAttr['version'])) {
			    $toReturn->version = _copix_utf8_decode ((string) $defaultAttr['version']);    
			}
			CopixContext::push($toReturn->name);
			$toReturn->description     = isset ($defaultAttr['descriptioni18n']) ? _i18n((string)$defaultAttr['descriptioni18n']) : _copix_utf8_decode ((string)$defaultAttr['description']);
			$toReturn->longDescription = isset($defaultAttr['longdescriptioni18n']) ? _i18n((string)$defaultAttr['longdescriptioni18n']) : (isset ($defaultAttr['longdescription']) ? _copix_utf8_decode ((string) $defaultAttr['longdescription']) : $toReturn->description);
			$toReturn->path            = self::getBasePath ($moduleName);
			if (isset ($defaultAttr['icon']) && file_exists (_resourcePath ('img/icons/' . (string)$defaultAttr['icon']))) {
				$toReturn->icon = _resource ('img/icons/' . (string)$defaultAttr['icon']);
			} else {
				$toReturn->icon = null;
			}

			$toReturn->dependencies = array();
			if (isset ($parsedFile->dependencies)) {
				foreach ($parsedFile->dependencies->dependency as $dependency){
					$attributes = $dependency->attributes ();
					$currentDependency = new stdClass();
					$currentDependency->name = _copix_utf8_decode ((string) $attributes['name']);
					$currentDependency->kind = _copix_utf8_decode ((string) $attributes['kind']);
					$toReturn->dependencies[] = $currentDependency;
				}
			}

			$toReturn->admin_links = array ();
			if (isset ($parsedFile->admin)) {
				$adminAttributes = $parsedFile->admin->attributes ();
				$toReturn->groupid = isset ($adminAttributes['groupid']) ? (string) $adminAttributes['groupid'] : null;					
				$toReturn->groupcaption = null;
				if (isset ($adminAttributes['groupcaption'])) {
					$toReturn->groupcaption = (string)$adminAttributes['groupcaption'];
				} else if (isset ($adminAttributes['groupcaptioni18n'])) {
					$toReturn->groupcaption =  _i18n ((string)$adminAttributes['groupcaptioni18n']);
				}
				$toReturn->groupicon = (isset ($adminAttributes['groupicon'])) ? _resource ('img/icons/' . (string)$adminAttributes['groupicon']) : null;
				
				foreach ($parsedFile->admin->link as $link){
					$attributes = $link->attributes ();
					
					$linkInformations = array ();
					if (isset ($attributes['captioni18n'])){
						$linkInformations['caption'] = _i18n ((string)$attributes['captioni18n']);
					}else{
						$linkInformations['caption'] = isset ($attributes['caption']) ? _copix_utf8_decode ((string) $attributes['caption']) : $toReturn->name;
					}
					$linkInformations['url'] = _url ((string) $attributes['url']);
					$linkInformations['credentials'] = isset ($attributes['credentials']) ? (string) $attributes['credentials'] : null;
					
					$toReturn->admin_links[] = $linkInformations;
				}
			}
			
			//echo '<pre><div align="left">';
			//var_dump ($linkInformations);
			
			//Récupération des droits
			$toReturn->credential = array ();
			$toReturn->credential_notspecific = array();
			if (isset ($parsedFile->credentials)) {
			    foreach ($parsedFile->credentials->credential as $credential){
			        if (isset($credential['specific']) && (string)$credential['specific'] == "false") {
		                $toReturn->credential_notspecific[(string)$credential['name']] = array ();
		                $currentCredential = &$toReturn->credential_notspecific[(string)$credential['name']];
			        } else {
			            $toReturn->credential[(string)$credential['name']] = array ();
		                $currentCredential = &$toReturn->credential[(string)$credential['name']];
			        }
			        
			        
			        foreach ($credential->value as $value) {
			            $currentValue = new StdClass();
			            $currentValue->name = (string)$value['name'];
			            $currentValue->level = isset ($value['level']) ? (string)$value['level'] : null;
			            $currentCredential[] = $currentValue;
			        }
			    }
			}
			
			//Récupération de la list des scripts d'update
			$toReturn->update = array();
			if (isset ($parsedFile->updates)) {
			    foreach ($parsedFile->updates->update as $update){
			        $currentUpdate = new stdClass();
			        $attributes = $update->attributes ();
			        $currentUpdate->script = isset($attributes['script']) ? (string)$attributes['script'] : null;
			        $currentUpdate->from = isset($attributes['from']) ? (string)$attributes['from'] : null;
			        $currentUpdate->to = isset($attributes['to']) ? (string)$attributes['to'] : null;
			        $toReturn->update[] = $currentUpdate;
			    }
			}
			
			
			CopixContext::pop();
			
		}else{
			throw new Exception ('Impossible de lire le fichier '.self::getPath ($moduleName).'module.xml');
		}
		return $toReturn;
	}

	/**
    * gets the parameters for a given module
    * @return array
    */
	public static function getParameters ($moduleName){
		if (self::isValid($moduleName)){
			return CopixConfig::getParams($moduleName);
		}
		return array ();
	}

	/**
    * Check if the module has a correct name
    *
    * Check (if trusted module is on) if the module name belongs to the trusted module list
    * Check if there is a module.xml file
    * Handles a cache as it is called very very very often
    * 
    * @param string $moduleName le nom du module que l'on souhaites analyser.
    * @param string $pBasePath le nom du chemin dans lequel on souhaite analyser le module. 
    *  Si null, tente de dterminer le chemin lui mme
    */
	public static function isValid ($moduleName, $pBasePath = null){
		$me = new CopixModule ();

		//Is the module name ok ?
		$safeModuleName = str_replace (array ('.', ';', '/', '\\', '>', '[', ']', '(', ')', ' ', '&', '|'), '', $moduleName);
		if ($safeModuleName !== $moduleName){
			return false;
		}
		if (strlen (trim ($moduleName)) === 0){
			return false;
		}

    	//On tente de dterminer le chemin du module si pas donn
		if ($pBasePath === null){
			$path = $me->getPath ($moduleName);
		}else{
			$path = $pBasePath.'/'.$moduleName.'/';
		}


		//Can we read the module.xml file ?
		if (!is_readable ($path.'module.xml')){
			return false;
		}

		//check for the trusted module.
		$config = CopixConfig::instance ();
		if (($config->checkTrustedModules === true) && (!in_array ($moduleName, $config->trustedModules))){
			return false;
		}
		return true;
	}
	
	/**
	 * Indique le chemin (tel que dfini dans CopixConfig::$arModulesPath) du module donn.
	 * @param string $pModuleName le nom du module que l'on souhaites trouver 
	 * @return string le chemin
	 */
	public static function getPath ($pModuleName){
		if (($basePath = self::getBasePath ($pModuleName)) === null){
			return null;
		}
		return $basePath . $pModuleName . '/';
	}
	
	/**
	 * Indique le chemin de base pour les modules
	 */
	public static function getBasePath ($pModuleName){
		static $results = array ();
		if (isset ($results[$pModuleName])){
			return $results[$pModuleName];
		}
		$arModules = self::getFullList ();
		if (isset ($arModules[$pModuleName])){
			//Le module à été trouvé dans les élments installés, on retourne son chemin
			return $results[$pModuleName] = $arModules[$pModuleName];
		}else{
			$arModules = self::getFullList (false);
			if (isset ($arModules[$pModuleName])){
				//Le module à été trouvé
				return $results[$pModuleName] = $arModules[$pModuleName];
			}       	
		}
		return null;//module introuvable
    }

	/**
	 * Indique si le module donné est autorisé à l'exécution.
	 * @param string	$pModuleName	le nom du module que l'on souhaites tester
	 * @return boolean
	 */
	static public function isEnabled ($pModuleName){
		//génération du cache des modules exécutables
		self::getFullList ();
		return isset (self::$_arModuleList[$pModuleName]);
	}
	
	/**
	 * Liste des dependance a installer si on install le module
	 *
	 * @param string $pModuleName Nom du module
	 * @param mixed $arDependencies tableau de dépendances permettant de concatener les sous dépendance
	 * @return mixed Tableau des dépendances
	 */
	public static function getDependenciesForInstall ($moduleName, $arDependencies = array(), $pLevel = 0) {
	    $toCheck = self::getInformations ($moduleName);
	    $moduleDependency = new stdClass();
	    $moduleDependency->level = $pLevel;
	    $moduleDependency->name = $moduleName;
   	    $moduleDependency->kind = 'module';
	    $arDependencies['module_'.$moduleName] = $moduleDependency;
		foreach($toCheck->dependencies as $dependency){
		    if (!isset($arDependencies[$dependency->kind.'_'.$dependency->name])) {
		        $dependency->level = $pLevel+1;
       		    $arDependencies[$dependency->kind.'_'.$dependency->name] = $dependency;
    		    if ($dependency->kind === 'module')     {
    		        if (! in_array ($dependency->name, self::getList (true))) {
           		        if (in_array ($dependency->name, self::getList (false))) {
        		            $arDependencies = array_merge ($arDependencies, CopixModule::getDependenciesForInstall($dependency->name,$arDependencies, $pLevel + 1));
           		        }
    		        } else {
    		            unset($arDependencies[$dependency->kind.'_'.$dependency->name]);
    		        }
    		    }
		    }
		}
		return $arDependencies;
	}
	
	/**
	 * Liste des dependance a supprimer si on supprime le module
	 *
	 * @param string $pModuleName Nom du module
	 * @param mixed $arDependencies tableau de dépendances permettant de concatener les sous dépendance
	 * @return mixed Tableau des dépendances
	 */
	public static function getDependenciesForDelete ($pModuleName, $arDependencies = array()) {
	    if (!in_array ($pModuleName, $arDependencies)) {
			$arDependencies[] = $pModuleName;	        
	    }
		foreach (self::getList(true) as $installedModule){
			$toCheck = self::getInformations ($installedModule);
			foreach((array) $toCheck->dependencies as $dependency){
			    if ($dependency->kind === 'module') {
    				if ($dependency->name == $pModuleName && !in_array ($toCheck->name, $arDependencies)) {
    					$arDependencies[] = $toCheck->name;
    					$arDependencies = self::getDependenciesForDelete ($toCheck->name, $arDependencies);
    				}
			    }
			}
		}
		return $arDependencies;
	}
	
	/**
	 * Test si une dépendance est valide
	 * Pour un module, regarde si il existe (installer ou pas)
	 * Pour une extension, regarde si elle est dans la liste des extensions chargé
	 *
	 * @param mixed $pDependency La dependance (kind, name)
	 * @return boolean true ou false
	 */
	public static function testDependency($pDependency) {
        switch ($pDependency->kind) {
            case 'module':
                return in_array($pDependency->name, self::getList(false));
            case 'extension':
                return extension_loaded($pDependency->name);
            case 'function':
                return function_exists($pDependency->name);
            case 'class':
            	return class_exists($pDependency->name);
        }
        return false;
	}
	
	/**
	 * Installation d'un module (sans prendre en compte les dépendances)
	 *
	 * @param string $pModuleName Nom du module
	 * @return true si success et message de l'exception sinon
	 */
	public static function installModule ($pModuleName) {
	    try {
        	$scriptFile = self::_getInstallFile ($pModuleName);
    		if ($scriptFile) {
    			$ct = CopixDB::getConnection () ;
    			$ct->doSQLScript($scriptFile);
    		}
    		
    		$moduleInstaller = self::_getModuleInstaller ($pModuleName);
    		
    		if ($moduleInstaller !== null) {
    		    $moduleInstaller->processInstall ();
    		}
    		
    		self::$_arModuleList = false;
    		self::_addModuleInDatabase ($pModuleName);
    		self::_loadPHPCacheFromDatabase ();
			CopixListenerFactory::clearCompiledFile ();
	    } catch (Exception $e) {
	        return $e->getMessage();
	    }
    	return true;
	}
	
	/**
	 * Désinstallation d'un module (sans prendre en compte les dépendances)
	 *
	 * @param string $pModuleName Nom du module
	 * @return true si success et message de l'exception sinon
	 */
	public static function deleteModule ($pModuleName) {
	    try {
        	$scriptFile = self::_getDeleteFile ($pModuleName);
    		if ($scriptFile) {
    			$ct = CopixDB::getConnection () ;
    			$ct->doSQLScript ($scriptFile);
    		}
    		
	        $moduleInstaller = self::_getModuleInstaller ($pModuleName);
    		
    		if ($moduleInstaller !== null) {
    		    $moduleInstaller->processDelete ();
    		}
    		
    		self::$_arModuleList = false;
    		self::_deleteModuleInDatabase ($pModuleName);
    		self::_loadPHPCacheFromDatabase ();//on demande de rafrachir le cache PHP une fois termin.
			CopixListenerFactory::clearCompiledFile ();
	    } catch (Exception $e) {
	        return $e->getMessage();
	    }
    	return true;
	}
	/**
	 * Mets a jour un module
	 * @param $pModuleName string Nom du module
	 * @return mixed true si tout va bien, le message en cas d'exception et false si impossible de maj
	 */
	public static function updateModule ($pModuleName) {
	    $dao = _ioDAO ('copix:CopixModule');
	    $infos = CopixModule::getInformations($pModuleName);
	    $currentVersion = $dao->get($pModuleName);
	    $moduleVersion  = $infos->version;
	    if ($currentVersion->version_cpm == $moduleVersion) {
	        return true;
	    }

	    $error = false;
	    while ($currentVersion->version_cpm != $moduleVersion && !$error) {
	        $error = true;
	        foreach ($infos->update as $version) {
	            if ($version->from == $currentVersion->version_cpm) {
	                try {
    	                $scriptFile = self::_getScriptFile($pModuleName, $version->script);
    	                if ($scriptFile) {
        			        $ct = CopixDB::getConnection () ;
        			        $ct->doSQLScript ($scriptFile);
        		        }
        		        
	                    $moduleInstaller = self::_getModuleInstaller ($pModuleName);
    		
                        if ($moduleInstaller !== null) {
                		    $moduleInstaller->processUpdate ();
    		            }
    		            
    		            $method = 'process'.$version->script;
    		            if (method_exists($moduleInstaller, $method)) {
    		                $moduleInstaller->$method();
    		            }
    		            
        		        $error = false;
        		        $currentVersion->version_cpm = $version->to;
        		        _ioDAO ('copix:CopixModule')->update($currentVersion);
	                    break;
	                } catch (Exception $e) {
	                    return $e->getMessage();
	                }
	            }
	        }
	    }
	    if (!$error) {
	        return true;
	    } else {
	        return false;
	    }
	}
	
	/**
	 * Ajoute un module comme install dans la base de donnes
	 * @param string $moduleName le nom du module  ajouter
	 * @return void
	 */
	private static function _addModuleInDatabase ($moduleName){
		//insert in database if we can
		$dao	= _ioDAO ('copix:CopixModule');
		if (! $dao->get ($moduleName)){
			$record = _record ('copix:CopixModule');
			$record->name_cpm = $moduleName;
			$record->path_cpm = CopixModule::getBasePath ($moduleName);
			$record->version_cpm = CopixModule::getInformations($moduleName)->version;
			$dao->insert ($record);
		}
	}

	/**
	 * Enléve le module de la base de donnes
	 * @param string $moduleName le nom du module
	 */
	private static function _deleteModuleInDatabase ($moduleName){
		_ioDAO ('copix:CopixModule')->delete ($moduleName);
	}


	
	/**
	 * _getInstallFile
	 *
	 * Return  install.DBType.sql file for the modulePath
	 * @param string $modulePath
	 * @return scriptFile
	 * @access private
	 */
	private static function _getInstallFile ($pModuleName) {
		if (self::_dbConfigured ()){
			// find the current connection type (defined in /plugins/copixDB/profils.definition.xml)
			$config = CopixConfig::instance ();
			$driver = $config->copixdb_getProfile ();
			$typeDB = $driver->getDriverName ();

			// Search each module install file
			$scriptName ='install.'.$typeDB.'.sql';

			$SQLScriptFile = CopixModule::getPath ($pModuleName) . COPIX_INSTALL_DIR . 'scripts/' . $scriptName; // chemin et nom du fichier de script d'install
			if (file_exists($SQLScriptFile)) {
				return $SQLScriptFile;
			} else {
				return null;
			}
		}
		return null;
	}

	/**
	 * Instancie le module installer
	 *
	 */
	private static function _getModuleInstaller($pModuleName) {
		$moduleInstallerFile = CopixModule::getPath ($pModuleName) . COPIX_INSTALL_DIR . 'scripts/'.strtolower($pModuleName).'.class.php'; // chemin et nom du fichier de script d'install
	    _log('chemin '.$moduleInstallerFile, 'install');
		if (file_exists($moduleInstallerFile)) {
		    require_once($moduleInstallerFile);
		    $class = 'CopixModuleInstaller'.$pModuleName;
			return new $class;
		} else {
			return null;
		}
    
	}
	
	/**
	 * _getInstallFile
	 *
	 * Return  install.DBType.sql file for the modulePath
	 * @param string $modulePath
	 * @param string script name
	 * @return scriptFile
	 * @access private
	 */
	private static function _getScriptFile ($pModuleName, $pScript) {
		if (self::_dbConfigured ()){
			// find the current connection type (defined in /plugins/copixDB/profils.definition.xml)
			$config = CopixConfig::instance ();
			$driver = $config->copixdb_getProfile ();
			$typeDB = $driver->getDriverName ();

			// Search each module install file
			$scriptName =$pScript.'.'.$typeDB.'.sql';

			$SQLScriptFile = CopixModule::getPath ($pModuleName) . COPIX_INSTALL_DIR . 'scripts/' . $scriptName; // chemin et nom du fichier de script d'install
			if (file_exists($SQLScriptFile)) {
				return $SQLScriptFile;
			} else {
				return null;
			}
		}
		return null;
	}
	
	/**
	 * _getDeleteFile
	 *
	 * Return  delete.DBType.sql file for the modulePath
	 * @param string $pModuleName le nom du module
	 * @return le chemin du fichier sql
	 * @access private
	 */
	private static function _getDeleteFile ($pModuleName) {
		if (self::_dbConfigured()){
			// find the current connection type (defined in /plugins/copixDB/profils.definition.xml)
			$config = CopixConfig::instance ();
			$driver = $config->copixdb_getProfile ();
			$typeDB = $driver->getDriverName();

			// Search each module install file
			$scriptName = 'delete.'.$typeDB.'.sql';
			$SQLScriptFile = CopixModule::getPath ($pModuleName) . COPIX_INSTALL_DIR . 'scripts/' . $scriptName; // chemin et nom du fichier de script d'install
			return is_readable($SQLScriptFile) ? $SQLScriptFile : null;
		}
		return null;
	}
}
?>
