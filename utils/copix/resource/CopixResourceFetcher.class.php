<?php
/**
 * @package		copix
 * @subpackage	utils
 * @author		Guillaume Perréal
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe de récupération de ressource.
 * @package copix
 * @subpackage utils
 */
class CopixResourceFetcher {
	
	/**
	 * Liste des types MIME pour lesquelles on va effectue la résolution du tag {copixresource}.
	 *
	 * @var array
	 */
	private static $_processedMimeTypes = array(
		'application/x-javascript',
		'text/css',
		'text/html'
	);
	
	/**
	 * Type de resource
	 * 
	 * @var string
	 */
	private $_type = null;
	
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
	 * Can we gzip content ?
	 * @var boolean
	 */
	private $_gzhandler = false;
	
	/**
	 * Créer un récupération de ressource.
	 */
	public function __construct () {
		include_once (COPIX_CONFIG_FILE);		
		$this->_config = CopixConfig::instance ();
	}

	/**
	 * Recherche les infos de la ressource depuis les paramètres
	 */
	public function setFromRequest () {
		// Mode default
		if (isset ($_REQUEST['theme'])) {
			$vars = array ();
			foreach (array ('type', 'theme', 'lang', 'country', 'module', 'path') as $var) {
				$value = null;
				if (isset ($_REQUEST[$var])) {
					$value = $_REQUEST[$var];
					if (get_magic_quotes_gpc ()) {
						$value = stripslashes($value);
					}
				}
				$vars[$var] = $value;
			}
			$this->setType ($vars['type']);
			$this->setTheme ($vars['theme']);
			$this->setI18N ($vars['lang'], $vars['country']);
			$this->setModule ($vars['module']);
			$this->setPath ($vars['path']);

		// Mode prepend
		} else {
			// Récupère le PATH_INFO comme on peut
			if (isset ($_SERVER['PATH_INFO'])){
				$pathInfo = isset ($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : $_SERVER['PATH_INFO'];
			} else {
				$pathInfo = $_SERVER["PHP_SELF"];
			}
			$this->setPathInfo (preg_replace ('@^' . preg_quote ($_SERVER['SCRIPT_NAME']) . '@', '', $pathInfo));
		}
	}

	/**
	 * Recherche les infos depuis une adresse qui pointe sur resource.php
	 *
	 * @param string $pURL Adresse complète, doit contenir resource.php
	 */
	public function setFromURL ($pURL) {
		$pos = strpos ($pURL, 'resource.php');
		if ($pos === false) {
			throw new CopixResourceException ('L\'adresse "' . $pURL . '" n\'est pas valide, elle ne contient pas resource.php.');
		}

		$path = substr ($pURL, $pos + 12);

		if (substr ($path, 0, 1) == '?') {

		} else {
			$this->setPathInfo ($path);
		}
	}
	
	/**
	 * Fixe le chemin 
	 *
	 * @param string $pPathInfo Chemin relatif.
	 * @throws CopixResourceForbiddenException si le chemin n'est pas valide.
	 */
	public function setPathInfo($pPathInfo) {
		$type = (substr ($pPathInfo, 1, strpos ($pPathInfo, '/', 1) - 1));
		if ($type == 'module') {
			if(preg_match('@^/([^/\\\]+)/([^/\\\]+)/(\w{2})(?:_(\w{2}))?/([^/\\\]+)/(.+)$@', $pPathInfo, $parts)) {
				list(, , $theme, $lang, $country, $module, $path) = $parts;
			} else {
				throw new CopixResourceNotFoundException ($pPathInfo);
			}
		} else {
			if(preg_match('@^/([^/\\\]+)/([^/\\\]+)/(\w{2})(?:_(\w{2}))?/(.+)$@', $pPathInfo, $parts)) {
				list(, , $theme, $lang, $country, $path) = $parts;
				$module = null;
			} else {
				throw new CopixResourceNotFoundException ($pPathInfo);
			}
		}
		$this->setType ($type);
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
	 * Définit le type de la resource, à savoir si elle vient d'un module ou d'un thème
	 *
	 * @param string $pType Type de resource : module ou theme
	 */	
	public function setType ($pType) {
		$this->_type = $pType;
	}
	
	/**
	 * Recupère la ressource
	 *
	 * @throws CopixResourceForbiddenException si l'accès à la ressource est interdit, 
	 * @throws CopixResourceNotFoundException Si la ressource n'a pas été trouvée
	 */
	public function fetch ($pSend = true) {
		$filePath = $this->getFilePath ();
		
		// Récupère le type MIME
		$mimeType = CopixMIMETypes::getFromFileName($filePath);
		
		// La substitution ne touche que les fichiers des modules
		if($this->_type == 'theme' || ($this->_modulePath && substr($filePath, 0, strlen($this->_modulePath)) == $this->_modulePath)) {
			$filePath = $this->_processModuleFile($filePath, $mimeType);
		}
		
		// Envoie le fichier
		if ($pSend) {
			$this->_sendFile($filePath, $mimeType);
		} else {
			return CopixFile::read ($filePath);
		}
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
		
		// Calcule le chemin du fichier en cache
		$cacheKey = array(COPIX_CACHE_PATH . 'resources/');
		$cacheKey[] = $this->_module;
		$cacheKey[] = $this->_theme;
		if($this->_config->i18n_path_enabled) {
			$cacheKey[] = $this->_country;
			$cacheKey[] = $this->_lang;
		}
		$cacheKey[] = preg_replace('@[/\x5c]@', '_', $this->_path);		
		$cacheFile = join('_', $cacheKey);
		
		// Vérifie le fichier caché
		if($this->_config->force_compile || !is_readable($cacheFile) || ($this->_config->compile_check || filemtime($pFilePath) > filemtime($cacheFile))) {
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
	 * Retourne les fichiers temporaires pour le module demandé
	 *
	 * @param string $pModule
	 * @return array
	 */
	public static function getCacheFilesName ($pModule) {
		return CopixFile::glob (COPIX_CACHE_PATH . 'resources/_' . $pModule . '_*');
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
	 * "304 Not Modified" si c'est applicable.
	 *
	 * @param string $pFilePath Chemin du fichier.
	 * @param string $pMIMEType Type MIME du fichier.
	 */
	private function _sendFile($pFilePath, $pMIMEType) {
		if ($this->_config->copixresource_gzipCompress && array_key_exists ('HTTP_ACCEPT_ENCODING', $_SERVER) && substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
			ob_start("ob_gzhandler");
			$this->_gzhandler = true;
		}
		if (!headers_sent ()) {
			header("Cache-Control: public");
			header("Date: ".gmdate("r"));
			header("Last-Modified: ".gmdate("r", filemtime($pFilePath)));
			header('Content-Type: '.$pMIMEType);
			header('Content-Length: '.filesize($pFilePath));
			
			switch ($this->_config->etag) {
				case CopixConfig::ETAG_MD5_FILECONTENT:
					$etagValue = md5_file($pFilePath);
					break;

				case CopixConfig::ETAG_FILEDATETIME:
					$etagValue = filemtime ($pFilePath);
					break;

				case CopixConfig::ETAG_MD5_FILEDATETIME_AND_SIZE:
					$etagValue = md5($pFilePath.filesize($pFilePath));
					break;

				default:
					$etagValue = false;
					break;
			}
			if ($etagValue !== false){
				header('ETag: '.$etagValue);
			}
			
			// Vérification de la date de modification
			if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
				$time = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
				
				if($time !== false && filemtime($pFilePath) <= $time) {
					// si on  a configuré une methode eTag, on l'exploite
					$if_none_match = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) : false;
					if($etagValue !== false && $if_none_match !== false){
						if($if_none_match && $if_none_match == $etagValue){
							header("304 Not Modified", null, 304);				
							return;
						}	
					}else{
						header("304 Not Modified", null, 304);				
						return;
					}
				}
			}
		}

		// N'envoie le fichier que si on a pas une requête HEAD
		if($_SERVER['REQUEST_METHOD'] != 'HEAD') {
			readfile($pFilePath, false);
			//if gzip content, send buffer
			if($this->_gzhandler === true){
				ob_end_flush();
			}
		}	
	}
	
	/**
	 * Retourne le chemin physique du fichier
	 *
	 * @return string
	 */
	public function getFilePath () {
		// Vérifie qu'on ait pas de "backward"
		$unescapedPath = utf8_decode ($this->_path); // Pas de blague avec l'UTF8
		if (preg_match ('@\\.\\.[/\\\]@', $unescapedPath)) {
			throw new CopixResourceForbiddenException ($unescapedPath);
		}

		// Vérifie l'existence du theme
		if (!$this->_theme || !is_dir (CopixTheme::getPath ($this->_theme))) {
			throw new CopixResourceNotFoundException (CopixTheme::getPath ($this->_theme));
		}

		$arModules = CopixModule::getFullList (false);

		// Si on a bien un module
		if ($this->_module) {
			// Vérifie l'existence du module
			if (isset ($arModules[$this->_module])) {
				$this->_modulePath = $arModules[$this->_module] . $this->_module . DIRECTORY_SEPARATOR;
			} else {
				throw new CopixResourceNotFoundException ($this->_module);
			}

			// Vérifie l'existence du chemin 'www' du module
			if (!is_dir ($this->_modulePath . COPIX_WWW_DIR)) {
				throw new CopixResourceNotFoundException ($this->_modulePath . COPIX_WWW_DIR);
			}
		}

		// Recherche le fichier
		if (!($filePath = CopixResource::findResourcePath ($this->_path, $this->_module, $this->_modulePath, $this->_theme, $this->_config->i18n_path_enabled, $this->_lang, $this->_country))) {
			throw new CopixResourceNotFoundException ($this->_path);
		}
		$filePath = CopixResource::getPathWithoutVersion($filePath);
		return $filePath;
	}
}