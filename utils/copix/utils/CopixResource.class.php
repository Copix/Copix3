<?php
/**
 * @package		copix
 * @subpackage	utils
 * @author		Guillaume Perréal
 * @copyright	CopixTeam
 * @link			http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe permettant de trouver une ressource dans www et les modules.
 *
 * Cette classe ne fait référence à aucune autre classe pour limiter au maximum les inclusions.
 * L'objectif est de pouvoir l'utiliser depuis resource.php.
 * 
 */
class CopixResource {
	
	/**
	 * Comme file_exists mais permet de faire du debug...
	 *
	 * @param unknown_type $path
	 * @return unknown
	 */
	static private function file_exists($path) {
		$file_exists = file_exists($path);
		var_dump(compact('path', 'file_exists'));
		return $file_exists;
	}
	
	/**
	 * Teste la présence d'un fichier dans un répertoire.
	 * 
	 * Cherche les variantes I18N si demandé.
	 *
	 * @param string $basePath Répertoire de base.
	 * @param string $resDir Chemin relatif du fichier.
	 * @param string $resName Nom du fichier.
	 * @param boolean $useI18N Doit-on chercher les variantes I18N ?
	 * @param string $lang Langue
	 * @param string $country Pays
	 * @return mixed Chemin du fichier trouvé ou false
	 */
	static private function _checkI18N($basePath, $resDir, $resName, $useI18N, $lang, $country) {
		if(is_dir($basePath.".")) {
			if($useI18N) {
				if(file_exists($toReturn = "${basePath}${resDir}${lang}_${country}/${resName}")) {
					return $toReturn;
				} elseif(file_exists($toReturn = "${basePath}${resDir}${lang}/${resName}")) {
					return $toReturn;
				}
			} 
			if(file_exists($toReturn = "${basePath}${resDir}${resName}")) {
				return $toReturn;
			}	
		}
		return false;
	}	
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $resourceDirs
	 * @param unknown_type $basePath
	 * @param unknown_type $resDir
	 * @param unknown_type $resName
	 * @param unknown_type $useI18N
	 * @param unknown_type $lang
	 * @param unknown_type $country
	 * @return unknown
	 */
	static private function _checkGlobal($resourceDirs, $basePath, $resDir, $resName, $useI18N, $lang, $country) {
		foreach($resourceDirs as $dir) {
			if($toReturn = self::_checkI18N($dir.$basePath, $resDir, $resName, $useI18N, $lang, $country)) {
				return $toReturn;
			}
		}
		return false;
	}
	
	/**
	 * Retourne le chemin de base des ressources
	 *
	 * @param string $pUrlBase Url de base Copix.
	 * @param string $pTheme Url 
	 * @param string $pLang
	 * @param string $pCountry
	 * @return unknown
	 */	
	static public function getResourceBaseUrl($pUrlBase, $pTheme, $pLang, $pCountry) {
		return sprintf('%sresource.php/%s/%s_%s/', $pUrlBase, $pTheme, $pLang, $pCountry);
	}

