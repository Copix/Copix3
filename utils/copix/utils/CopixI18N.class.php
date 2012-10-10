<?php

/**
* @package		copix
* @subpackage 	i18n
* @author		Croës Gérald, Jouanneau Laurent
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Classe qui permet de gérer des contenus différents en fonction des langues / pays.
 * 
 * Les constantes sont gérées dans des fichiers .properties dans les répertoires resources
 * des différents modules.
 * 
 * Cette classe est destinée à être utilisée dans les sources PHP. Si vous souhaitez 
 * manipuler des clefs dans un template smarty, utilisez {i18n key=clef}
 * 
 * <code>
 *  echo CopixI18N::get ('code.chaine');//va chercher la clef dans resources/code_fr.properties si l'on est en français.
 * </code>
 *  
 * @package copix
 * @subpackage i18n
 */
class CopixI18N {
	/**
	* Les modules déja chargés
	* @var array
	*/
	private static $_bundles; //[module][lang]

	/**
	 * La code langue courante (false si aucun défini)
	 * @var string
	 */
	private static $_lang = false;

	/**
	 * Le code pays courant (false si aucun défini)
	 * @var string
	 */
	private static $_country = false;

	/**
	 * Le charset courant (false si aucun encore défini)
	 * @var string
	 */
	private static $_charset = false;

	/**
	 * Récupération de la langue courante
	 * @return string
	 */
	public static function getLang() {
		if (self :: $_lang === false) {
			self :: $_lang = CopixConfig :: instance()->default_language;
		}
		return self :: $_lang;
	}

	/**
	 * Récupération du charset courant
	 * @return string
	 */
	public static function getCharset() {
		if (self :: $_charset === false) {
			self :: $_charset = CopixConfig :: instance()->default_charset;
		}
		return self :: $_charset;
	}

	/**
	 * Définition du charset à utiliser
	 * @param	string	$pCharset	le charset à utiliser
	 */
	public static function setCharset($pCharset) {
		self :: $_charset = $pCharset;
	}

	/**
	 * Définition de la langue à utiliser
		 * <code>
	 * //on demande à utiliser le français
	 * CopixI18N::setLang ('fr');
	 * </code>
	
	 * @param	string	$pLang 	La langue à définir
	 */
	public static function setLang($pLang) {
		self :: $_lang = strtolower($pLang);
	}

	/**
	* Récupération du format de date en fonction de la langue.
	* @param	string	$separator	le séparateur à utiliser
	* @return string
	*/
	public static function getDateFormat ($separator = '/') {
		switch ($lang = self::getLang ()) {
			case 'fr' :
			case 'it' :
				$format = "d" . $separator . "m" . $separator . "Y";
				break;
			case 'en' :
				$format = "m" . $separator . "d" . $separator . "Y";
				break;
			default :
				$country = self::getCountry ();
				throw new CopixException (self::get ('copix:copix.error.i18n.unknowDateFormat', array ($lang, $country)));
		}
		return $format;
	}
	
	/**
	 * Récupération du format de date et heure en fonction de la langue
	 * 
	 * @param string $separator Le séparateur à utiliser
	 * @return string
	 */
	public static function getDateTimeFormat ($separator) {
		switch ($lang = self::getLang ()) {
			case 'fr' :
			case 'it' :			
				$format = "d" . $separator . "m" . $separator . "Y H:i:s";
				break;
			case 'en' :
				$format = "m" . $separator . "d" . $separator . "Y h:i:s a";
				break;
			default :
				$country = self::getCountry ();
				throw new CopixException (self::get('copix:copix.error.i18n.unknowDateFormat', array ($lang, $country)));
		}
		return $format;
	}
	
	/**
	 * Récupère le masque pour la date et l'heure, en fonction de la langue
	 * 
	 * @param string $separator Le séparateur à utiliser
	 * @return object Propriétés : mask et format
	 */
	public static function getDateTimeMask ($separator) {
		$toReturn = new stdClass ();
		switch ($lang = self :: getLang()) {
			case 'fr' :
			case 'it' :			
				$mask = "%d" . $separator . "%d" . $separator . "%d %d:%d:%d";
				$format = array (
					'd',
					'm',
					'y',
					'H',
					'i',
					's'
				);
				break;
			case 'en' :
				$mask = "%d" . $separator . "%d" . $separator . "%d %d:%d:%d %s";
				$format = array (
					'm',
					'd',
					'y',
					'h',
					'i',
					's',
					'p'
				);
				break;
			default :
				$country = self::getCountry ();
				throw new CopixException (self::get ('copix:copix.error.i18n.unknowDateFormat', array ($lang, $country)));
		}
		$toReturn->mask = $mask;
		$toReturn->format = $format;
		return $toReturn;
	}

	/**
	* Récupération du pays courant
	* @return string
	*/
	public static function getCountry() {
		if (self :: $_country === false) {
			self :: $_country = CopixConfig :: instance()->default_country;
		}
		return self :: $_country;
	}

