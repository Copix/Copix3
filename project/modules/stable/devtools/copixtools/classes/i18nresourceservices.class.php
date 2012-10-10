<?php
/**
 * Classe qui représente un ensemble de clefs, pour plusieurs langues
 * @package devtools
 * @subpackage copixtools
 */
class Locales {
	/**
	 * liste des fichiers chargés
	 *
	 * @var array
	 */
	private $_files = array ();

	/**
	 * Liste des messages chargés
	 *
	 * @var array
	 */
	private $_messages = array ();
	
	/**
	 * Liste des clefs connues
	 *
	 * @var string
	 */
	private $_keys = array ();

	/**
	 * Charge un fichier et considère les clefs trouvées comme étant de langue $lang et pays $country
	 * 
	 * @param string $filePath  le nom de fichier
	 * @param string $lang      la langue des clefs chargées
	 * @param string $country   le pays des clefs 
	 */
	public function load ($filePath, $lang, $country){
		if (!isset ($this->_files[$lang][$country])){
			$this->_files[$lang][$country] = array ();
		} 
		$this->_files[$lang][$country][] = new I18NFile ((string) $filePath);
		$this->export ();
	}

	/**
	 * Sauvegarde les clefs actuellements connues pour le module $pModuleName
	 *
	 * @param string $pModuleName
	 */
	public function save ($pModuleName){
		foreach ($this->_files as $lang=>$countries){
			foreach ($countries as $country=>$datas){
				foreach ($datas as $data){
					foreach ($data->getKeys () as $key=>$value){
						if ($lang === 'all' && $country === 'all'){
							$locale = 'default'; 
						}elseif ($country === 'all'){
							$locale = $lang;
						}else{
							$locale = $lang.'_'.strtoupper ($country);
						}
						$this->_messages[$locale][$key] = $value;
					}
				}
			}
		}
		
		foreach ($this->_messages as $local=>$values){
			CopixFile::write (CopixModule::getPath ($pModuleName).'locales/'.$local.'.properties', $this->_makeFileContent ($values));
			if ($pModuleName === 'copix:'){
				CopixFile::write (COPIX_PATH.'locales/'.$local.'.properties', $this->_makeFileContent ($values));
			}
		}
	}
	
	/**
	 * Ajoute des clefs fournies sous la forme $arKeys[$lang_country][$key] = $value
	 *
	 * @param array $pKeys les clefs à ajouter au format $arKeys[$lang_country][$key] = $value
	 */
	public function addKeys ($pKeys){
		foreach ($pKeys as $locale=>$keys){
			foreach ($keys as $key=>$value){
				$this->_messages[$locale][$key] = $value;
				if (!in_array ($key, $this->_keys)){
					$this->_keys[] = $key;
				} 
			}
		}
		sort ($this->_keys);
	}

	/**
	 * Retourne la liste des clefs connues
	 *
	 * @return unknown
	 */
	public function getKeys (){
		return $this->_keys;
	}
	
	/**
	 * Récupère la liste des locales connues pour au moins l'une des clefs
	 *
	 * @return array
	 */
	public function getLocales (){
		return array_keys ($this->_messages);
	}
	
	/**
	 * Récupère la liste des traductions connues 
	 *
	 * @return string
	 */
	public function getTranslations (){
		return $this->_messages;
	}

	/**
	 * Traite l'ensemble des fichiers connus pour en extraire les clefs
	 */
	public function export (){
		foreach ($this->_files as $lang=>$countries){
			foreach ($countries as $country=>$datas){
				foreach ($datas as $data){
					foreach ($data->getKeys () as $key=>$value){
						if ($lang === 'all' && $country === 'all'){
							$locale = 'default'; 
						}elseif ($country === 'all'){
							$locale = $lang;
						}else{
							$locale = $lang.'_'.strtoupper ($country);
						}
						$this->_messages[$locale][$key] = $value;
						if (!in_array ($key, $this->_keys)){ 
							$this->_keys[] = $key;
						} 
					}
				}
			}
		}
		sort ($this->_keys);
	}
	
