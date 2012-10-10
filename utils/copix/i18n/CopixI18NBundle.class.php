<?php
/**
 * @package copix
 * @subpackage i18n
 * @author Croës Gérald, Jouanneau Laurent
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Contient un ensemble de traductions concernant une langue donnée, et pour tout les pays concernés
 * 
 * @package copix
 * @subpackage i18n
 */
class CopixI18NBundle {
	/**
	 * Langue chargée
	 * 
	 * @var string
	 */
	private $_lang;

	/**
	 * Messages chargés
	 * 
	 * @var array
	 */
	private $_messages = array ();
	
	/**
	 * Indique si le bundle est chargé
	 * 
	 * @var boolean
	 */
	private $_ready = false;
	
	/**
     * Constructeur
     * 
     * @param CopixFileSelector $pFile Fichier
     * @param string $pLang Langue que l'on veut charger
     */
	public function __construct ($pResource, $pLang, $pCountry) {
		if ($pResource === 'copix:'){
			$this->_fileName = COPIX_PATH.'locales/';
		}else{
			$this->_fileName = CopixModule::getPath ($pResource).'locales/';
		}

		$this->_resource = $pResource;
		$this->_lang = $pLang;
		$this->_country = $pCountry;
	}
	
	/**
	 * Retourne les messages pour la langue $this->_lang et le pays $pCountry
	 * 
	 * @param string $pCountry Pays
	 * @return array
	 */
	public function getKeys ($pCountry) {
		$this->_loadLocales ();
		return $this->_messages;		
	}

	/**
     * Retourne la chaine de caractère représentée par $pKey pour le pays $pCountry
     * 
     * @param string $pKey Clef
     * @param string $pCountry Pays
     * @return string,null
     */
	public function get ($pKey) {
		$this->_loadLocales ();		
		if (isset ($this->_messages[$pKey])) {
			return $this->_messages[$pKey];
		} else {
			return null;
		}
	}

	/**
     * Charge les traductions pour le pays donné
     * 
     * @param string $pCountry Pays
     */
	private function _loadLocales () {
		if ($this->_ready){
			return true;
		}

		$pCountry = $this->_country;
		$path = $this->_fileName;
		$toLoad[] = array ('file' => $path . 'default.properties', 'lang' => 'default', 'country' => 'DEFAULT');
		$toLoad[] = array ('file' => $path . $this->_lang . '.properties', 'lang' => $this->_lang, 'country' => strtoupper ($this->_lang));
		$toLoad[] = array ('file' => $path . $this->_lang . '_' . $this->_country . '.properties', 'lang' => $this->_lang, 'country' => $pCountry);

		// check if we have a compiled version of the ressources
		$_compileResourceId = $this->_getCompileId ();

		if (($_compileResourceIdTime = @filemtime ($_compileResourceId)) !== false) {
            $config = CopixConfig::instance ();
			if ($config->compile_check || $config->force_compile) {
				if ($config->force_compile) {
					//force compile, compiled files are never assumed to be ok.
					$okcompile = false;
				} else {
					// on verifie que les fichiers de ressources sont plus anciens que la version compilée
					$compiledate = $_compileResourceIdTime;
					$okcompile = true;//Compiled files are assumed to be ok.
					foreach ($toLoad as $infos) {
						if (($fileTime = @filemtime ($infos['file'])) !== false) {
							if ($fileTime > $compiledate) {
							   $okcompile = false;
							   break;
							}
						}
					}
				}
			} else {
				//no compile check, it's ok then
				$okcompile = true;
			}

			if ($okcompile) {
				$_loaded = array ();
				include ($_compileResourceId);//va charger _loaded
				$this->_messages = $_loaded;
				$this->_ready = true;
				//everything was loaded.
				return;
			}
		}

		//loads the founded resources.
		foreach ($toLoad as $infos) {
           $this->_loadResources ($infos['file'], $pCountry);
		}
		
		//indique que les locales sont chargées
		$this->_ready = true;

		//we want to use the PHP compilation of the resources.
        $generator = new CopixPHPGenerator ();
        $_resources = $generator->getPHPTags ($generator->getVariableDeclaration ('$_loaded', $this->_messages));
		CopixFile::write ($_compileResourceId, $_resources);
	}

