<?php
/**
 * @package copix
 * @subpackage theme
 * @author Croës Gérald
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */


/**
 * Opérations sur les templates
 * @package copix
 * @subpackage theme
 */
class CopixTheme {
	/**
	 * Cache des informations sur les thèmes
	 * 
	 * @var CopixThemeDescription[]
	 */
	private static $_themeDescriptions = array ();

	/**
	 * Cache des chemins des themes
	 *
	 * @var array
	 */
	private static $_themePathCache = false;

	/**
	 * Retourne le chemin physique vers le thème demandé, ou false si ce thème n'existe pas 
	 *
	 * @param string $pTheme Nom du thème, null pour le thème courant
	 * @return string
	 */
	public static function getPath ($pTheme = null) {
		$theme = ($pTheme === null) ? CopixTPL::getTheme () : $pTheme;
		
		if (self::$_themePathCache === false){
		    self::$_themePathCache = self::_getThemePathCache ();
		}

		if (!array_key_exists ($theme, self::$_themePathCache)) {
			self::$_themePathCache = self::_getThemePathCache (true);
		}

		return (array_key_exists ($theme, self::$_themePathCache)) ? self::$_themePathCache[$theme] : null;
	}

	/**
	 * Retourne le contenu du cache fichier des chemins des thèmes, ou le créé si besoin
	 *
	 * @return string
	 */
	private static function _getThemePathCache  ($pForceCompile = false) {
		$config = CopixConfig::instance ();
		$cacheFile = COPIX_CACHE_PATH . 'themes' . DIRECTORY_SEPARATOR . 'themes.list.php';
		
		// cache dans un fichier
		if ($pForceCompile || $config->force_compile || !is_readable ($cacheFile)) {
			$themesPaths = array ();
			foreach ($config->copixtheme_getPaths () as $path) {
				if (($hwnd = opendir ($path)) !== false){
    				while (($file = readdir ($hwnd)) !== false) {
    					if (is_dir ($path . $file) && is_readable ($path . $file . DIRECTORY_SEPARATOR . 'theme.xml')) {
    						$themesPaths[$file] = $path . $file . DIRECTORY_SEPARATOR;
    					}
    				}
                    closedir ($hwnd);    				
				} else {
				   _log ('Cannot read from '.$path, 'errors', CopixLog::ERROR);
				}
			}
			$php = new CopixPHPGenerator ();
			$content = $php->getVariableReturn ($themesPaths);

			// pas de thème trouvé, cest normalement impossible
			// donc on fait un log de toutes les infos qu'on peut avoir pour vérifier si c'est un bug
			if (count ($themesPaths) == 0) {
				$extras = array (
					'copixtheme_getPaths' => $config->copixtheme_getPaths (),
					'COPIX_CONFIG_FILE' => COPIX_CONFIG_FILE,
					'COPIX_CONFIG_FILE readable' => is_readable (COPIX_CONFIG_FILE),
					'themes' => array ()
				);
				foreach ($config->copixtheme_getPaths () as $path) {
					$hwnd = opendir ($path);
					while (($file = readdir ($hwnd)) !== false) {
						$extras['themes'][] = array (
							'path' => $path,
							'file' => $file,
							'is_dir' => is_dir ($path . $file),
							'is_readable theme.xml' => is_readable ($path . $file . DIRECTORY_SEPARATOR . 'theme.xml')
						);
					}
					closedir ($hwnd);
				}
				_log ('Aucun thème trouvé.', 'errors', CopixLog::FATAL_ERROR, $extras);
			}

			CopixFile::write ($cacheFile, $php->getPHPTags ($content));
		}

		// inclusion du fichier de cache
		return require ($cacheFile);
	}
	
	/**
	 * Retourne la liste des thèmes
	 * 
	 * @param boolean $pGetDefaultTheme Indique si on veut retourner le thème default, si on le trouve
	 * @return array
	 */
	public static function getList ($pGetDefaultTheme = false) {
		$themes = self::_getThemePathCache ();
		if (!$pGetDefaultTheme && isset ($themes['default'])) {
			unset ($themes['default']);
		}
		ksort ($themes);
		foreach ($themes as $theme => &$path) {
			$path = self::getInformations ($theme)->getName ();
		}
		return $themes;
	}

	/**
	 * Retourne les informations d'un thème, ou false si le thème n'existe pas
	 * 
	 * @param string $pTheme Nom du thème
	 * @return CopixThemeDescription
	 */
	public static function getInformations ($pTheme) {
		if (!array_key_exists ($pTheme, self::$_themeDescriptions)) {
			if (($path = self::getPath ($pTheme)) !== false) {
				self::$_themeDescriptions[$pTheme] = new CopixThemeDescription ($pTheme, $path . 'theme.xml');
			} else {
				self::$_themeDescriptions[$pTheme] = false;
			}
		}
		
		return self::$_themeDescriptions[$pTheme];
	}

	/**
	 * Optimize le thème demandé en mettant toutes les ressources du thème et des modules installés dans le www global
	 *
	 * @param string $pTheme Nom du thème, null pour le thème courant
	 * @param boolean $pOverWrite Indique si on doit écraser un fichier si il existe déja (peut supprimer de vraies surcharges du thème)
	 */
	public static function optimize ($pTheme = null, $pOverWrite = true) {
		$theme = ($pTheme === null) ? CopixTPL::getTheme () : $pTheme;

		// création du répertoire du thème
		try {
			$themePath = 'themes/' . $theme . '/';
			CopixFile::createDir ($themePath);
		} catch (Exception $e) {
			$message = 'Erreur lors de la création du répertoire www/' . $themePath . '.';
			$message . 'Le répertoire www/themes/ doit avoir les droits d\'écriture le temps d\'optimiser le thème ' . $theme . '.';
			throw new CopixException (_i18n ($message));
		}

		// copie des ressources
		self::_moveResources ($theme, self::getPath ($theme) . COPIX_WWW_DIR, $pOverWrite);
		foreach (CopixModule::getList () as $module) {
			self::_moveResources ($theme, CopixModule::getPath ($module) . COPIX_WWW_DIR, $pOverWrite, $module);
		}
	}

	/**
	 * Déplace les ressources dans le www global
	 *
	 * @param string $pTheme Nom du thème
	 * @param string $pPath Chemin à copier
	 * @param boolean $pOverWrite Indique si on doit écraser un fichier si il existe déja (peut supprimer de vraies surcharges du thème)
	 * @param string $pModule Nom du module, null si c'est une ressource du thème
	 */
	private static function _moveResources ($pTheme, $pPath, $pOverWrite, $pModule = null) {
		if (is_dir ($pPath)) {
			$files = CopixFile::findFiles ($pPath);
			foreach ($files as $file) {
				// on ne copie pas les css, car ils peuvent contenir un tag {copixresource, parsé par resource.php
				if (CopixFile::extractFileExt ($file) == '.css') {
					break;
				}

				$subDir = substr ($file, strpos ($file, COPIX_WWW_DIR) + strlen (COPIX_WWW_DIR));
				if ($pModule !== null) {
					$subDir = 'modules' . DIRECTORY_SEPARATOR . $pModule . DIRECTORY_SEPARATOR . $subDir;
				}
				$fullDir = 'themes' . DIRECTORY_SEPARATOR . $pTheme . DIRECTORY_SEPARATOR . $subDir;

				// nouveau répertoire
				if (is_dir ($file)) {
					CopixFile::createDir ($fullDir);
				// nouveau fichier
				} else {
					if (!file_exists ($fullDir) || $pOverWrite) {
						copy ($file, $fullDir);
					}
				}
			}
		}
	}
}