	/**
	 * Création du contenu d'un fichier a partir des valeurs données
	 *
	 * @param array $pValues
	 */
	public function _makeFileContent ($pValues){
		ksort ($pValues);

		$content = '';
		foreach ($pValues as $key => $value){
			$content .= $key .' = '.$value."\n"; 			
		}
		return $content; 
	}

	/**
	 * Récupère la liste des clefs manquantes par locales
	 * @return array $ar[$locale] = nombre de clefs manquantes
	 */
	public function getDiff (){
		//on va parcourir les clefs définies pour s'assurer qu'elles ont toute une valeur != null
		foreach ($this->_messages as $locale=>$values){
			foreach ($values as $key=>$value){
				if (empty ($value)){
					unset ($this->_messages[$locale][$key]);
				}
			}
		}
		
		
		//on récupère la liste des clefs pour chaque locale
		$keys = array ();
		foreach ($this->_messages as $locale=>$values){
			$keys[$locale] = array_keys ($values);
		}
		
		//on calcule maintenant la différence des clefs traduites avec les clefs connues
		foreach ($keys as $locale=>$values){
			$keys[$locale] = array_diff ($this->getKeys (), $values);
		}
		
		return $keys;
	}
}

class I18NResourceServices {
	public function find (& $pLocales){
		$list = array ();

		//On ajoute tous les modules à la liste.
		foreach (CopixModule::getList (false) as $module){
			$list[$module] = $this->_findIn ($module);
		}
		$list['copix:'] = $this->_findIn ('copix:');
	
		foreach ($list as $resource=>$locales){
			foreach ($locales as $locale){
				if (!in_array ($locale, $pLocales)){
					$pLocales[] = $locale;
				}
			}
		}
		
		foreach ($list as $module=>$locales){
			$list[$module] = $this->getLocales ($module);
		}

		sort ($pLocales);
		ksort ($list);
		return $list;
	}
	
	public function getLocales ($pModule){
		$list = array ();

		//On ajoute tous les modules à la liste.
		$localeFile = new Locales ();
		foreach ($this->_findIn ($pModule) as $locale){
			$parts = explode ('_', $locale);
			if (count ($parts) === 1){
				$country = 'all';
				if ($parts[0] === 'default'){
					$lang = 'all';
				}else{
					$lang = $parts[0];
				}
			}else{
				$lang = $parts[0];
				$country = $parts[1];				
			}
			
			if ($pModule !== 'copix:'){
				$localeFile->load (CopixModule::getPath ($pModule).'locales/'.$locale.'.properties', $lang, $country);
			}else{
				$localeFile->load (COPIX_PATH.'locales/'.$locale.'.properties', $lang, $country);
			}

		}

		return $localeFile;		
	}

	public function _findIn ($pModuleName){
 		if ($pModuleName !== 'copix:'){
			if (! is_readable ($path = CopixModule::getPath ($pModuleName).'locales/')){
				return array ();
			}
		}else{
			$path = COPIX_PATH.'locales/';
		}

		$directories = new RecursiveIteratorIterator (new RecursiveDirectoryIterator ($path));
		$decorator = new CopixExtensionFilterIteratorDecorator ($directories);
		$decorator->setExtension ('.properties');

		$locales = array ();
		foreach ($decorator as $file){
			$locales[] = substr ($file->getFileName (), 0, self::EXTENSION_TRASH_LENGTH);
		}
		return $locales;
	}

	const EXTENSION_TRASH_LENGTH = -11; //taille de ".properties"
}

class I18NFile {
	private $_messages = array ();

	public function countKeys (){
		return count ($this->_messages);
	}

	public function __construct ($pPath){
		$messages = array ();

		if (($f = fopen ($pPath, 'r')) !== false) {
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
	
	public function getKeys (){
		return $this->_messages;
	}

	/**
     * Conversion d'une chaine UTF-8 en chaine utilisable pour le HTML
     * 
     * @param string $pStr Chaine UTF-8 Ã  convertir
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