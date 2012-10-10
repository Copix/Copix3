<?php
/**
* @package		copix
* @subpackage 	i18n
* @author		Croës Gérald, Jouanneau Laurent
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Contient un ensemble de traductions concernant une langue donnée
* (et pour tout les pays concernés)
* @package		copix
* @subpackage	i18n
*/
class CopixI18NBundle {
	/**
	 * Le fichier du bundle
	 * @var CopixFileSelector
	 */
	private $_fic;

	/*
	 * La langue chargée
	 * @var string
	 */
	private $_lang;

	/**
	 * Les pays chargés
	 * @var array
	 */
	private $_loadedCountries = array ();

	/**
	 * Les messages chargés 
	 * @var array
	 */
	private $_messages = array ();
	
	/**
    * constructor
    * @param	CopixFileSelector	$file
    * @param	string				$lang        the language we wants to load
    */
	public function __construct ($file, $lang){
		$this->_fic  = $file;
		$this->_lang = $lang;
	}
	
	/**
	 * Retourne les messages pour la lange $this->_lang et le country $country
	 * 
	 * @param string $country Country d'ou on prend les clefs
	 * @return array
	 */
	public function getKeys ($country){
		// si on n'a pas chargé le contenu de ce fichier
		if (!isset ($this->_messages[$this->_lang])) {
			$this->_loadLocales ($this->_lang, $country);
		}
		return (isset ($this->_messages[$country])) ? $this->_messages[$country] : array ();		
	}

	/**
    * Récupère la chaine de caractère représentée par key pour le pays donné
    * @param	string	$key		la clef que l'on souhaite afficher
    * @param 	string	$country	le pays pour lequel on souhaite récupérer la clef
    * @return 	string	null si la clef n'est pas trouvée
    */
	public function get ($key, $country){
		if (!in_array ($country, $this->_loadedCountries)){
           $this->_loadLocales ($this->_lang, $country);
		}

		// check if the key exists for the specified country
		if (isset ($this->_messages[$country][$key])){
			return $this->_messages[$country][$key];
		}elseif ($country !== strtoupper ($this->_lang)){
			// the key doesn't exist for the specified country,
			// so get the key of the native country.
			//
			// If we were already using native country, skiping to the default string 
			return $this->get ($key, strtoupper ($this->_lang));
		}else{
			return null;
		}
	}

	/**
    * Loads the resources for a given lang/country.
    * will automatically loads the default (lang lang)
    * @param string $lang     the language
    * @param string $country the country
    */
	private function _loadLocales ($lang, $country){
		$this->_loadedCountries[] = $country;

		//file names for different cases.
		$bundleLang     = $this->_fic->fileName.'_'.$lang.'.properties';
		$bundleCountry  = $this->_fic->fileName.'_'.$lang.'_'.$country.'.properties';

		$path = $this->_fic->getPath (COPIX_RESOURCES_DIR);
		$toLoad[] = array ('file'=>$path . $this->_fic->fileName.'.properties', 'lang'=>'default', 'country'=>'DEFAULT');
		$toLoad[] = array ('file'=>$path . $bundleLang, 'lang'=>$lang, 'country'=>strtoupper ($lang));
		$toLoad[] = array ('file'=>$path . $bundleCountry, 'lang'=>$lang, 'country'=>$country);

		// check if we have a compiled version of the ressources
		$_compileResourceId = $this->_getCompileId ($lang, $country);
		// DDT : ajout du test file_exists
		if (file_exists ($_compileResourceId) === true && ($_compileResourceIdTime = @filemtime ($_compileResourceId)) !== false){
            $config = CopixConfig::instance ();
			if ($config->compile_check || $config->force_compile){
				if ($config->force_compile){
					//force compile, compiled files are never assumed to be ok.
					$okcompile = false;
				}else{
					// on verifie que les fichiers de ressources sont plus anciens que la version compilée
					$compiledate = $_compileResourceIdTime;
					$okcompile   = true;//Compiled files are assumed to be ok.
					foreach ($toLoad as $infos){
						if (file_exists ($infos['file']) === false || ($fileTime = @filemtime($infos['file'])) !== false){
							if (!isset ($fileTime) || $fileTime > $compiledate){
							   $okcompile = false;
							   break;
							}
						}
					}
				}
			}else{
				//no compile check, it's ok then
				$okcompile = true;
			}
			if ($okcompile) {
				$_loaded = array ();
				include ($_compileResourceId);//va charger _loaded
				$this->_messages[$country] = $_loaded;
				//everything was loaded.
				return;
			}
		}

		//loads the founded resources.
		foreach ($toLoad as $infos){
           $this->_loadResources ($infos['file'], $country);
		}

		//we want to use the PHP compilation of the resources.
        $generator  = new CopixPHPGenerator ();
        $_resources = $generator->getPHPTags ($generator->getVariableDeclaration ('$_loaded', isset ($this->_messages[$country]) ? $this->_messages[$country] : array ()));
		CopixFile::write ($_compileResourceId, $_resources);
	}