	/**
	 * Recherche une ressource.
	 *
	 * @param string $path Chemin relatif du fichier.
	 * @param string $moduleName Nom du module pouvant fournir le fichier ou null. 
	 * @param string $modulePath Chemin vers le module pouvant fournir le fichier ou null.
	 * @param string $theme Nom du thème actuel.
	 * @param boolean $useI18N Doit-on chercher les variantes I18N ?
	 * @param string $lang Langue
	 * @param string $country Pays
	 * @return mixed Un tableau array($path, $url) si le fichier a été trouvé, false sinon.
	 */
	static private function _resolve($path, $moduleName, $modulePath, $theme, $useI18N, $lang, $country) {
		
		$resDir = dirname($path);
		$resDir = $resDir == "." ? "" : "${resDir}/";
		$resName = basename($path);
		$theme = (!empty($theme) && is_dir("themes/${theme}")) ? $theme : 'default';
		
		if(isset ($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
			$urlBase = 'https://';
		} else {
			$urlBase = 'http://';
		}
		$urlBase .= (!empty($_SERVER ['HTTP_X_FORWARDED_HOST']) ? $_SERVER ['HTTP_X_FORWARDED_HOST'] : $_SERVER ['HTTP_HOST']);
		$urlBase .= preg_replace('@/[^/]*$@', '', isset ($_SERVER['ORIG_SCRIPT_NAME']) ? $_SERVER['ORIG_SCRIPT_NAME'] : $_SERVER['SCRIPT_NAME']).'/';
		
		$resourceDirs = CopixConfig::instance()->copixresource_getDirectories();

		if($theme != 'default') {
			if($moduleName) {
				if($toReturn = self::_checkGlobal($resourceDirs, "themes/${theme}/modules/${moduleName}/", $resDir, $resName, $useI18N, $lang, $country)) {
					return array($toReturn, $urlBase.$toReturn);
				}
			} else if($toReturn = self::_checkGlobal($resourceDirs, "themes/${theme}/", $resDir, $resName, $useI18N, $lang, $country)) {
				return array($toReturn, $urlBase.$toReturn);
			}
		}
		
		if($moduleName) {
			if($toReturn = self::_checkI18N("${modulePath}www/", $resDir, $resName, $useI18N, $lang, $country)) {
				return array($toReturn, self::getResourceBaseUrl($urlBase, $theme, $lang, $country).$moduleName.'/'.$path);
			}
		} elseif($toReturn = self::_checkGlobal($resourceDirs, "themes/default/", $resDir, $resName, $useI18N, $lang, $country)) {
			return array($toReturn, $urlBase.$toReturn);
		} elseif($toReturn = self::_checkGlobal($resourceDirs, "", $resDir, $resName, $useI18N, $lang, $country)) {
			return array($toReturn, $urlBase.$toReturn);
		}
		
		return array($path, $urlBase.$path);
	}
	
	/**
	 * Trouve le chemin d'une ressource, par rapport à l'index.php.
	 * 
	 * @param string $path Chemin relatif du fichier.
	 * @param string $moduleName Nom du module pouvant fournir le fichier ou null. 
	 * @param string $modulePath Chemin vers le module pouvant fournir le fichier ou null.
	 * @param string $theme Nom du thème actuel.
	 * @param boolean $useI18N Doit-on chercher les variantes I18N ?
	 * @param string $lang Langue
	 * @param string $country Pays
	 * @return mixed Le chemin du fichier s'il a été trouvé, false sinon.
	 */
	static public function findResourcePath($path, $moduleName=null, $modulePath=null, $theme=null, $useI18N=false, $lang=null, $country=null) {
		if($result = self::_resolve($path, $moduleName, $modulePath, $theme, $useI18N, $lang, $country)) {
			return $result[0];
		} else {
			return $path;
		}	
	}
	
	/**
	 * Trouve l'URL d'une ressource, par rapport à index.php.
	 * 
	 * @param string $path Chemin relatif du fichier.
	 * @param string $moduleName Nom du module pouvant fournir le fichier ou null. 
	 * @param string $modulePath Chemin vers le module pouvant fournir le fichier ou null.
	 * @param string $theme Nom du thème actuel.
	 * @param boolean $useI18N Doit-on chercher les variantes I18N ?
	 * @param string $lang Langue
	 * @param string $country Pays
	 * @return mixed Le chemin du fichier s'il a été trouvé, false sinon.
	 */
	static public function findResourceURL($path, $moduleName=null, $modulePath=null, $theme=null, $useI18N=false, $lang=null, $country=null) {
		if($result = self::_resolve($path, $moduleName, $modulePath, $theme, $useI18N, $lang, $country)) {
			return $result[1];
		} else {
			return $path;
		}
	}
	
	/**
	 * Trouve le nom de fichier d'un template contenu dans un thème.
	 * 
	 * Le schéma de recherche est différent de celui des resources pour l'I18N (afin de rester
	 * compatible avec l'existant).
	 * 
	 * Pour un sélecteur ressemblant à monModule|chemin/vers/mon/template.tpl, version fr_FR avec
	 * le thème monTheme, on aura :
	 * <code>
	 *   - monTheme/monModule/fr_FR/chemin/vers/mon/template.tpl
	 *   - monTheme/monModule/fr/chemin/vers/mon/template.tpl
	 *   - monTheme/monModule/chemin/vers/mon/template.tpl
	 *   - default/monModule/fr_FR/chemin/vers/mon/template.tpl
	 *   - default/monModule/fr/chemin/vers/mon/template.tpl
	 *   - default/monModule/chemin/vers/mon/template.tpl
	 *   - cheminCompletDeMonModule/templates/fr_FR/chemin/vers/mon/template.tpl
	 *   - cheminCompletDeMonModule/templates/fr/chemin/vers/mon/template.tpl
	 *   - cheminCompletDeMonModule/templates/chemin/vers/mon/template.tpl
	 * </code>
	 *
	 * @param string $path Chemin relatif dans le thème.
	 * @param string $moduleName Nom du module.
	 * @param string $moduleName Chemin du module.
	 * @param string $theme Nom du thème courant.
	 * @param boolean $useI18N Doit-on rechercher des templates localisés ?
	 * @param string $lang Langue.
	 * @param string $country Pays.
	 * @return string Chemin du fichier contenant le template.
	 */
	static public function findThemeTemplate($path, $moduleName=null, $modulePath=null, $theme=null, $useI18N=false, $lang=null, $country=null) {
		
		// Récupère la liste de chemins des thèmes
		$searchPaths = CopixConfig::instance()->copixtpl_getPaths();
		
		// Cherche dans le thème courant
		if($toReturn = self::_checkGlobal($searchPaths, $theme.DIRECTORY_SEPARATOR.$moduleName.DIRECTORY_SEPARATOR, "", $path, $useI18N, $lang, $country)) {
			return $toReturn;
		}
		
		// Cherche dans le thème par défaut
		if($theme != "default" && ($toReturn = self::_checkGlobal($searchPaths, "default".DIRECTORY_SEPARATOR.$moduleName.DIRECTORY_SEPARATOR, "", $path, $useI18N, $lang, $country))) {
			return $toReturn;
		}
		
		// Cherche dans le module
		if($toReturn = self::_checkI18N($modulePath, COPIX_TEMPLATES_DIR, $path, $useI18N, $lang, $country)) {
			return $toReturn;
		}
		
		// Pas trouvé
		return false;
	}
	
	/**
	 * Indique si la ressource demandée existe
	 *
	 * @param string $pResourcePath Chemin vers la ressource
	 * @return boolean
	 */
	public static function exists ($pResourcePath) {
		return file_exists (_resourcePath ($pResourcePath));
	}
}

/**
 * Erreur de récupération de ressource.
 *
 */
class CopixResourceException extends CopixException {};

/**
 * Erreur de récupération de ressource : ressource introuvable.
 *
 */
class CopixResourceNotFoundException extends CopixResourceException {};

/**
 * Erreur de récupération de ressource : accès interdit.
 *
 */
class CopixResourceForbiddenException extends CopixResourceException {};

/**
 * Classe de récupération de ressource.
 *
 */
class CopixResourceFetcher {
	
