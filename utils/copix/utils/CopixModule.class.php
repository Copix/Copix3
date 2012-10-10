<?php
/**
 * @package		copix
 * @subpackage	core
 * @author		Croës Gérald, Salleyron Julien
 * @copyright	2001-2008 CopixTeam
 * @link		http://copix.org
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
     * Cache des groupes de modules
     * @var array
     */
    private static $_groupCache = false;

    /**
     * Liste des modules connus
     * @var array
     */
    private static $_arModuleList = false;

    /**
     * Indique si une base à été configurée
     * @return boolean true si une base est configurée, faux sinon
     */
    private static function _dbConfigured (){
        $defaultProfileName = CopixConfig::instance ()->copixdb_getDefaultProfileName ();
        return isset($defaultProfileName);
    }

    /**
     * Supprime de façon logique tous les modules installés.
     */
    public static function reset (){
        $cacheFile = self::_getCompiledFileName ();
        if (is_file($cacheFile)) {
            unlink($cacheFile);
        }
        if (self::_dbConfigured ()){
            _ioDAO ('copix:CopixModule')->deleteBy (_daoSP ());
        }
        CopixListenerFactory::clearCompiledFile ();
    }

    /**
     * Récupère la liste des modules
     * Le premier module trouvé dans un répertoire (dans l'ordre de déclaration des répertoires fait foi)
     *
     * @param boolean $restrictedList Si l'on souhaites restreindre la liste des modules aux seuls modules installs
     * @return array (seuls les noms de module sont retourns, pas les chemins)
     */
    public static function getList ($pRestrictedList = true, $pGroupId = null){
        //on ne souhaites que les noms de module, pas les chemins
        return array_keys (self::getFullList ($pRestrictedList, $pGroupId));
    }

    /**
     * Rafraichit la liste des modules
     */
    public static function clearCache () {
        $cacheFile = self::_getCompiledFileName ();
        if (is_file($cacheFile)) {
            unlink($cacheFile);
        }
    	self::$_hdCache = false;
    	self::$_arModuleList = false;
    }
    

    /**
     * Le premier module trouvé dans un répertoire (dans l'ordre de déclaration des répertoires fait foi) 
     *
     * @param boolean $restrictedList Si l'on souhaites restreindre la liste des modules aux seuls modules installs
     * @return array (nomModule=>cheminModule)
     */
    public static function getFullList ($pRestrictedList = true, $pGroupId = null){
        $conf = CopixConfig::instance ();
        $toReturn = array ();
		
		// si on veut tous les modules (installés et non installés)
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
		// si on ne veut que les modules installés
        }else{
        	$toReturn = self::_loadModuleList();
        }
        
        // si on ne veut qu'un certain group de module
        if (!is_null ($pGroupId)) {
        	foreach ($toReturn as $moduleName => $path) {
        		$moduleInfos = self::getInformations ($moduleName);
        		
        		// si on n'a pas encore mi les infos des groupes en cache, on le fait
        		if (!is_null ($moduleInfos->group->id) && !isset (self::$_groupCache[$moduleInfos->group->id])) {
        			self::$_groupCache[$moduleInfos->group->id] = $moduleInfos->group->caption;
        		}
        		
        		// si ce module ne fait pas parti du groupe que l'on veut
        		if ($moduleInfos->group->id != $pGroupId) {
        			unset ($toReturn[$moduleName]);
        		}
        	}
        }
        
        return $toReturn;
    }
    
    /**
     * Recherche les groupes de modules disponibles
     * Pour qu'un groupe soit retourné, il doit contenir au moins un module
     * 
     * @param bool $pRestrictedList Si l'on souhaites restreindre la liste des modules aux seuls modules installés
     * @return array
     */
    public static function getGroupList ($pRestrictedList = true) {
		if (self::$_groupCache !== false) {
			return self::$_groupCache;
		}
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
        	return $arModules;
        	
        } catch (CopixDBException $e) {
        	// Jette les exceptions de base et crée un fichier vide
        	return array();
    	}
    }

    /**
     * Charge la liste des modules depuis un fichier de cache.
     *
     * @param string $pFilePath
     * @return array Liste des modules
     */
    private static function _loadPHPCacheFromFile($pFilePath) {
		include ($pFilePath);
		return $arModules;	
    }
    
    /**
     * Charge la liste des modules.
     * 
     * Si le fichier de cache existe et que CopixConfig::instance ()->force_compile 
     * est faux, on le charge. Sinon, on charge la liste depuis la base de données.
     * 
     * Lorsque l'on charge la liste depuis la base ou que CopixConfig::instance ()->compile_check 
     * est vrai, la liste des modules est vérifiée. Pour chaque module, on vérifie alors que :
     *  - le module existe bien dans le chemin indiqué,
     *  - le chemin indiqué est bien listé dans CopixConfig::instance ()->arModulesPath.
     * 
     * Si la vérification n'est pas satisfaite, on recherche à nouveau le module dans l'installation
     * locale. S'il est trouvé, on enregistre son chemin dans le fichier de cache (la base n'est
     * pas modifiée). S'il n'est pas trouvé, on log un message d'erreur.
     *  
     * Crée des logs de type 'modules'. 
     *
     * @param boolean $pForceReload Force un rechargement à partir de la base.
     */
    private static function _loadModuleList($pForceReload = false) {
		if (!$pForceReload && self::$_arModuleList !== false){
			return self::$_arModuleList;
		}

		$conf = CopixConfig::instance ();
		
		// Récupère la liste des modules
		$cacheFile = self::_getCompiledFileName ();
		if (!$pForceReload && is_readable ($cacheFile) && !$conf->force_compile) {
			// Depuis le fichier de cache
			$dirty = false;
			$arModules = self::_loadPHPCacheFromFile ($cacheFile);
		} else {
			// Depuis la base
			$dirty = true;
			$arModules = self::_loadPHPCacheFromDatabase ();
		}
		
		// Vérifie la liste
		if ($dirty || $conf->compile_check) {
			
			$modulePaths = array_map(array($conf, 'getRealPath'), $conf->arModulesPath);
			
			$toSearch = array();			
			// Vérifie les des modules
			foreach ($arModules as $module=>$path) {
				
				// Résoud le chemin
				$realPath = $conf->getRealPath($path);
				
				// Vérifie qu'il appartient bien à notre installation
				if (! ($realPath && in_array ($realPath, $modulePaths))) {
					_log (_i18n ('copix:copix.error.module.unknownBasePath', array ($module, $path)), 'modules', CopixLog::WARNING);
					$toSearch[$module] = true;
				
				// S'il fait bien partie de notre installation vérifie qu'il soit valide
				} elseif (!is_readable ($realPath.$module.DIRECTORY_SEPARATOR.'module.xml')) {
					_log (_i18n ('copix:copix.error.module.doesntExist', array ($module, $path)), 'modules', CopixLog::WARNING);
					$toSearch[$module] = true;
				
				// Sinon tout va bien					
				} else {				
					// Mémorise le chemin réel
					$arModules[$module] = $realPath;
				}
			}

			// S'il y a des modules à rechercher
			if (count ($toSearch) > 0) {
				$dirty = true;
				// Recherche chaque module
				foreach ($toSearch as $module=>$dummy) {
					unset ($arModules[$module]);            	
					// Cherche le module dans l'installation
					foreach ($modulePaths as $path) {
						if (is_readable ($path.$module.DIRECTORY_SEPARATOR.'module.xml')) {
							// On l'a trouvé !
							_log (_i18n ('copix:copix.error.module.foundIn', array ($module, $path)), 'modules', CopixLog::WARNING);
							$arModules[$module] = $path;
							unset ($toSearch[$module]);
							break;
						}
					}
				}
				// S'il reste des modules non trouvés, log une erreur
				if (count ($toSearch)) {
					_log (_i18n ('copix:copix.error.module.notFound', join(', ', array_keys ($toSearch))), 'modules', CopixLog::ERROR);
				}
			}
		}
		
		
		// Récrée le fichier de cache si nécessaire (et que la liste n'est pas vide)
		if ($dirty && count($arModules) > 0) {
			self::_writeInPHPCache ($arModules);
		}
		
		return self::$_arModuleList = $arModules;
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

        $toReturn = new CopixModuleDescription ();
        $parsedFile = simplexml_load_file (self::getPath ($moduleName).'module.xml');
        if (isset ($parsedFile->general)) {
            $defaultAttr    = $parsedFile->general->default->attributes ();
            $toReturn->name = _copix_utf8_decode ((string) $defaultAttr['name']);
            //Récupération de la version des sources
            $toReturn->version = 0;
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
            
            // informations sur le groupe du module (node group)
            if (isset ($parsedFile->general->group)) {
            	$attributes = $parsedFile->general->group->attributes ();
            	// pas d'id renseigné
            	if (!isset ($attributes['id'])) {
            		throw new CopixException (_i18n ('copix:copixmodule.error.moduleGroupIdEmpty'));
            	}
            	$toReturn->group->id = (isset ($attributes['id'])) ? (string)$attributes['id'] : null;
            	if (isset ($attributes['caption'])) {
            		$toReturn->group->caption = (string)$attributes['caption'];
            	} else if (isset ($attributes['captioni18n'])) {
            		$toReturn->group->caption = _i18n ((string)$attributes['captioni18n']);
            	} else {
            		$toReturn->group->caption = $toReturn->group_id;
            	}
            // pas d'information de groupe
            } else {
            	$toReturn->group->id = null;
            	$toReturn->group->caption = null;
            }
			
			// dépendances
            $toReturn->dependencies = array();
            $toReturn->XMLDependencies = isset($parsedFile->dependencies) ? $parsedFile->dependencies : null;
            if (isset ($parsedFile->dependencies)) {
                foreach ($parsedFile->dependencies->dependency as $dependency){
                    $attributes = $dependency->attributes ();
                    $currentDependency = new stdClass();
                    $currentDependency->name = _copix_utf8_decode ((string) $attributes['name']);
                    $currentDependency->kind = _copix_utf8_decode ((string) $attributes['kind']);
                    $toReturn->dependencies[] = $currentDependency;
                }
            }

			// liens dans la partie admin
            $toReturn->admin_links = array ();
            if (isset ($parsedFile->admin)) {
                $adminAttributes = $parsedFile->admin->attributes ();
                $toReturn->admin_links_group->id = isset ($adminAttributes['groupid']) ? (string) $adminAttributes['groupid'] : null;
                $toReturn->admin_links_group->caption = null;
                if (isset ($adminAttributes['groupcaption'])) {
                    $toReturn->admin_links_group->caption = (string)$adminAttributes['groupcaption'];
                } else if (isset ($adminAttributes['groupcaptioni18n'])) {
                    $toReturn->admin_links_group->caption =  _i18n ((string)$adminAttributes['groupcaptioni18n']);
                }
                $toReturn->admin_links_group->icon = (isset ($adminAttributes['groupicon'])) ? _resource ('img/icons/' . (string)$adminAttributes['groupicon']) : null;

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
            CopixContext::pop ();
            	
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
        if (!self::isValidName ($moduleName)) {
        	return false;
        }

        //On tente de déterminer le chemin du module si pas donné
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
        
        // tout est ok
        return true;
    }
    
    /**
     * Vérifie qu'un nom de module est valide
     * 
     * @param string $pModuleName Nom du module à vérifier
     */
    public static function isValidName ($pModuleName) {
    	$safeModuleName = str_replace (array ('.', ';', '/', '\\', '>', '[', ']', '(', ')', ' ', '&', '|'), '', $pModuleName);
        return (strlen (trim ($safeModuleName)) > 0 && $safeModuleName === $pModuleName);
    }
    
    /**
     * Vérifie qu'un nom de module est disponible (qu'un autre module n'ait pas le même nom)
     * 
     * @param string $pModuleName Nom du module à vérifier
     */
    public static function isAvailable ($pModuleName) {
    	// vérification du nom du module
    	if (!self::isValidName ($pModuleName)) {
    		return false;
    	}
    	
    	// vérification que ce nom de module n'est pas déja utilisé
    	if (in_array ($pModuleName, self::getList (false))) {
    		return false;
    	}
    	
    	// tout est ok
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
            //Le module à été trouvé dans les élments installés, on retourne son chemi
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
    public static function getDependenciesForInstall ($moduleName, $arDependencies = array (), $pLevel = 0, $pGetAll = false) {
        $toCheck = self::getInformations ($moduleName);
        $moduleDependency = new stdClass ();
        $moduleDependency->level = $pLevel;
        $moduleDependency->name = $moduleName;
   	    $moduleDependency->kind = 'module';
   	    $arDependencies['module_'.$moduleName] = $moduleDependency;
   	    foreach($toCheck->dependencies as $dependency){
   	        if (!isset ($arDependencies[$dependency->kind.'_'.$dependency->name])) {
   	            $dependency->level = $pLevel+1;
   	            $arDependencies[$dependency->kind.'_'.$dependency->name] = $dependency;
   	            if ($dependency->kind === 'module')     {
   	                if (! in_array ($dependency->name, self::getList (true))) {
   	                    if (in_array ($dependency->name, self::getList (false))) {
   	                        $arDependencies = array_merge ($arDependencies, CopixModule::getDependenciesForInstall ($dependency->name,$arDependencies, $pLevel + 1, $pGetAll));
   	                    }
   	                } else {
   	                    if (! $pGetAll) {
   	                        unset ($arDependencies[$dependency->kind.'_'.$dependency->name]);
   	                    }
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
                $arDependency = explode('|',$pDependency->name);
                if (class_exists($arDependency[0])) {
                    return true;
                } elseif (isset($arDependency[1])) {
                    if (@include_once($arDependency[1])) {
                        return class_exists($arDependency[0]);
                    }
                }
                return false;
            case 'copix':
                $arDependency = explode('.',$pDependency->name);
                if (COPIX_VERSION_MAJOR < $arDependency[0]) {
                    return false;  
                } else if (COPIX_VERSION_MAJOR > $arDependency[0]) {
                    return true;
                }
                if (!isset($arDependency[1])) {
                    return true;
                }
                if (COPIX_VERSION_MINOR < $arDependency[1]) {
                    return false;
                } else if (COPIX_VERSION_MINOR > $arDependency[1]) {
                    return true;
                }
                if (!isset($arDependency[2])) {
                    return true;
                }
                if (COPIX_VERSION_FIX < $arDependency[2]) {
                    return false;  
                }
                return true;
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
        	// evenement avant l'installation du module. si un listener retourne false, on annule l'installation
        	$response = CopixEventNotifier::notify (new CopixEvent ('beforeInstallModule', array ('moduleName' => $pModuleName)));
        	foreach ($response->getResponse () as $listener) {
        		if (isset ($listener['install']) && $listener['install'] === false) {
        			$message = (isset ($listener['message'])) ? $listener['message'] : _i18n ('copix:copixmodule.error.listenerNoMessage');
        			throw new CopixException (_i18n ('copix:copixmodule.error.listenerCancelInstall', array ($pModuleName, $message)));
        		}
        	}
        	
        	// execution des scripts de base de données
            $scriptFile = self::_getInstallFile ($pModuleName);
            if ($scriptFile) {
                $ct = CopixDB::getConnection () ;
                $ct->doSQLScript($scriptFile);
            }
			
			// execution d'un script après l'install de la base de données
            $moduleInstaller = self::_getModuleInstaller ($pModuleName);
            if ($moduleInstaller !== null) {
                $moduleInstaller->processPreInstall ();
            }
            
			// recréé le cache des modules
            self::$_arModuleList = false;
            self::_addModuleInDatabase ($pModuleName);
            self::_loadModuleList (true);
            self::_clearRegistryCache();
            
            CopixListenerFactory::clearCompiledFile ();
            
            if ($moduleInstaller !== null) {
                $moduleInstaller->processPostInstall ();
            }
            
            // evenement après l'installation du module. si un listener retourne false, on annule l'installation
        	$response = CopixEventNotifier::notify (new CopixEvent ('afterInstallModule', array ('moduleName' => $pModuleName)));
        	foreach ($response->getResponse () as $listener) {
        		if (isset ($listener['install']) && $listener['install'] === false) {
        			$message = (isset ($listener['message'])) ? $listener['message'] : _i18n ('copix:copixmodule.error.listenerNoMessage');
        			throw new CopixException (_i18n ('copix:copixmodule.error.listenerCancelInstall', array ($pModuleName, $message)));
        		}
        	}
            
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
        	// evenement avant la désinstallation du module. si un listener retourne false, on annule la désinstallation
        	$response = CopixEventNotifier::notify (new CopixEvent ('beforeUninstallModule', array ('moduleName' => $pModuleName)));
        	foreach ($response->getResponse () as $listener) {
        		if (isset ($listener['uninstall']) && $listener['uninstall'] === false) {
        			$message = (isset ($listener['message'])) ? $listener['message'] : _i18n ('copix:copixmodule.error.listenerNoMessage');
        			throw new CopixException (_i18n ('copix:copixmodule.error.listenerCancelUninstall', array ($pModuleName, $message)));
        		}
        	}
        	
            $moduleInstaller = self::_getModuleInstaller ($pModuleName);
            if ($moduleInstaller !== null) {
                $moduleInstaller->processPreDelete ();
            }
                    	
            $scriptFile = self::_getDeleteFile ($pModuleName);
            if ($scriptFile) {
                $ct = CopixDB::getConnection () ;
                $ct->doSQLScript ($scriptFile);
            }

            self::$_arModuleList = false;
            self::_deleteModuleInDatabase ($pModuleName);
            self::_loadModuleList (true);//on demande de rafrachir le cache PHP une fois termin.
            self::_clearRegistryCache();
            CopixListenerFactory::clearCompiledFile ();
            
            if ($moduleInstaller !== null) {
                $moduleInstaller->processPostDelete ();
            }
                    	            
            // evenement après la désinstallation du module
        	$response = CopixEventNotifier::notify (new CopixEvent ('afterUninstallModule', array ('moduleName' => $pModuleName)));
        	
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
        $moduleInstaller = self::_getModuleInstaller ($pModuleName);
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
     * Retourne une classe qui contient des méthodes d'installation, ou null si la classe n'existe pas
     * 
     * @param string $pModuleName Nom du module
     * @return stdclass
     */
    private static function _getModuleInstaller($pModuleName) {
        // chemin et nom du fichier de script d'install
        $moduleInstallerFile = CopixModule::getPath ($pModuleName) . COPIX_INSTALL_DIR . 'scripts/'.strtolower($pModuleName).'.class.php';
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
    
    /**
     * Utilise self::getParsedModuleInformation pour extraire les informations de tags contenus dans <registry>.
     * 
     * @see getParsedModuleInformation()
     *
     * @param string $pEntryId Identifiant de l'entrée recherchée
     * @param callback $pParserCallback Callback du parser
     * @return mixed Valeur de retour du parser passé en paramètre.
     */
    static public function getParsedRegistryEntries($pEntryId, $pParserCallback) {
    	return self::getParsedModuleInformation('entry-'.$pEntryId, "/moduledefinition/registry/entry[@id='".$pEntryId."']", $pParserCallback);
    }
    
    /**
     * Retourne le chemin du cache de registre.
     *
     * @return string Chemin du cache de registre.
     */
    static private function _getRegistryCachePath() {
    	return COPIX_TEMP_PATH.'modules/registry/';
    }
    
    /**
     * Détermine le chemin du fichier de cache du registre.
     *
     * @param string $pCacheKey Clef de cache
     * @return string Chemin du fichier de cache.
     */
    static private function _getRegistryCacheFile($pCacheKey) {
    	return self::_getRegistryCachePath().preg_replace('@[:/\\\]@', '_', $pCacheKey).'.bin';
    }
    
    /**
     * Cache mémoire du registre.
     *
     * @var array "clef_de_cache" => "valeur"
     */
    static private $_registryCache = array();
    
    /**
     * Nettoie le cache de regitre.
     */
    static private function _clearRegistryCache() {
    	self::$_registryCache = array();
    	$path = self::_getRegistryCachePath();
    	if(file_exists($path) && is_dir($path)) {
    		CopixFile::removeFileFromPath($path);
    	}
    }
    
    /**
     * Extrait des informations de l'ensemble des fichiers module.xml des modules installés. 
     *
     * @param string $pCacheKey Clef pour la mise en cache.
     * @param string $pXPath Expression XPath de sélection des 
     * @param callback $pParserCallback
     * @return mixed La valeur retournée par $pParserCallback.
     */
    static public function getParsedModuleInformation($pCacheKey, $pXPath, $pParserCallback) {
    	if(!is_callable($pParserCallback)) {
    		$final = '';
    		if (is_array ($pParserCallback)){
    			foreach ($pParserCallback as $name){
    				$final .= $name;
    			}
    		}
    		throw new Exception("getParsedModuleInformation: $final should be callable");
    	}
    	
    	$config = CopixConfig::instance();
    	$force_compile = $config->force_compile;
    	$compile_check = $config->compile_check;
    	//var_dump($force_compile, $compile_check);
    	
    	if(!isset(self::$_registryCache[$pCacheKey])) {
    		
    		$cacheFile = self::_getRegistryCacheFile($pCacheKey);
    		
    		$must_compile = (!file_exists($cacheFile) || $force_compile);
    		
    		if(!$must_compile && $compile_check) {
	    		// Test les fichiers si compile_check est vrai
    			_log("Vérification du cache pour $pCacheKey ($cacheFile)", "registry", CopixLog::NOTICE);
    			$cacheDate = filemtime($cacheFile);
    			foreach(self::getList(true) as $moduleName) {
    				$descriptorPath = self::getPath($moduleName).'module.xml';
    				if(filemtime($descriptorPath) > $cacheDate) {
    					_log("$descriptorPath est plus récent que $cacheFile", "registry", CopixLog::NOTICE);
    					$must_compile = true;
    					break;
    				}
    			}
    		}
    		
    		if($must_compile) {
    			_log("Génération du cache pour $pCacheKey ($cacheFile)", "registry", CopixLog::NOTICE);
    			
    			// Liste les modules
    			$nodes = array();
    			foreach(self::getList(true) as $moduleName) {    				
    				$xml = simplexml_load_file(self::getPath($moduleName).'module.xml');
    				
    				// Extrait les infos
    				$moduleNodes = $xml->xpath($pXPath);
    				
    				// N'ajoute dans la liste que si on trouve quelque chose
    				if(is_array($moduleNodes) && count($moduleNodes) > 0) {
    					$nodes[$moduleName] = $xml->xpath($pXPath);
    				}    				
    			}
    			
    			// Compile le tout
   				self::$_registryCache[$pCacheKey] = call_user_func($pParserCallback, $nodes);

   				// Ecrit le cache
   				CopixFile::write($cacheFile, serialize(self::$_registryCache[$pCacheKey]));
    			
    		} else {
    			_log("Chargement du cache pour $pCacheKey ($cacheFile)", "registry", CopixLog::NOTICE);
    			self::$_registryCache[$pCacheKey] = unserialize(CopixFile::read($cacheFile));
    		}
    	}
    	
    	return self::$_registryCache[$pCacheKey];
    }
}

/**
 * Description d'un module
 * @package copix
 * @subpackage utils
 */
class CopixModuleDescription {
	public $admin_links_group;
	public $group;
	function __construct (){
		$this->admin_links_group = new StdClass ();
		$this->group = new StdClass ();
	}
}
?>