	/**
     * Retourne l'identifiant de compilation d'une ressource pour un couple langue / pays
     * 
     * @param string $pCountry Pays
     * @return string
     */
	private function _getCompileId () {
		$realName = str_replace (array (':', '|'), array ('_', '~'), $this->_resource . '~' . $this->_lang . '_' . $this->_country);
		return COPIX_CACHE_PATH . 'locales/' . $realName . '.php';
	}

	/**
	 * Retourne les fichiers de cache des traductions pour le module demandé
	 *
	 * @param string $pModule Nom du module
	 * @return array
	 */
	public static function getCacheFilesName ($pModule) {
		return CopixFile::glob (COPIX_CACHE_PATH . 'locales/' . $pModule . '~*.php');
	}

	/**
     * Charge les ressources pour un pays donné
     * 
     * @param string $pPath Chemin du fichier à lire
     * @param string $pCountry Pays
     */
	private function _loadResources ($pPath, $pCountry) {
		if (($f = @fopen ($pPath, 'r')) !== false) {
		    $key = null;//juste pour ne pas avoir un warning d'existence 
		    // de la variable $key dans les analyseurs de code.

			$multiline = false;
			$linenumber = 0;
			while (!feof ($f)) {
				if ($line = fgets ($f, 1024)) {
					// length required for php < 4.2
					$linenumber++;
					if ($multiline) {
						if (preg_match ("/^([^#]+)(\#?.*)$/", $line, $match)) {
							// toujours vrai en fait
							$value = trim ($match[1]);
							if (strpos ($value, "\\u") !== false) {
								$value = $this->_utf16 ($value);
							}
							if ($multiline = (substr ($value, -1) == "\\")) {
   								$this->_messages[$key] .= substr ($value, 0, -1);
							} else {
								$this->_messages[$key] .= $value;
							}
						}
					} else if (preg_match ("/^\s*(([^#=]+)=([^#]+))?(\#?.*)$/", $line, $match)) {
						if ($match[1] != '') {
							// on a bien un cle=valeur
							$value = trim ($match[3]);
							if ($multiline = (substr ($value, -1) == "\\")) {
								$value = substr ($value, 0, -1);
							}

							$key = trim($match[2]);

							if (strpos ($match[1], "\\u" ) !== false) {
								$key = $this->_utf16 ($key);
								$value = $this->_utf16 ($value);
							}
							$this->_messages[$key] = $value;
						} else {
							if ($match[4] != '' && substr ($match[4], 0, 1) != '#') {
								fclose ($f);
								throw new CopixI18NException (
									$this->_lang, $pCountry,
									_i18n ('copix:copix.error.i18n.syntaxError', array ($pPath, $linenumber)),
									CopixI18NException::SYNTAX_ERROR
								);
							}
						}
					} else {
						fclose ($f);
						throw new CopixI18NException (
							$this->_lang, $pCountry,
							_i18n ('copix:copix.error.i18n.syntaxError', array ($pPath, $linenumber)),
							CopixI18NException::SYNTAX_ERROR
						);
					}
				}
			}
			fclose ($f);
		}
	}

	/**
     * Conversion d'une chaine UTF-8 en chaine utilisable pour le HTML
     * 
     * @param string $pStr Chaine UTF-8 à convertir
     * @return string
     */
	private function _utf16 ($pStr) {
		while (preg_match ("/\\\\u[0-9A-F]{4}/", $pStr, $unicode)) {
			$repl = "&#" . hexdec ($unicode[0]) . ";";
			$pStr = str_replace ($unicode[0], $repl, $pStr);
		}
		return $pStr;
	}
}