	/**
	 * Liste des types MIME pour lesquelles on va effectue la résolution du tag {copixresource}.
	 *
	 * @var array
	 */
	static private $_processedMimeTypes = array(
		'application/x-javascript',
		'text/css',
		'text/html'
	);
	
	/**
	 * Nom du thème en vigueur.
	 *
	 * @var string
	 */
	private $_theme = null;
	
	/**
	 * Nom du module auquel appartient la ressource.
	 *
	 * @var string
	 */
	private $_module = null;
	
	/**
	 * Chemin du module auquel appartient la ressource.
	 *
	 * @var string
	 */
	private $_modulePath = null;
	
	/**
	 * Composante 'langue' de l'i18n.
	 *
	 * @var string
	 */
	private $_lang = null;
	
	/**
	 * Composante 'pays' de l'i18n.
	 *
	 * @var string
	 */
	private $_country = null;
	
	/**
	 * Chemin relatif de la ressource.
	 *
	 * @var string
	 */
	private $_path = null;
	
	/**
	 * Liste des modules activés et de leur chemin.
	 *
	 * @var array
	 */
	private $_arModules = null;
	
	/**
	 * Chemin de base de l'application.
	 *
	 * @var string
	 */
	private $_basePath = null;
	
	/**
	 * Configuration Copix.
	 *
	 * @var CopixConfig
	 */
	private $_config = null;
	
	/**
	 * Cache d'URL (pour le remplacement du tag {copixresource})
	 *
	 * @var array
	 */
	private $_urlCache = array();
	
	/**
	 * Créer un récupération de ressource.
	 *
	 * @param string $pBasePath Chemin de référence.
	 */
	public function __construct($pBasePath) {
		$this->_basePath = $pBasePath;
	}
	
	/**
	 * Fixe le chemin 
	 *
	 * @param string $pPathInfo Chemin relatif.
	 * @throws CopixResourceForbiddenException si le chemin n'est pas valide.
	 */
	public function setPathInfo($pPathInfo) {
		if(preg_match('@^/([^/\\\]+)/(\w{2})(?:_(\w{2}))?/([^/\\\]+)/(.+)$@', $pPathInfo, $parts)) {
			list(, $theme, $lang, $country, $module, $path) = $parts;
		} else {
			throw new CopixResourceForbiddenException($pPathInfo);
		}
		$this->setTheme($theme);
		$this->setI18N($lang, $country);
		$this->setModule($module);
		$this->setPath($path);
	}
	