	/**
	 * Définition du pays à utiliser
	 * <code>
	 * //on demande à utiliser le pays france
	 * CopixI18N::setCountry ('FR');
	 * </code>
	 * @param	string	$pCountry 	Le code du pays à définir
	 */
	public static function setCountry($pCountry) {
		self :: $_country = strtoupper($pCountry);
	}

	/**
	* gets the correct string, for a given language.
	*   if it can't get the correct language, it will try to gets the string
	*   from the default language.
	*/
	public static function get($key, $args = null, $locale = null, $trigger = true) {
		//finds out required lang / coutry
		if ($locale === null) {
			$lang = self :: getLang();
			$country = self :: getCountry();
		} else {
			$ext = explode('_', $locale);
			if (count($ext) > 1) {
				$lang = $ext[0];
				$country = $ext[1];
			} else {
				$lang = $ext[0];
				$country = strtoupper($ext[0]);
			}
		}

		//Gets the bundle for the given language.
		$keySelector = substr($key, 0, strpos($key, '.'));
		$key = self :: _extractMessageKey($key);
		$bundle = self :: getBundle($keySelector, $lang);

		//try to get the message from the bundle.
		$string = $bundle->get($key, $country);
		if ($string === null) {
			//if the message was not found, we're gonna
			//use the default language and country.
			if (($lang == CopixConfig :: instance()->default_language) && ($country == CopixConfig :: instance()->default_country)) {
				if (CopixConfig :: instance()->i18n_missingKeyLaunchException === true) { 
					if ($key === 'copix.error.i18n.keyNotExists') {
						$msg = 'Can\'t find message key (which should actually be THIS message): ' . $key;
					} else {
						$msg = self :: get('copix:copix.error.i18n.keyNotExists', array (
							$key,
						CopixContext :: get()));
					}
					throw new CopixException ($msg);
				}
				return $key;
			}
			return self :: get($key, $args, CopixConfig :: instance()->default_language . '_' . CopixConfig :: instance()->default_country);
		} else {
			//here, we know the message
			if ($args !== null) {
				$string = call_user_func_array('sprintf', array_merge(array (
					$string
				), is_array($args) ? $args : array (
					$args
				)));
			}
			return $string;
		}
	}

	/**
	* tells if the given key exists
	*/
	public static function exists($key, $locale = null) {
		//finds out required lang / coutry
		if ($locale === null) {
			$lang = self :: getLang();
			$country = self :: getCountry();
		} else {
			$ext = explode('_', $locale);
			if (count($ext) > 1) {
				$lang = $ext[0];
				$country = $ext[1];
			} else {
				$lang = $ext[0];
				$country = $ext[0];
			}
		}

		//Gets the bundle for the given language.
		$keySelector = substr($key, 0, strpos($key, '.'));
		$key = self :: _extractMessageKey($key);
		$bundle = self :: getBundle($keySelector, $lang);

		//try to get the message from the bundle.
		$string = $bundle->get($key, $country);
		if ($string === null) {
			//if the message was not found, we're gonna
			//use the default language and country.
			if (($lang == CopixConfig :: instance()->default_language) && ($country == CopixConfig :: instance()->default_country)) {
				return false;
			}
			return self :: exists($key, CopixConfig :: instance()->default_language . '_' . CopixConfig :: instance()->default_country);
		} else {
			return true;
		}
	}

	/**
	* extracting the message key
	* @param $key the message key we wants to extract
	* @return string the key only (withoout its file or resource informations)
	*/
	private static function _extractMessageKey($key) {
		static $knownKeys = array ();
		if (isset ($knownKeys[$key])) {
			return $knownKeys[$key];
		}
		$parsedKey = $key;
		//extracting the message id
		if (($posPipe = strpos($parsedKey, '|')) !== false) {
			$parsedKey = substr($parsedKey, $posPipe +1);
		}
		if (($posColon = strpos($parsedKey, ':')) !== false) {
			$parsedKey = substr($parsedKey, $posColon +1);
		}
		$knownKeys[$key] = $parsedKey;
		return $knownKeys[$key];
	}

	/**
	* gets the bundle for a given language.
	* @param string $bundleKey la clef du bundle
	* @param string $lang la langue dans laquelle on veut récupérer le bundle
	* @return CopixI18NBundle
	*/
	public static function getBundle($pBundleKey, $pLang) {
		$context = CopixContext :: get();
		if (isset (self :: $_bundles[$context][$pBundleKey][$pLang])) {
			return self :: $_bundles[$context][$pBundleKey][$pLang];
		}

		$fileSelector = CopixSelectorFactory :: create($pBundleKey);
		$s = $fileSelector->getSelector();
		if (!isset (self :: $_bundles[$context][$s][$pLang])) {
			self :: $_bundles[$context][$s][$pLang] = new CopixI18NBundle($fileSelector, $pLang);
			self :: $_bundles[$context][$pBundleKey][$pLang] = self :: $_bundles[$context][$s][$pLang];
		}
		return self :: $_bundles[$context][$s][$pLang];
	}
}
?>