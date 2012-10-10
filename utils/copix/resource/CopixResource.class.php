<?php
/**
 * @package		copix
 * @subpackage	utils
 * @author		Guillaume Perréal, Steevan BARBOYON
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Permet de trouver une ressource dans www et les modules.
 * Cette classe ne fait référence à aucune autre classe pour limiter au maximum les inclusions.
 * L'objectif est de pouvoir l'utiliser depuis resource.php.
 * 
 * @package		copix
 * @subpackage	utils
 */
class CopixResource {
	/**
	 * Nombre d'utilisation des serveurs de ressources
	 *
	 * @var array
	 */
	private static $_resHostsUsed = array ();

	/**
	 * Teste la présence d'un fichier dans un répertoire, et cherche les variantes i18n si demandé
	 *
	 * @param string $pBasePath Répertoire de base
	 * @param string $pResDir Chemin relatif du fichier
	 * @param string $pResName Nom du fichier
	 * @param boolean $pUseI18N Indique si on doit chercher dans un répertoire de type i18n
	 * @param string $pLang Langue (utilisé si $pUseI18N = true)
	 * @param string $pCountry Pays (utilisé si $pUseI18N = true)
	 * @return mixed Chemin du fichier trouvé ou false
	 */
	private static function _checkI18N ($pBasePath, $pResDir, $pResName, $pUseI18N, $pLang, $pCountry) {
		if ($pUseI18N) {
			if (is_readable ($toReturn = "${pBasePath}${pResDir}${pLang}_${pCountry}/${pResName}")) {
				return $toReturn;
			} elseif (is_readable ($toReturn = "${pBasePath}${pResDir}${pLang}/${pResName}")) {
				return $toReturn;
			}
		}
		if (is_readable ($toReturn = "${pBasePath}${pResDir}${pResName}")) {
			return $toReturn;
		}	
		return false;
	}	

	/**
	 * Cherche une ressource "globale", qui peut se trouve dans un sous-répertoire de ressources.
	 *
	 * @param array $resourceDirs Répertoires de ressources.
	 * @param string $basePath Chemin de base.
	 * @param string $resDir Chemin de la ressource.
	 * @param string $resName Nom du fichier de ressource.
	 * @param boolean $useI18N Doit-on chercher les variantes I18N ?
	 * @param string $lang Langue
	 * @param string $country Pays
	 * @return mixed Le chemin trouvé ou false.
	 */
	private static function _checkGlobal($resourceDirs, $basePath, $resDir, $resName, $useI18N, $lang, $country) {
		foreach($resourceDirs as $dir) {
			if($toReturn = self::_checkI18N ($dir.$basePath, $resDir, $resName, $useI18N, $lang, $country)) {
				return $toReturn;
			}
		}
		return false;
	}
	
	/**
	 * Génère le chemin d'accès à une ressource de module.
	 *
	 * @param string $pUrlBase
	 * @param string $pTheme
	 * @param string $pLang
	 * @param string $pCountry
	 * @param string $pModule
	 * @param string $pPath
	 * @return string URL de la ressource.
	 */
	private static function _buildResourceUrl($pType, $pUrlBase, $pTheme, $pLang, $pCountry, $pModule = null, $pPath = null) {
		if(CopixConfig::instance ()->significant_url_mode == 'prepend') {
			// En mode prepend, fait tout à la main :p
			if ($pCountry != null){
				$toReturn = sprintf ('%sresource.php/' . $pType . '/%s/%s_%s/', $pUrlBase, $pTheme, $pLang, $pCountry);				
			}else{
				$toReturn = sprintf ('%sresource.php/' . $pType . '/%s/%s/', $pUrlBase, $pTheme, $pLang);
			}

			if ($pType == 'module') {
				if($pModule !== null) {
					$toReturn .= $pModule . '/';
					if($pPath !== null) {
						$toReturn .= $pPath ;
					}
				}
			} else {
				$toReturn .= $pPath;
			}
			return $toReturn;
		} else {
			// Laisse CopixURL gérer
			return CopixURL::appendToUrl ($pUrlBase.'resource.php', array(
				'type' => $pType,
				'theme' => $pTheme,
				'lang' => $pLang,
				'country' => $pCountry,
				'module' => $pModule,
				'path' => $pPath
			));
		}		
	}
	