	/**
	 * Définit le thème utilise.
	 *
	 * @param string $pTheme Nom du thème.
	 */
	public function setTheme($pTheme) {
		$this->_theme = $pTheme;
	}
	
	/**
	 * Définit les paramètres I18N.
	 *
	 * @param string $pLang Code de langue.
	 * @param string $pCountry Code de pays.
	 */
	public function setI18N($pLang, $pCountry) {
		$this->_lang = $pLang;
		$this->_country = $pCountry;
	}
	
	/**
	 * Définit le module auquel appartient la ressource.
	 *
	 * @param string $pModule Nom du module.
	 */
	public function setModule($pModule) {
		if($pModule != 'www') {
			$this->_module = $pModule;
		}
	}
	
	/**
	 * Définit le chemin relatif de la ressource.
	 *
	 * @param String $pPath chemin relatif de la ressource.
	 */
	public function setPath($pPath) {
		$this->_path = $pPath;
	}
	
	/**
	 * Recupère la ressource
	 *
	 * @throws CopixResourceForbiddenException si l'accès à la ressource est interdit, 
	 *         CopixResourceNotFoundException si la ressource n'a pas été trouvée.
	 */
	public function fetch() {
		
		// Vérifie qu'on ait pas de "backward"
		$unescapedPath = utf8_decode($this->_path); // Pas de blague avec l'UTF8
		if(preg_match('@\\.\\.[/\\\]@', $unescapedPath)) {
			throw new CopixResourceForbiddenException($this->_path);
		}
		
		// Vérifie l'existence du theme
		if(!$this->_theme || !is_dir('themes/'.$this->_theme)) {
			throw new CopixResourceNotFoundException($this->_theme);
		}
			
		$arModules = $this->_getArModules();
		
		// Si on a bien un module
		if($this->_module) {
		
			// Vérifie l'existence du module
			if(isset($arModules[$this->_module])) {
				$this->_modulePath = $arModules[$this->_module].$this->_module.'/';
			} else {
				throw new CopixResourceNotFoundException($this->_module);
			}
			
			// Vérifie l'existence du chemin 'www' du module
			if(!is_dir($this->_modulePath."www")) {
				throw new CopixResourceNotFoundException($this->_module);
			}
		}
		
		// Récupère la config
		$config = $this->_getCopixConfig();
		
		// Recherche le fichier
		if(!($filePath = CopixResource::findResourcePath($this->_path, $this->_module, $this->_modulePath, $this->_theme, $config->i18n_path_enabled, $this->_lang, $this->_country))) {
			throw new CopixResourceNotFoundException($this->_path);
		}
		
		// Récupère le type MIME
		$mimeType = CopixMIMETypes::getFromFileName($filePath);
		
		// La substitution ne touche que les fichiers des modules
		if($this->_modulePath && substr($filePath, 0, strlen($this->_modulePath)) == $this->_modulePath) {
			$filePath = $this->_processModuleFile($filePath, $mimeType);
		}
		
		// Mode DEBUG ?
		if(isset($_REQUEST['DEBUG'])) {
			_dump(array(
				'this' => $this,
				'filePath' => $filePath,
				'mimeType' => $mimeType,
				'included_files' => get_included_files()
			));
			exit();
		}
		
		// Envoie le fichier
		$this->_sendFile($filePath, $mimeType);
	}
	
	/**
	 * Calcule l'URL d'une ressource à partir de  de la capture d'une expression régulière.
	 * 
	 * @param array $parts Résultat de la capture de l'expression régulière.
	 * @return string L'URL à utiliser.
	 */
	private function _replaceCopixresource($parts) {		
		list(,,$fullPath, $modulePrefix, $forceModule, $path) = $parts;
		if(!isset($this->_urlCache[$fullPath])) {
			if(!empty($modulePrefix)) {
				if(!empty($forceModule) && isset($this->_arModules[$forceModule])) {
					$this->_urlCache[$fullPath] = CopixResource::findResourceURL($path, $forceModule, $this->_arModules[$forceModule].$forceModule.'/', $this->_theme, $this->_config->i18n_path_enabled, $this->_lang, $this->_country);
				} else {
					$this->_urlCache[$fullPath] = CopixResource::findResourceURL($path, $this->_module, $this->_modulePath, $this->_theme, $this->_config->i18n_path_enabled, $this->_lang, $this->_country);
				}
			} else {
				$this->_urlCache[$fullPath] = CopixResource::findResourceURL($path, null, null, $this->_theme, $this->_config->i18n_path_enabled, $this->_lang, $this->_country);
			}
		}
		return $this->_urlCache[$fullPath];
	}
	
