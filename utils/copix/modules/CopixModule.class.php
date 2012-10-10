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
		$cacheFile = self::getListCacheFileName ();
		if (is_file($cacheFile)) {
			unlink($cacheFile);
		}
		if (self::_dbConfigured ()){
			$dao = new DAOCopixModule ();
			$dao->deleteBy (_daoSP ());
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
	 * Rafraichit la liste des modules et tout ce qui y est associé
	 */
	public static function clearCache () {
		$cacheFile = self::getListCacheFileName ();
		if (is_file($cacheFile)) {
			unlink($cacheFile);
		}
		self::$_hdCache = false;
		self::$_arModuleList = false;

		self::_loadModuleList (true);//on demande de rafrachir le cache PHP une fois termin.
		
		self::$_registryCache = array();
		$path = self::_getRegistryCachePath();
		if(is_readable($path) && is_dir($path)) {
			CopixFile::removeFileFromPath($path);
		}
		CopixListenerFactory::clearCompiledFile ();
	}

	/**
	 * Le premier module trouvé dans un répertoire (dans l'ordre de déclaration des répertoires fait foi) 
	 *
	 * @param boolean $restrictedList Si l'on souhaites restreindre la liste des modules aux seuls modules installs
	 * @return array (nomModule=>cheminModule)
	 */
	public static function getFullList ($pRestrictedList = true, $pGroupId = null){
		$toReturn = array ();
		
		// si on veut tous les modules (installés et non installés)
		if ($pRestrictedList === false) {
			if (self::$_hdCache !== false){
				return self::$_hdCache;
			}
			$conf = CopixConfig::instance ();
			foreach ($conf->arModulesPath as $path){
				$path = CopixFile::trailingSlash ($path);
				foreach (self::_findModulesIn ($path) as $moduleName){
					if (!isset ($toReturn[$moduleName])){
						$toReturn[$moduleName] = $path;
					}
				}
			}
			self::$_hdCache = $toReturn;
			self::_writeInPHPCache (self::$_hdCache, false);
		// si on ne veut que les modules installés
		}else{
			//loadModuleList positionne $arModuleList
				self::_loadModuleList ();
				$toReturn = self::$_arModuleList;
		}
		
		// si on ne veut qu'un certain groupe de module
		if ($pGroupId !== null) {
			foreach ($toReturn as $moduleName => $path) {
				$moduleInfos = self::getInformations ($moduleName);
				
				// si on n'a pas encore mi les infos des groupes en cache, on le fait
				if (($moduleInfos->group->id !== null) && !isset (self::$_groupCache[$moduleInfos->group->id])) {
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

		if ($dir	  = @opendir ($pPath)){
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
			$arTemp = _ioDAO ('CopixModule')->findAll ();
			$arModules = array();
			foreach ($arTemp as $module){		  	
		   		$arModules[$module->name_cpm] = $module->path_cpm;
			}
			return $arModules;
			
		} catch (CopixDBException $e) {
			// Jette les exceptions de base et crée un fichier vide
			return array();
		} catch (CopixDaoException $e) {
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
	private static function _loadModuleList ($pForceReload = false) {
		if (!$pForceReload && self::$_arModuleList !== false){
			return;
		}

		$conf = CopixConfig::instance ();
		
		// Récupère la liste des modules
		$cacheFile = self::getListCacheFileName ();
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
			$modulePaths = array_map (array ('CopixFile', 'getRealPath'), $conf->arModulesPath);
			$modulePaths = array_map (array ('CopixFile', 'trailingSlash'), $modulePaths);

			$toSearch = array();			
			// Vérifie les des modules
			foreach ($arModules as $module=>$path) {
				// Résoud le chemin
				$realPath = CopixFile::getRealPath ($path);

				// Vérifie qu'il appartient bien à notre installation
				if (! ($realPath && in_array ($realPath, $modulePaths))) {
					_log (_i18n ('copix:copix.error.module.unknownBasePath', array ($module, $path)), 'modules', CopixLog::WARNING);
					$toSearch[$module] = true;
				} elseif (!is_readable ($realPath.$module.DIRECTORY_SEPARATOR.'module.xml')) {
					 // S'il fait bien partie de notre installation vérifie qu'il soit valide
					_log (_i18n ('copix:copix.error.module.doesntExist', array ($module, $path)), 'modules', CopixLog::WARNING);
					$toSearch[$module] = true;
				} else {				
					// Sinon tout va bien
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
							$arModules[$module] = CopixFile::getRealPath ($path);
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
		
		self::$_arModuleList = $arModules;
	}
	
	/**
	 * Ecriture d'un fichier PHP dans lequel existera un tableau associatif (nommodule=>chemin)
	 * @param array   $arModules   le tableau que l'on souhaites crire.
	 * @param boolean $pRestricted Si arModulesPath ne concerne que les modules installés
	 */
	private static function _writeInPHPCache ($arModules, $pRestricted = true) {
		$generator = new CopixPHPGenerator ();
		$PHPString = $generator->getPHPTags ($generator->getVariableDeclaration ('$arModules', $arModules));
		CopixFile::write (self::getListCacheFileName ($pRestricted), $PHPString);
	}

	/**
	 * Nom du fichier de cache pour les modules
	 * 
	 * @param boolean $pRestricted Indique si l'on souhaite uniquement les modules installés
	 * @return string	
	 */
	public static function getListCacheFileName ($pRestricted = true) {
		return $pRestricted ? COPIX_CACHE_PATH . 'modules/installed.php' : COPIX_CACHE_PATH . 'modules/availables.php';
	}

	/**
	 * Gets the module info
	 *
	 * @return CopixModuleDescription module informations
	 */
	public static function getInformations ($pModuleName){
		if (!self::isValid ($pModuleName)) {
			throw new CopixException ('Nom de module "'.$pModuleName.'" invalide');
		}
		
		if (self::isEnabled ($pModuleName)){
			//le module est installé, on récupère le fichier xml grace à getParsedModuleInformation
			$moduleXmlParser = new CopixModuleXmlParser ($pModuleName);
			return self::getParsedModuleInformation ('copix|'.$pModuleName.'|module.xml','/moduledefinition', array ($moduleXmlParser, 'getDescriptionFromXml')); 
		}else{
			//le module n'est pas installé, il faut récupérer le fichier xml a la main
			return new CopixModuleDescription (simplexml_load_file (CopixModule::getPath ($pModuleName).'module.xml'));
		}
	}
	
	/**
	 * Récupère la définition du module
	 * 
	 * @return object 
	 */
	public static function getDefinition ($pModuleName){
		if (!self::isValid ($pModuleName)) {
			throw new CopixException ('Nom de module '.$pModuleName.' invalide');
		}
		$moduleXmlParser = new CopixModuleXmlParser ($pModuleName);
		return self::getParsedModuleInformation ('copix|'.$pModuleName.'|module.xml','/moduledefinition', array ($moduleXmlParser, 'getDefinitionFromXml')); 
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
		return (strlen (trim ($safeModuleName)) > 0 && $safeModuleName === (string) $pModuleName);
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
		return $basePath . $pModuleName . DIRECTORY_SEPARATOR;
	}

	/**
	 * Indique le chemin de base pour les modules
	 */
	public static function getBasePath ($pModuleName){
	   	//On appèle getFullList qui génère self::$_arModuleList s'il n'existe pas
	   	self::_loadModuleList ();

	//si défini, on retourne
		if (isset (self::$_arModuleList[$pModuleName])){
			return self::$_arModuleList[$pModuleName];
		}
	
		//On a pas trouvé dans les modules installés ?
		//on réappèle getFullList en demandant la liste complète des modules
		//pour générer la partie $_hdCache
		self::getFullList (false);
		if (isset (self::$_hdCache[$pModuleName])){
			//Le module à été trouvé
			return self::$_hdCache[$pModuleName];
		}
		
		//module introuvable
		return;
	}

	/**
	 * Indique si le module donné est autorisé à l'exécution.
	 * @param string	$pModuleName	le nom du module que l'on souhaites tester
	 * @return boolean
	 */
	public static function isEnabled ($pModuleName){
		//génération du cache des modules exécutables
		self::getFullList ();
		return isset (self::$_arModuleList[$pModuleName]);
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
	public static function getParsedRegistryEntries($pEntryId, $pParserCallback) {
		return self::getParsedModuleInformation('entry-'.$pEntryId, "/moduledefinition/registry/entry[@id='".$pEntryId."']", $pParserCallback);
	}

	/**
	 * Retourne le chemin du cache de registre.
	 *
	 * @return string Chemin du cache de registre.
	 */
	private static function _getRegistryCachePath() {
		return COPIX_TEMP_PATH.'cache/modules/registry/';
	}
	
	/**
	 * Détermine le chemin du fichier de cache du registre.
	 *
	 * @param string $pCacheKey Clef de cache
	 * @return string Chemin du fichier de cache.
	 */
	private static function _getRegistryCacheFile ($pCacheKey, $pLocale = null) {
		$locale = ($pLocale == null) ? CopixI18N::getLocale () : $pLocale;
		return self::_getRegistryCachePath () . preg_replace ('@[:|/\\\]@', '_', $pCacheKey) . '~' . $locale . '.bin';
	}

	/**
	 * Retourne les fichiers de cache concernant les informations du module donné
	 *
	 * @param string $pName
	 */
	public static function getCacheFilesName ($pName) {
		return CopixFile::glob (self::_getRegistryCacheFile ('copix|' . $pName . '|module.xml', '*'));
	}

	/**
	 * Cache mémoire du registre.
	 *
	 * @var array "clef_de_cache" => "valeur"
	 */
	private static $_registryCache = array();
	
	/**
	 * Extrait des informations de l'ensemble des fichiers module.xml des modules installés. 
	 *
	 * @param string $pCacheKey Clef pour la mise en cache.
	 * @param string $pXPath Expression XPath de sélection des 
	 * @param callback $pParserCallback
	 * @return mixed La valeur retournée par $pParserCallback.
	 */
	public static function getParsedModuleInformation($pCacheKey, $pXPath, $pParserCallback) {
		static $filemtimes = array ();

		if (! array_key_exists ($pCacheKey, self::$_registryCache)) {
			$config = CopixConfig::instance();
			$force_compile = $config->force_compile;
			$compile_check = $config->compile_check;

			$cacheFile = self::_getRegistryCacheFile($pCacheKey);
			$must_compile = (!is_readable($cacheFile) || $force_compile);
			if(!$must_compile && $compile_check) {
				// Test les fichiers si compile_check est vrai
				$cacheDate = filemtime($cacheFile);
				foreach (self::getList() as $moduleName) {
					if (! isset ($filemtimes[$moduleName])){
						$descriptorPath = self::getPath($moduleName).'module.xml';
						$filemtimes[$moduleName] = filemtime($descriptorPath);
					}

					if ($filemtimes[$moduleName] > $cacheDate) {
						$must_compile = true;
						break;
					}
				}
			}
			
			if($must_compile) {
				//Si on doit compiler, on vérifie que la méthode de parsing est correcte
				if(!is_callable($pParserCallback)) {
					throw new CopixException('getParsedModuleInformation: $pParserCallback should be callable');
				}

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
//   				$generator = new CopixPHPGenerator ();
//   				$generator->write ($cacheFile, $generator->getPHPTags($generator->getVariableDeclaration ('$cached', self::$_registryCache[$pCacheKey])));
   				CopixFile::write($cacheFile, serialize(self::$_registryCache[$pCacheKey]));
			} else {
				//_log("Chargement du cache pour $pCacheKey ($cacheFile)", "registry", CopixLog::INFORMATION);
				//include ($cacheFile);
				//self::$_registryCache[$pCacheKey] = $cached; 
				self::$_registryCache[$pCacheKey] = unserialize(CopixFile::read($cacheFile));
			}
		}

		return self::$_registryCache[$pCacheKey];
	}
	
	public static function getDependenciesForInstall ($moduleName, $arDependencies = array (), $pLevel = 0){
		return CopixModuleInstallOperations::getDependenciesForInstall ($moduleName, $arDependencies, $pLevel);
	}
	public static function getDependenciesForDelete ($pModuleName, $arDependencies = array()) {
		return CopixModuleInstallOperations::getDependenciesForDelete ($pModuleName, $arDependencies);
	}
	public static function testDependency($pDependency) {
		return CopixModuleInstallOperations::testDependency($pDependency);
	}
	public static function installModule ($pModuleName){
		return CopixModuleInstallOperations::installModule ($pModuleName);
	}
	public static function deleteModule ($pModuleName) {
		return CopixModuleInstallOperations::deleteModule ($pModuleName);
	}
	public static function updateModule ($pModuleName) {
		return CopixModuleInstallOperations::updateModule ($pModuleName); 
	}
}