	/**
    * Récupération de l'identifiant de compilation d'une ressource pour une langue / pays
    * @param	string	$lang		le code langue pour lequel on souhaites connaitre l'identifiant
    * @param 	string 	$country	le pays pour lequel on souhaites connaitre l'identifiant
    * @return string
    */
	private function _getCompileId ($lang, $country){
		return COPIX_CACHE_PATH.'php/resources/'.str_replace (array (':', '|'), array ('~', '~'), strtolower ($this->_fic->getSelector()).'~'.$lang.'_'.$country).'.php';
	}

	/**
    * Charge les ressources pour un pays donné
    *
    * @param string	$path		Le chemin du fichier que l'on va lire
    * @param string $country	le code pays
    */
	private function _loadResources ($path, $country){
        // DDT le 2006-09-18 ajout du file_exists
		if (file_exists($path) && ($f = @fopen ($path, 'r')) !== false) {
		    $key = null;//juste pour ne pas avoir un warning d'existence 
		    // de la variable $key dans les analyseurs de code.

			$multiline = false;
			$linenumber = 0;
			while (!feof ($f)) {
				if ($line = fgets ($f, 1024)){
					// length required for php < 4.2
					$linenumber++;
					if ($multiline){
						if (preg_match ("/^([^#]+)(\#?.*)$/", $line, $match)){
							// toujours vrai en fait
							$value = trim ($match[1]);
							if (strpos ($value, "\\u") !== false){
								$value=$this->_utf16 ($value);
							}
							if ($multiline = (substr($value, -1) == "\\")){
   								$this->_messages[$country][$key] .= substr ($value,0,-1);
							}else{
								$this->_messages[$country][$key] .= $value;
							}
						}
					}elseif (preg_match ("/^\s*(([^#=]+)=([^#]+))?(\#?.*)$/", $line, $match)){
						if ($match[1] != ''){
							// on a bien un cle=valeur
							$value = trim($match[3]);
							if($multiline = (substr($value,-1) == "\\")){
								$value = substr ($value,0,-1);
							}

							$key = trim($match[2]);

							if (strpos ($match[1], "\\u" ) !== false){
								$key = $this->_utf16 ($key);
								$value = $this->_utf16 ($value);
							}
							$this->_messages[$country][$key] = $value;
						}else{
							if ($match[4] != '' && substr($match[4], 0, 1) != '#'){
								throw new CopixException (_i18n ('copix:copix.error.i18n.syntaxError', array ($path, $linenumber)));
							}
						}
					}else{
						throw new CopixException (_i18n ('copix:copix.error.i18n.syntaxError', array ($path, $linenumber)));
					}
				}
			}
			fclose ($f);
		}
	}

	/**
    * Conversion d'une chaine UTF8 en chaine utilisable pour le HTML
    * @param  	string	$str   La chaine UTF8 à convertir
    * @return	string
    */
	private function _utf16 ($str) {
		while (ereg ("\\\\u[0-9A-F]{4}", $str,$unicode)) {
			$repl = "&#".hexdec( $unicode[0] ).";";
			$str = str_replace( $unicode[0],$repl,$str );
		}
		return $str;
	}
}
?>