	/**
	 * Retourne le chemin de base des ressources.
	 *
	 * @param string $pUrlBase Url de base Copix.
	 * @param string $pTheme Thème
	 * @param string $pLang Langue
	 * @param string $pCountry Pays
	 * @return string L'URL "de base"
	 */	
	public static function getResourceBaseUrl($pUrlBase, $pTheme, $pLang, $pCountry) {
		return self::_buildResourceUrl ('module', $pUrlBase, $pTheme, $pLang, $pCountry);
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
	private static function _resolve ($path, $moduleName, $modulePath, $pTheme, $useI18N, $lang, $country) {
		$resDir = dirname($path);
		$resDir = $resDir == '.' ? '' : "${resDir}/";
		$resName = basename($path);
		$theme = (!empty($pTheme) && CopixTheme::getPath ($pTheme) !== null) ? $pTheme : 'default';
		
		$urlBase = self::getResourceServer ($path);
		
		$urlBase .= preg_replace ('@/[^/]*$@', '', isset ($_SERVER['ORIG_SCRIPT_NAME']) ? $_SERVER['ORIG_SCRIPT_NAME'] : $_SERVER['SCRIPT_NAME']).'/';
		
		$resourceDirs = CopixConfig::instance ()->copixresource_getDirectories ();
		
		$themesDirs = array (CopixTheme::getPath ($theme));
		if (!$moduleName) {
			if ($toReturn = self::_checkGlobal ($themesDirs, COPIX_WWW_DIR, $resDir, $resName, $useI18N, $lang, $country)) {
				return array ($toReturn, self::_buildResourceUrl ('theme', $urlBase, $theme, $lang, $country, null, $path));
			}
		}else{
			if ($toReturn = self::_checkGlobal ($themesDirs, COPIX_WWW_DIR.'modules'.DIRECTORY_SEPARATOR.$moduleName.DIRECTORY_SEPARATOR, $resDir, $resName, $useI18N, $lang, $country)){
				return array ($toReturn, self::_buildResourceUrl ('module', $urlBase, $theme, $lang, $country, $moduleName, $path));
			}			
		}
		
		if($theme != 'default') {
			if($moduleName) {
				if($toReturn = self::_checkGlobal ($resourceDirs, "themes/${theme}/modules/${moduleName}/", $resDir, $resName, $useI18N, $lang, $country)) {
					return array ($toReturn, $urlBase.$toReturn);
				}
			} else if($toReturn = self::_checkGlobal ($resourceDirs, "themes/${theme}/", $resDir, $resName, $useI18N, $lang, $country)) {
				return array ($toReturn, $urlBase.$toReturn);
			}
		}

		if($moduleName) {
			if($toReturn = self::_checkI18N ($modulePath . COPIX_WWW_DIR, $resDir, $resName, $useI18N, $lang, $country)) {
				return array ($toReturn, self::_buildResourceUrl ('module', $urlBase, $theme, $lang, $country, $moduleName, $path));
			}
		} elseif($toReturn = self::_checkGlobal ($resourceDirs, "themes/default/", $resDir, $resName, $useI18N, $lang, $country)) {
			return array ($toReturn, $urlBase.$toReturn);
		} elseif($toReturn = self::_checkGlobal($resourceDirs, "", $resDir, $resName, $useI18N, $lang, $country)) {
			return array ($toReturn, $urlBase.$toReturn);
		}

		return false;
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
	public static function findResourcePath ($path, $moduleName=null, $modulePath=null, $theme=null, $useI18N=false, $lang=null, $country=null) {
		$path = self::getPathWithoutVersion($path);
		if($result = self::_resolve ($path, $moduleName, $modulePath, $theme, $useI18N, $lang, $country)) {
			return $result[0];
		} else {
			$extras = array ('$moduleName' => $moduleName, '$modulePath' => $modulePath,
				'$theme' => $theme, '$useI18N' => $useI18N, '$lang' => $lang, '$country' => $country
			);
			self::_getCaller ($path, $extras);
			_log ('Resource not found : ' . $path, 'errors', CopixLOG::ERROR, $extras);
			return false;
		}
	}
	
	public static function getPathWithoutVersion($path) {
		if (CopixConfig::instance ()->copixresource_addVersionInFileName) {
			$path = preg_replace ('/\.v\d*(\.[a-z0-9]*)$/i', '$1', $path);
		} else {
			$path = preg_replace('/([^?]*).*/', '$1', $path);
		}
		return $path;
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
	public static function findResourceURL ($path, $moduleName=null, $modulePath=null, $theme=null, $useI18N=false, $lang=null, $country=null, $withVersion = false) {
		if($result = self::_resolve($path, $moduleName, $modulePath, $theme, $useI18N, $lang, $country)) {
			$path = realpath($result[0]);
			$url = $result[1];
			if ($withVersion) {
				$timestamp = 0;
				if(file_exists($path)){
					$timestamp = filemtime($path);
				}
				if (CopixConfig::instance ()->copixresource_addVersionInFileName) {
					$extension = CopixFile::extractFileExt($url);
					$url = str_replace ($extension, '.v' .$timestamp . $extension, $url);
				} else {
					$url .='?v='.$timestamp;
				}
			}
			return $url;
		} else {
			$extras = array ('$moduleName' => $moduleName, '$modulePath' => $modulePath,
				'$theme' => $theme, '$useI18N' => $useI18N, '$lang' => $lang, '$country' => $country
			);
			self::_getCaller ($path, $extras);
			_log ('Resource not found : ' . $path, 'errors', CopixLOG::ERROR, $extras);
			return false;
		}
	}

	/**
	 * Recherche le véritable appel à _resource ou CopixUrl::getResource
	 *
	 * @param string $pPath Chemin de la ressource
	 * @param array $pExtras Les clefs file et line seront changée dans ce tableau
	 */
	private static function _getCaller ($pPath, &$pExtras) {
		$isSmarty = false;
		foreach (debug_backtrace () as $infos) {
			// si on a trouvé qu'on était dans un template smarty
			if ($isSmarty) {
				if (strtolower ($infos['function']) == 'fetch' && strtolower ($infos['class']) == 'smarty') {
					$pExtras['file'] = $infos['args'][0];
					$pExtras['line'] = null;
					break;
				}

			// si c'est dans un tpl et qu'on a utilisé {copixresource}
			} else if (array_key_exists('file', $infos) && strpos ($infos['file'], 'smarty_plugins/function.copixresource.php') !== false && count ($infos['args']) > 0 && $infos['args'][0] == $pPath) {
				$isSmarty = true;

			// on sait pas encore si on est dans un template smarty, on cherche _resource
			} else if (strtolower ($infos['function']) == '_resource') {
				$pExtras['file'] = $infos['file'];
				$pExtras['line'] = $infos['line'];
			}
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
	public static function findThemeTemplate ($path, $moduleName=null, $modulePath=null, $theme=null, $useI18N=false, $lang=null, $country=null) {
		// Récupère la liste de chemins des thèmes
		$searchPaths = CopixConfig::instance ()->copixtpl_getPaths();
		
		// Cherche dans le thème courant
		if ($toReturn = self::_checkGlobal ($searchPaths, $theme.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$moduleName.DIRECTORY_SEPARATOR, "", $path, $useI18N, $lang, $country)) {
			return $toReturn;
		}
		
		// Cherche dans le thème par défaut
		if($theme != "default" && ($toReturn = self::_checkGlobal ($searchPaths, "default".DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$moduleName.DIRECTORY_SEPARATOR, "", $path, $useI18N, $lang, $country))) {
			return $toReturn;
		}
		
		// Cherche dans le module
		if($toReturn = self::_checkI18N ($modulePath, COPIX_TEMPLATES_DIR, $path, $useI18N, $lang, $country)) {
			return $toReturn;
		}
		// Pas trouvé
		return false;
	}

	/**
	 * Indique si la ressource existe
	 *
	 * @param string $pPath Sélecteur de ressource
	 * @param string $pTheme Nom du thème, thème actuel si null
	 */
	public static function exists ($pPath, $pTheme = null) {
		$theme = ($pTheme === null) ? CopixTpl::getTheme () : $pTheme;
		$i18n = CopixConfig::instance ()->i18n_path_enabled;
		$lang = CopixI18N::getLang ();
		$country = CopixI18N::getCountry ();

		if (!preg_match ('@^((\w+)?\|)?/?(.+)$@', $pPath, $parts)) {
			return false;
		}
		list (, $modulePrefix, $moduleName, $resourcePath) = $parts;
		if (!empty ($modulePrefix) && empty ($moduleName)) {
			$moduleName = CopixContext::get ();
		}
		$modulePath = (empty ($moduleName)) ? null : CopixModule::getPath ($moduleName);

		return (self::_resolve ($resourcePath, $moduleName, $modulePath, $theme, $i18n, $lang, $country) !== false);
	}
	
	/**
	 * Renvoie le serveur d'une ressource en fonction de son type
	 *
	 * @param string $path le chemin vers une ressource (seule l'extension est utilisée)
	 * @return string protocole + domaine
	 */
	public static function getResourceServer ($path) {
		if(isset ($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
			$urlBase = 'https://';
		} else {
			$urlBase = 'http://';
		}
		if (!empty ($_SERVER ['HTTP_X_FORWARDED_HOST'])) {
			$urlBase .= $_SERVER ['HTTP_X_FORWARDED_HOST'];
		} else {
			// si on a ajouté des serveurs de ressource
			$config = CopixConfig::instance ();
			if ($config->copixresource_haveServers ()) {
				$ext = CopixFile::extractFileExt ($path);
				// recherche du type de la ressource
				if ($ext == '.css') {
					$resType = CopixConfig::RESSERVER_STYLES;
				} else if ($ext == '.js') {
					$resType = CopixConfig::RESSERVER_JS;

				} else if (strpos (CopixMIMETypes::getFromExtension ($ext), 'image/') !== false) {
					$resType = CopixConfig::RESSERVER_IMAGES;
				} else {
					$resType = CopixConfig::RESSERVER_OTHERS;
				}

				// recherche du serveur à utiliser
				// un serveur a déja été utilisé, et on peut encore l'utiliser
				if (isset (self::$_resHostsUsed[$resType]) && self::$_resHostsUsed[$resType]['count'] < $config->copixresource_getChangeServer ()) {
					$urlBase .= self::$_resHostsUsed[$resType]['host'];
					self::$_resHostsUsed[$resType]['count']++;
				// aucun serveur n'a été utilisé, ou alors le serveur actuel a été utilisé au maximum
				} else {
					$hosts = CopixConfig::instance ()->copixresource_getServers ($resType);
					// pas de serveurs définis pour ce type
					if (count ($hosts) == 0) {
						$urlBase .= CopixUrl::getRequestedDomain ();
					// on a des serveurs pour ce type de ressource
					} else {
						// aucun serveur n'a encore été utilisé, ou il n'y en a pas de suivant, on prend le 1er
						if (!isset (self::$_resHostsUsed[$resType]) || !isset ($hosts[self::$_resHostsUsed[$resType]['index'] + 1])) {
							$host = $hosts[0];
							self::$_resHostsUsed[$resType]['index'] = 0;
						// récupération du serveur suivant
						} else {
							$host = $hosts[self::$_resHostsUsed[$resType]['index'] + 1];
							self::$_resHostsUsed[$resType]['index'] = self::$_resHostsUsed[$resType]['index'] + 1;
						}
						$urlBase .= $host;
						self::$_resHostsUsed[$resType]['host'] = $host;
						self::$_resHostsUsed[$resType]['count'] = 1;
						
					}
				}

			// pas de serveur de ressource ajouté
			} else {
				$urlBase .= CopixUrl::getRequestedDomain ();
			}
		}
		return $urlBase;
	}
}