	/**
	 * Traite un fichier de module.
	 * 
	 * Si le type MIME du fichier est l'un de ceux pour lesquels on procède à la résolution
	 * de {copixresource}, on vérifie si une version cachée existe.
	 *
	 * @param string $pFilePath Chemin complet du ficher.
	 * @param string $pMIMEType Type MIME du fichier.
	 * @return string Chemin complet du fichier à envoyer.
	 */
	private function _processModuleFile($pFilePath, $pMIMEType) {
		
		// Seuls certains types MIME sont traités
		if(!in_array($pMIMEType, self::$_processedMimeTypes)) {
			return $pFilePath;
		}
		
		// Récupère la config
		$config = $this->_getCopixConfig();
		
		// Calcule le chemin du fichier en cache
		$cacheKey = array(COPIX_TEMP_PATH . 'resources/');
		$cacheKey[] = $this->_module;
		$cacheKey[] = $this->_theme;
		if($config->i18n_path_enabled) {
			$cacheKey[] = $this->_country;
			$cacheKey[] = $this->_lang;
		}
		$cacheKey[] = preg_replace('@[/\x5c]@', '_', $this->_path);		
		$cacheFile = join('_', $cacheKey);
		
		// Vérifie le fichier caché
		if($config->force_compile || !file_exists($cacheFile) || ($config->compile_check || filemtime($pFilePath) > filemtime($cacheFile))) {
			
			// Génère le fichier de cache en remplaçant tous les tags 
			CopixFile::write($cacheFile,
				preg_replace_callback(
					'@\{\s*copixresource\s+path=(["\'])(((\w+)?\|)?/?(.+))\1\\s*}@i',
					array($this, '_replaceCopixresource'),
					CopixFile::read($pFilePath)
				)
			);
		}
		
		// On envoie le fichier caché
		return $cacheFile;
	}
	
	/**
	 * Envoie un fichier au navigateur.
	 * 
	 * Génère toutes les en-têtes nécessaires pour une mise en cache par le navigateur
	 * et/ou des caches HTTP intermédiares. 
	 * 
	 * Prend en charge la méthode "HEAD", en n'envoyant que les en-têtes.
	 * 
	 * Prend en charge l'en-tête HTTP "If-Modified-Since", en envoyant une réponse 
	 * "340 Not Modified" si c'est applicable.
	 *
	 * @param string $pFilePath Chemin du fichier.
	 * @param string $pMIMEType Type MIME du fichier.
	 */
	private function _sendFile($pFilePath, $pMIMEType) {
		if (!file_exists($pFilePath)) {
			header("HTTP/1.0 404 Not Found");
			header("Status: 404 Not found");
			echo "File not found";
			return ;
		}
		
		header("Cache-Control: public");
		header("Date: ".date("r"));
		header("Last-Modified: ".date("r", filemtime($pFilePath)));
		header('Content-Type: '.$pMIMEType);
		header('Content-Length: '.filesize($pFilePath));
		
		// Vérification de la date de modification
		if(isset($_SERVER['X_IF_MODIFIED_SINCE'])) {
			$time = strtotime($_SERVER['X_IF_MODIFIED_SINCE']);
			if($time !== false && $time >= filemtime($pFilePath)) {
				header("340 Not Modified", null, 340);				
				return;
			}
		}

		// log d'erreur si le fichier n'existe pas
		if (!file_exists ($pFilePath)) {
			$extras = array ('backtrace' => debug_backtrace ());
			_log ('Resource not found : ' . $pFilePath, 'errors', CopixLog::ERROR, $extras);
		}
		
		// On ne fait le md5_file qu'ici, car il consomme un peu plus de CPU 
		header('ETag: '.filemtime($pFilePath));
		
		// N'envoie le fichier que si on a pas une requête HEAD
		if($_SERVER['REQUEST_METHOD'] != 'HEAD') {
			readfile($pFilePath, false);
		}	
	
	}
	
	/**
	 * Récupère la liste des modules activés et leur chemin
	 *
	 * @return array
	 */
	private function _getArModules() {
		include_once (COPIX_TEMP_PATH.'cache/php/copixmodule.php');
		return $this->_arModules = $arModules;
	}
	
	/**
	 * Récupère la configuration Copix.
	 *
	 * @return CopixConfig
	 */
	private function _getCopixConfig() {
		include_once (COPIX_CONFIG_FILE);		
		return $this->_config = CopixConfig::instance();
	}
}