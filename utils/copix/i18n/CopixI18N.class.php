<?php
/**
 * @package copix
 * @subpackage i18n
 * @author Croës Gérald, Jouanneau Laurent, Steevan BARBOYON, Duboeuf Damien
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Permet de gérer des contenus différents en fonction des langues / pays
 * 
 * @package copix
 * @subpackage i18n
 */
class CopixI18N {
	/**
	 * Traduction des noms de constantes
	 * 
	 * @see Zend_Locale_Data_Translation
	 * 
	 * @var array
	 */
	private static $_localeTranslation = array(
	    'Australia'       => 'AU',
	    'Austria'         => 'AT',
	    'Belgium'         => 'BE',
	    'Brazil'          => 'BR',
	    'Canada'          => 'CA',
	    'China'           => 'CN',
	    'Czech Republic'  => 'CZ',
	    'Denmark'         => 'DK',
	    'Finland'         => 'FI',
	    'France'          => 'FR',
	    'Germany'         => 'DE',
	    'Greece'          => 'GR',
	    'Hong Kong SAR'   => 'HK',
	    'Hungary'         => 'HU',
	    'Iceland'         => 'IS',
	    'Ireland'         => 'IE',
	    'Italy'           => 'IT',
	    'Japan'           => 'JP',
	    'Korea'           => 'KP',
	    'Mexiko'          => 'MX',
	    'The Netherlands' => 'NL',
	    'New Zealand'     => 'NZ',
	    'Norway'          => 'NO',
	    'Poland'          => 'PL',
	    'Portugal'        => 'PT',
	    'Russia'          => 'RU',
	    'Singapore'       => 'SG',
	    'Slovakia'        => 'SK',
	    'Spain'           => 'ES',
	    'Sweden'          => 'SE',
	    'Switzerland'     => 'CH',
	    'Taiwan'          => 'TW',
	    'Turkey'          => 'TR',
	    'United Kingdom'  => 'GB',
	    'United States'   => 'US',

	    'Chinese'         => 'zh',
	    'Czech'           => 'cs',
	    'Danish'          => 'da',
	    'Dutch'           => 'nl',
	    'English'         => 'en',
	    'Finnish'         => 'fi',
	    'French'          => 'fr',
	    'German'          => 'de',
	    'Greek'           => 'el',
	    'Hungarian'       => 'hu',
	    'Icelandic'       => 'is',
	    'Italian'         => 'it',
	    'Japanese'        => 'ja',
	    'Korean'          => 'ko',
	    'Norwegian'       => 'no',
	    'Polish'          => 'pl',
	    'Portuguese'      => 'pt',
	    'Russian'         => 'ru',
	    'Slovak'          => 'sk',
	    'Spanish'         => 'es',
	    'Swedish'         => 'sv',
	    'Turkish'         => 'tr'
    );

	/**
	 * Les locales acceptées
	 * 
	 * @see Zend_Locale
	 *
	 * @var array
	 */
	private static $_localeData = array(
	    'default' => true, 'aa_DJ' => true, 'aa_ER' => true, 'aa_ET' => true, 'aa'    => true,
	    'af_NA' => true, 'af_ZA' => true, 'af'    => true, 'ak_GH' => true, 'ak'    => true,
	    'am_ET' => true, 'am'    => true, 'ar_AE' => true, 'ar_BH' => true, 'ar_DZ' => true,
	    'ar_EG' => true, 'ar_IQ' => true, 'ar_JO' => true, 'ar_KW' => true, 'ar_LB' => true,
	    'ar_LY' => true, 'ar_MA' => true, 'ar_OM' => true, 'ar_QA' => true, 'ar_SA' => true,
	    'ar_SD' => true, 'ar_SY' => true, 'ar_TN' => true, 'ar_YE' => true, 'ar'    => true,
	    'as_IN' => true, 'as'    => true, 'az_AZ' => true, 'az'    => true, 'be_BY' => true,
	    'be'    => true, 'bg_BG' => true, 'bg'    => true, 'bn_BD' => true, 'bn_IN' => true,
	    'bn'    => true, 'bo_CN' => true, 'bo_IN' => true, 'bo'    => true, 'bs_BA' => true,
	    'bs'    => true, 'byn_ER'=> true, 'byn'   => true, 'ca_ES' => true, 'ca'    => true,
	    'cch_NG'=> true, 'cch'   => true, 'cop_EG'=> true, 'cop_US'=> true, 'cop'   => true,
	    'cs_CZ' => true, 'cs'    => true, 'cy_GB' => true, 'cy'    => true, 'da_DK' => true,
	    'da'    => true, 'de_AT' => true, 'de_BE' => true, 'de_CH' => true, 'de_DE' => true,
	    'de_LI' => true, 'de_LU' => true, 'de'    => true, 'dv_MV' => true, 'dv'    => true,
	    'dz_BT' => true, 'dz'    => true, 'ee_GH' => true, 'ee_TG' => true, 'ee'    => true,
	    'el_CY' => true, 'el_GR' => true, 'el'    => true, 'en_AS' => true, 'en_AU' => true,
	    'en_BE' => true, 'en_BW' => true, 'en_BZ' => true, 'en_CA' => true, 'en_GB' => true,
	    'en_GU' => true, 'en_HK' => true, 'en_IE' => true, 'en_IN' => true, 'en_JM' => true,
	    'en_MH' => true, 'en_MP' => true, 'en_MT' => true, 'en_NZ' => true, 'en_PH' => true,
	    'en_PK' => true, 'en_SG' => true, 'en_TT' => true, 'en_UM' => true, 'en_US' => true,
	    'en_VI' => true, 'en_ZA' => true, 'en_ZW' => true, 'en'    => true, 'eo'    => true,
	    'es_AR' => true, 'es_BO' => true, 'es_CL' => true, 'es_CO' => true, 'es_CR' => true,
	    'es_DO' => true, 'es_EC' => true, 'es_ES' => true, 'es_GT' => true, 'es_HN' => true,
	    'es_MX' => true, 'es_NI' => true, 'es_PA' => true, 'es_PE' => true, 'es_PR' => true,
	    'es_PY' => true, 'es_SV' => true, 'es_US' => true, 'es_UY' => true, 'es_VE' => true,
	    'es'    => true, 'et_EE' => true, 'et'    => true, 'eu_ES' => true, 'eu'    => true,
	    'fa_AF' => true, 'fa_IR' => true, 'fa'    => true, 'fi_FI' => true, 'fi'    => true,
	    'fil'   => true, 'fo_FO' => true, 'fo'    => true, 'fr_BE' => true, 'fr_CA' => true,
	    'fr_CH' => true, 'fr_FR' => true, 'fr_LU' => true, 'fr_MC' => true, 'fr'    => true,
	    'fur_IT'=> true, 'fur'   => true, 'ga_IE' => true, 'ga'    => true, 'gaa_GH'=> true,
	    'gaa'   => true, 'gez_ER'=> true, 'gez_ET'=> true, 'gez'   => true, 'gl_ES' => true,
	    'gl'    => true, 'gu_IN' => true, 'gu'    => true, 'gv_GB' => true, 'gv'    => true,
	    'ha_GH' => true, 'ha_NE' => true, 'ha_NG' => true, 'ha'    => true, 'haw_US'=> true,
	    'haw'   => true, 'he_IL' => true, 'he'    => true, 'hi_IN' => true, 'hi'    => true,
	    'hr_HR' => true, 'hr'    => true, 'hu_HU' => true, 'hu'    => true, 'hy_AM' => true,
	    'hy'    => true, 'ia'    => true, 'id_ID' => true, 'id'    => true, 'ig_NG' => true,
	    'ig'    => true, 'ii_CN' => true, 'ii'    => true, 'is_IS' => true, 'is'    => true,
	    'it_CH' => true, 'it_IT' => true, 'it'    => true, 'iu'    => true, 'ja_JP' => true,
	    'ja'    => true, 'ka_GE' => true, 'ka'    => true, 'kaj_NG'=> true, 'kaj'   => true,
	    'kam_KE'=> true, 'kam'   => true, 'kcg_NG'=> true, 'kcg'   => true, 'kfo_NG'=> true,
	    'kfo'   => true, 'kk_KZ' => true, 'kk'    => true, 'kl_GL' => true, 'kl'    => true,
	    'km_KH' => true, 'km'    => true, 'kn_IN' => true, 'kn'    => true, 'ko_KR' => true,
	    'ko'    => true, 'kok_IN'=> true, 'kok'   => true, 'kpe_GN'=> true, 'kpe_LR'=> true,
	    'kpe'   => true, 'ku_IQ' => true, 'ku_IR' => true, 'ku_SY' => true, 'ku_TR' => true,
	    'ku'    => true, 'kw_GB' => true, 'kw'    => true, 'ky_KG' => true, 'ky'    => true,
	    'ln_CD' => true, 'ln_CG' => true, 'ln'    => true, 'lo_LA' => true, 'lo'    => true,
	    'lt_LT' => true, 'lt'    => true, 'lv_LV' => true, 'lv'    => true, 'mk_MK' => true,
	    'mk'    => true, 'ml_IN' => true, 'ml'    => true, 'mn_MN' => true, 'mn'    => true,
	    'mr_IN' => true, 'mr'    => true, 'ms_BN' => true, 'ms_MY' => true, 'ms'    => true,
	    'mt_MT' => true, 'mt'    => true, 'my_MM' => true, 'my'    => true, 'nb_NO' => true,
	    'nb'    => true, 'ne_NP' => true, 'ne'    => true, 'nl_BE' => true, 'nl_NL' => true,
	    'nl'    => true, 'nn_NO' => true, 'nn'    => true, 'nr_ZA' => true, 'nr'    => true,
	    'nso_ZA'=> true, 'nso'   => true, 'ny_MW' => true, 'ny'    => true, 'om_ET' => true,
	    'om_KE' => true, 'om'    => true, 'or_IN' => true, 'or'    => true, 'pa_IN' => true,
	    'pa_PK' => true, 'pa'    => true, 'pl_PL' => true, 'pl'    => true, 'ps_AF' => true,
	    'ps'    => true, 'pt_BR' => true, 'pt_PT' => true, 'pt'    => true, 'ro_RO' => true,
	    'ro'    => true, 'ru_RU' => true, 'ru_UA' => true, 'ru'    => true, 'rw_RW' => true,
	    'rw'    => true, 'sa_IN' => true, 'sa'    => true, 'se_FI' => true, 'se_NO' => true,
	    'se'    => true, 'sh_BA' => true, 'sh_CS' => true, 'sh_YU' => true, 'sh'    => true,
	    'sid_ET'=> true, 'sid'   => true, 'sk_SK' => true, 'sk'    => true, 'sl_SI' => true,
	    'sl'    => true, 'so_DJ' => true, 'so_ET' => true, 'so_KE' => true, 'so_SO' => true,
	    'so'    => true, 'sq_AL' => true, 'sq'    => true, 'sr_BA' => true, 'sr_CS' => true,
	    'sr_ME' => true, 'sr_RS' => true, 'sr_YU' => true, 'sr'    => true, 'ss_ZA' => true,
	    'ss'    => true, 'ssy'   => true, 'st_ZA' => true, 'st'    => true, 'sv_FI' => true,
	    'sv_SE' => true, 'sv'    => true, 'sw_KE' => true, 'sw_TZ' => true, 'sw'    => true,
	    'syr_SY'=> true, 'syr'   => true, 'ta_IN' => true, 'ta'    => true, 'te_IN' => true,
	    'te'    => true, 'tg_TJ' => true, 'tg'    => true, 'th_TH' => true, 'th'    => true,
	    'ti_ER' => true, 'ti_ET' => true, 'ti'    => true, 'tig_ER'=> true, 'tig'   => true,
	    'tn_ZA' => true, 'tn'    => true, 'to_TO' => true, 'to'    => true, 'tr_TR' => true,
	    'tr'    => true, 'ts_ZA' => true, 'ts'    => true, 'tt_RU' => true, 'tt'    => true,
	    'ug'    => true, 'uk_UA' => true, 'uk'    => true, 'und_ZZ'=> true, 'und'   => true,
	    'ur_IN' => true, 'ur_PK' => true, 'ur'    => true, 'uz_AF' => true, 'uz_UZ' => true,
	    'uz'    => true, 've_ZA' => true, 've'    => true, 'vi_VN' => true, 'vi'    => true,
	    'wal_ET'=> true, 'wal'   => true, 'wo_SN' => true, 'wo'    => true, 'xh_ZA' => true,
	    'xh'    => true, 'yo_NG' => true, 'yo'    => true, 'zh_CN' => true, 'zh_HK' => true,
	    'zh_MO' => true, 'zh_SG' => true, 'zh_TW' => true, 'zh'    => true, 'zu_ZA' => true,
	    'zu'    => true
	);

	
	/**
	 * Code de la langue courante (false si aucun défini)
	 * 
	 * @var string
	 */
	private static $_lang = NULL;

	/**
	 * Code du pays courant (false si aucun défini)
	 * 
	 * @var string
	 */
	private static $_country = NULL;

	/**
	 * Charset courant (false si aucun encore défini)
	 * 
	 * @var string
	 */
	private static $_charset = 'UTF-8';

	/**
	 * Cache des langues dont on a demandé des informations
	 *
	 * @var array
	 */
	private static $_languages = null;
	
	/**
	 * Initialise la langue à utiliser
	 */
	private static function _initLocal () {
		
		$arLocal = array ();
		$config = CopixConfig::instance();
		
		$langC = 'null';
		if ($config->i18n_use_cookie_locale) {
			$langC = CopixCookie::get('locale', 'i18n', 'null');
		}
		
		switch ($config->i18n_order) {
			case 'bec' : {
				$arLocal[] = self::getBrowserSupportedLanguages ();
				$arLocal[] = self::getEnvironnement ();
				$arLocal[] = array ($langC=>1);
				break;
			}
			case 'bce' : {
				$arLocal[] = self::getBrowserSupportedLanguages ();
				$arLocal[] = array ($langC=>1);
				$arLocal[] = self::getEnvironnement ();
				break;
			}
			case 'ecb' : {
				$arLocal[] = self::getEnvironnement ();
				$arLocal[] = array ($langC=>1);
				$arLocal[] = self::getBrowserSupportedLanguages ();
				break;
			}
			case 'ebc' : {
				$arLocal[] = self::getEnvironnement ();
				$arLocal[] = self::getBrowserSupportedLanguages ();
				$arLocal[] = array ($langC=>1);
				break;
			}
			case 'ceb' : {
				$arLocal[] = array ($langC=>1);
				$arLocal[] = self::getEnvironnement ();
				$arLocal[] = self::getBrowserSupportedLanguages ();
				break;
			}
			default :
			case 'cbe' : {
				$arLocal[] = array ($langC=>1);
				$arLocal[] = self::getBrowserSupportedLanguages ();
				$arLocal[] = self::getEnvironnement ();
				break;
			}
		}
		// Cherhe la meilleur langue valable
		foreach ($arLocal as $local){
			foreach ($local as $langTotTest=>$quality){
				if (self::localIsAvailable ($langTotTest)){
					self::getLangCountryByLocale ($langTotTest, $lang, $country);
					self::$_lang = $lang;
					self::$_country = $country;
					return;
				}
			}
		}
		// Si aucune langue n'est valable
		if (self::localIsAvailable ($config->i18n_default)) {
			self::getLangCountryByLocale ($config->i18n_default, $lang, $country);
			self::$_lang = $lang;
			self::$_country = $country;
		} else {
			throw new CopixException ('This site cannot be run in your supported langages');
		}
	}
	
	/**
	 * Sauvegarde dans un cookie le lcoal actuel
	 */
	private static function _remenberLocal () {
		CopixCookie::set('locale', self::getLocale (), 'i18n');
	}
	/**
	 * Retourn la langue courante
	 * 
	 * @return string
	 */
	public static function getLang () {
		if (self::$_lang === NULL) {
			self::_initLocal ();
		}
		return self::$_lang;
	}
	
	/**
	 * Définition de la langue à utiliser
	 * 	
	 * @param string $pLang Langue à définir
	 */
	public static function setLang ($pLang, $pRemenber=false) {
		$locale = self::getLocale ($pLang, self::getCountry ());
		if (self::localIsAvailable ($pLang)) {
			self::setCountry ('');
		} else {
			throw new CopixI18NException ($pLang, null, "Cannot set lang to $pLang. Resulting in an incorrect locale $locale.", CopixI18NException::UNKONW_LANGUAGE);
		}
		self::$_lang = $pLang;
		if ($pRemenber) {
			self::_remenberLocal ();
		}
	}
	
	/**
	 * Retourne le pays courant
	 * 
	 * @return string
	 */
	public static function getCountry () {
		if (self::$_country === NULL) {
			self::_initLocal ();
		}
		return self::$_country;
	}

	/**
	 * Définition du pays à utiliser
	 * 
	 * @param string $pCountry Code du pays
	 */
	public static function setCountry ($pCountry='', $pRemenber=false) {
		$locale = self::getLocale (self::getLang (), $pCountry);
		if (!self::localIsAvailable ($locale)) {
			throw new CopixI18NException (self::getLang (), $pCountry, "Cannot set country to $pCountry. Resulting in an incorrect locale $locale.", CopixI18NException::UNKONW_LANGUAGE);
		}
		self::$_country = strtoupper ($pCountry);
		if (self::$_country === null) {
			self::$_country = '';
		}
		if ($pRemenber) {
			self::_remenberLocal ();
		}
	}
	
	/**
	 * Test si un local est autorisé
	 * @param string $pLocale
	 * @return boolean
	 */
	public static function localIsAvailable ($pLocale) {
		return isset (self::$_localeData[$pLocale]) && CopixConfig::instance ()->localIsAvailable ($pLocale);
	}
	
	/**
	 * Retourne le charset courant
	 * 
	 * @return string
	 */
	public static function getCharset () {
		return self::$_charset;
	}

	/**
	 * Définition du charset à utiliser
	 * 
	 * @param string $pCharset Charset à utiliser
	 */
	public static function setCharset ($pCharset) {
		self::$_charset = $pCharset;
	}
	
	/**
	 * Retourne le couple langue_PAYS demandé
	 *
	 * @param string $pLang Langue dont on veut le couple, si null prend la langue courante
	 * @param string $pCountry Pays dont on veut le couple, si null prend le pays courant
	 * @return string
	 */
	public static function getLocale ($pLang = null, $pCountry = null) {
		$lang = ($pLang === null) ? self::getLang () : $pLang;
		$country = ($pCountry === null) ? self::getCountry () : $pCountry;
		// on fait == '' pour prendre le cas de null, et le cas ou on passe réellement un pays vide
		if ($lang == '' && $country == '') {
			return '';
		} else if ($country == '') {
			return $lang;
		} else {
			return $lang . '_' . $country;
		}
	}

	/**
	 * Retourne les informations voulues dans le xml de configuration de la langue demandée
	 *
	 * @param string $pPath Chemin vers la node (ex : formats/date)
	 * @param string $pLang Langue dont on veut des informations, si null langue courante
	 * @param string $pCountry Pays don on veut des informations, si null pays courant
	 * @return string
	 * @throws CopixI18NException Nom de fichier XML invalide, code CopixI18NException::INVALID_FILENAME_FORMAT
	 * @throws CopixI18NException Path non trouvé, code CopixI18NException::INVALID_FILE_FORMAT
	 * @throws CopixI18NException Langue inconnue, code CopixI18NException::UNKONW_LANGUAGE
	 */
	private static function _getXMLValue ($pPath, $pLang, $pCountry) {
		$config = CopixConfig::instance ();
		if ($pLang === null) {
			$pLang = self::getLang ();
		}
		if ($pCountry === null) {
			$pCountry = self::getCountry ();
		}
		
		// création du cache si il n'existe pas ou si on le force
		$cachePath = COPIX_TEMP_PATH . 'cache/i18n/config.php';

		//@TODO gérer le compile_check
		if (!is_readable ($cachePath) || $config->force_compile) {
			$files = CopixFile::findFiles (CopixFile::extractFilePath (__FILE__) . 'languages/');
			self::$_languages = array ();
			$formats = array ('date', 'datetime', 'datetimemask');
			foreach ($files as $file) {
				$fileName = CopixFile::extractFileName ($file);
				if (preg_match ('/^([a-z_A-Z]*).xml/', $fileName, $preg) === 0) {
					throw new CopixI18NException (null, null, self::get ('copix:copix.error.i18n.invalidFileNameFormat', $fileName), CopixI18NException::INVALID_FILENAME_FORMAT);
				}
				$xml = simplexml_load_file ($file);
				self::$_languages[$preg[1]] = self::_xmlToArray ($xml);
			}
			
			$php = new CopixPHPGenerator ();
			CopixFile::write ($cachePath, $php->getPHPTags ($php->getVariableReturn (self::$_languages)));
			
		// chargement du fichier de cache si ça n'a pas déja été fait
		} else if (self::$_languages === null) {
			self::$_languages = require ($cachePath);
		}

		// recherche de la langue à utiliser
		if ($pCountry !== null && isset (self::$_languages[$pLang . '_' . $pCountry])) {
			$lang = self::$_languages[$pLang . '_' . $pCountry];
		} else if (isset (self::$_languages[$pLang])) {
			$lang = self::$_languages[$pLang];
		} else {
			throw new CopixI18NException ($pLang, $pCountry, self::get ('copix:copix.error.i18n.unknowLanguage'), CopixI18NException::UNKONW_LANGUAGE);
		}
		
		// recherche de la node demandée
		$nodes = explode ('/', $pPath);
		$key = $lang;
		foreach ($nodes as $node) {
			if (!isset ($key[$node])) {
				throw new CopixI18NException ($pLang, $pCountry, self::get ('copix:copix.error.i18n.invalidFileFormat', array ($fileName, $pPath)), CopixI18NException::INVALID_FILE_FORMAT);
			}
			$key = $key[$node];
		}
		return $key;
	}
	
	/**
	 * Transforme une node XML en tableau
	 *
	 * @param SimpleXMLElement $pSimpleXMLElement Node à transformer
	 * @return array
	 */
	private static function _xmlToArray ($pSimpleXMLElement) {
		$toReturn = array ();
		foreach ($pSimpleXMLElement as $node => $value) {
			if (count ($value) > 0) {
				$toReturn[$node] = self::_xmlToArray ($value);
			} else {
				$toReturn[$node] = (string)$value;
			}
		}
		return $toReturn;
	}
	
	/**
	 * Récupération du format de date à passer aux fonctions date PHP, en fonction de la langue
	 * 
	 * @param string $pSeparator Séparateur à utiliser, null pour le séparateur de la langue courante
	 * @param string $pLang Langue dont on veut le format, null pour la langue courante
	 * @param string $pCountry Pays dont on veut le format, null pour le pays courant
	 * @return string
	 */
	public static function getDateFormat ($pSeparator = null, $pLang = null, $pCountry = null) {
		$separator = ($pSeparator === null) ? self::_getXMLValue ('formats/dateseparator', $pLang, $pCountry) : $pSeparator;
		return sprintf (self::_getXMLValue ('formats/date', $pLang, $pCountry), $separator);
	}
	
	/**
	 * Retourne le format de date et heure à passer aux fonctions date PHP, en fonction de la langue
	 * 
	 * @param string $pDateSeparator Séparateur à utiliser pour la date, null pour le séparateur de la langue courante
	 * @param string $pTimeSeparator Séparateur à utiliser pour l'heure, null pour le séparateur de la langue courante
	 * @param string $pLang Langue dont on veut le format, par défaut la langue courante
	 * @param string $pCountry Pays dont on veut le format, par défaut la langue courante
	 * @return string
	 */
	public static function getDateTimeFormat ($pDateSeparator = null, $pTimeSeparator = null, $pLang = null, $pCountry = null) {
		$separator = ($pDateSeparator === null) ? self::_getXMLValue ('formats/dateseparator', $pLang, $pCountry) : $pDateSeparator;
		$timeseparator = ($pTimeSeparator === null) ? self::_getXMLValue ('formats/timeseparator', $pLang, $pCountry) : $pTimeSeparator;
		return sprintf (self::_getXMLValue ('formats/datetime', $pLang, $pCountry), $separator, $timeseparator);
	}
	
	/**
	 * Retourne le masque pour la date et l'heure, en fonction de la langue
	 * 
	 * @param string $pDateSeparator Séparateur à utiliser pour la date, null pour le séparateur de la langue courante
	 * @param string $pTimeSeparator Séparateur à utiliser pour l'heure, null pour le séparateur de la langue courante
	 * @param string $pLang Langue dont on veut le format, par défaut la langue courante
	 * @param string $pCountry Pays dont on veut le format, par défaut la langue courante
	 * @return object Propriétés : mask et format
	 */
	public static function getDateTimeMask ($pDateSeparator = null, $pTimeSeparator = null, $pLang = null, $pCountry = null) {
		$separator = ($pDateSeparator === null) ? self::_getXMLValue ('formats/dateseparator', $pLang, $pCountry) : $pDateSeparator;
		$timeseparator = ($pTimeSeparator === null) ? self::_getXMLValue ('formats/timeseparator', $pLang, $pCountry) : $pTimeSeparator;
		$toReturn = new stdClass ();
		// le mask peut contenir des %d et %s, on doit donc les échapper le temps de remplacer les séparateurs
		$mask = self::_getXMLValue ('formats/datetimemask/mask', $pLang, $pCountry);
		$mask = str_replace ('%d', '___d___', $mask);
		$mask = str_replace ('%s', '___s___', $mask);
		$mask = sprintf ($mask, $separator, $timeseparator);
		$mask = str_replace ('___d___', '%d', $mask);
		$mask = str_replace ('___s___', '%s', $mask);
		$toReturn->mask = $mask;
		$toReturn->format = explode (',', self::_getXMLValue ('formats/datetimemask/format', $pLang, $pCountry));
		return $toReturn;
	}

    /**
	 * Récupération de l'indicatif téléphonique, en fonction du pays
	 *
	 * @param string $pLang Langue dont on veut le format, null pour la langue courante
	 * @param string $pCountry Pays dont on veut le format, null pour le pays courant
	 * @return string
	 */
	public static function getCallingCode ($pLang = null, $pCountry = null) {
		return self::_getXMLValue ('formats/callingcode', $pLang, $pCountry);
	}
	
	/**
	 * Cherche la ligne ou est appelé la demande i18n
	 * @param string $pFile
	 * @param string $pLine
	 */
	private static function _getLineCall (&$pFile, &$pLine) {
		
		$pFile = 'Not found';
		$pLine = 0;
		$debugs = debug_backtrace ();
		array_shift ($debugs);
		if (isset ($debugs [0])) {
			$debug = array_shift ($debugs);
			// Test si l'apelle vient de _i18n
			if (substr ($debug['file'], -17) == 'shortcuts.lib.php') {
				if (isset ($debugs [0])) {
					$debug = array_shift ($debugs);
				} else {
					return;
				}
			}
			$pFile = $debug['file'];
			$pLine = $debug['line'];
		}
	}
	
	/**
	 * Retourne le message conrespondant à la clef, pour la langue $pLocale, ou la langue courante, ou la langue par défaut
	 * 
	 * @param string $pKey Clef
	 * @param mixed String ou array, paramètre(s) %s à remplacer dans le message
	 * @param string $pLocale Force à retourne ce couple langue_PAYS
	 * 
	 * @throws CopixException $pKey non trouvée et CopixConfig::instance()->i18n_missingKeyLaunchException à true, code CopixI18NException::KEY_NOT_EXISTS
	 * @return string
	 */
	public static function get ($pKey, $pArgs = null, $pLocale = null) {
		$value = NULL;
		$config = CopixConfig::instance ();
		foreach ($config->copixi18n_getRegisteredI18nHandlers () as $handler) {
			if (($value = $handler->get ($pKey, $pArgs, $pLocale)) !== NULL) {
				break;
			}
		}
		
		self::getLangCountryByLocale ($pLocale, $lang,$country);
		
		// Action à faire si la clef n'est pas trouver
		if ($value === NULL) {
			if ($config->i18n_missingKeyLaunchException) {
				// Traduction de l'exception sans utilisé CopixI18n (car on a justement une erreur dessus)
				$message = 'This i18n key \''.$pKey.'\' not found. ';
				switch (self::getLang ()) {
					case 'fr' :$message = 'La clef i18n \''.$pKey.'\' non trouvée. '; break;
				}
				// Affiche la ligne de l'exception en mode DEVEL
				if ($config->getMode () == CopixConfig::DEVEL) {
					self::_getLineCall ($file, $line);
					$messageLine = 'Context :\''.CopixContext::get().'\' Line :\''.$line.'\' File :\''.$file.'\'';
					switch (self::getLang ()) {
						case 'fr' :$messageLine = 'Contexte :\''.CopixContext::get().'\' Ligne :\''.$line.'\' Fichier :\''.$file.'\''; break;
					}
					$message .= $messageLine;
				}
				throw new CopixI18NException ($lang, $country, $message, CopixI18NException::KEY_NOT_EXISTS);
			}
			$value = $pKey;
		}
		
		return $value;
	}
	
	/**
	 * Indique si la clef $pKey existe
	 * 
	 * @param string $pKey Clef
	 * @param string $pLocale Couple langue_PAYS dont on veut vérifier l'existance, null pour le couple courant
	 * @return bool
	 */
	public static function exists ($pKey) {
		foreach (CopixConfig::instance ()->copixi18n_getRegisteredI18nHandlers () as $handler) {
			if ($handler->exists ($pKey)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Convertir la locale passée en paramètre en tableau langue / pays
	 *
	 * @param string $pLocale la locale a convertir. Si null, donne pour la langue courante.
	 * 
	 * @return array
	 */
	public static function parseLocale ($pLocale){
		$ext = explode ('_', $pLocale);
		if (count ($ext) > 1) {
			return array ($ext[0], $ext[1]);
		} else {
			return array ($ext[0], '');
		}
	}
	
	/**
	 * Revoie la langue et la contré en fonction du $pLocal
	 * @param string $pLocale
	 * @param string $pLang
	 * @param string $pCountry
	 */
	public static function getLangCountryByLocale ($pLocale, &$pLang, &$pCountry){
		if ($pLocale) {
			$pLocale = self::parseLocale ($pLocale);
			$pLang = $pLocale[0];
			$pCountry = $pLocale[1];
		} else {
			$pLang = self::getLang ();
			$pCountry = self::getCountry ();
		}
	}
	
	/**
	 * Traduit un local en snon de constante
	 * @param string $pLocale
	 * @return string
	 */
	public static function translateLocal ($pLocale) {
		self::getLangCountryByLocale ($pLocale, $lang, $country);
		$arTranslate = array_flip (self::$_localeTranslation);
		if ($lang && $country &&
		    isset ($arTranslate[$lang]) &&
		    isset ($arTranslate[$country])) {
			return $arTranslate[$lang].' '.$arTranslate[$country];
			
		} else if ($lang && isset ($arTranslate[$lang])) {
			return $arTranslate[$lang];
		}
		return NULL;
	}
	
	/**
	 * Récupération des langues supportées par le navigateur internet.
	 * 
	 * Cette fonction est fortement inspirée du ZendFramework, dans Zend_Locale
	 * 
	 * @return array
	 */
	public static function getBrowserSupportedLanguages (){
		$httplanguages = getenv('HTTP_ACCEPT_LANGUAGE');
		$languages     = array();
		if (empty($httplanguages) === true) {
			return $languages;
		}

		$accepted = preg_split('/,\s*/', $httplanguages);

		foreach ($accepted as $accept) {
			$match  = null;
			$result = preg_match('/^([a-z]{1,8}(?:[-_][a-z]{1,8})*)(?:;\s*q=(0(?:\.[0-9]{1,3})?|1(?:\.0{1,3})?))?$/i',
			                     $accept, $match);

			if ($result < 1) {
				continue;
			}

			if (isset($match[2]) === true) {
				$quality = (float) $match[2];
			} else {
				$quality = 1.0;
			}

			$countrys = explode('-', $match[1]);
			$region   = array_shift($countrys);

			$country2 = explode('_', $region);
			$region   = array_shift($country2);

			foreach ($countrys as $country) {
				$languages[$region . '_' . strtoupper($country)] = $quality;
			}

			foreach ($country2 as $country) {
				$languages[$region . '_' . strtoupper($country)] = $quality;
			}

			if ((isset($languages[$region]) === false) || ($languages[$region] < $quality)) {
				$languages[$region] = $quality;
			}
		}

		return $languages;
	}

	/**
	 * Récupération des paramètres de langue configurés sur le système.
	 * 
	 * Le contenu de cette fonction est fortement inspiré du ZendFramework dans Zend_Locale
	 * 
	 * @return array
	 */
	public static function getEnvironnement (){
		$language      = setlocale(LC_ALL, 0);
		$languages     = explode(';', $language);
		$languagearray = array();

		foreach ($languages as $locale) {
			if (strpos($locale, '=') !== false) {
				$language = substr($locale, strpos($locale, '='));
				$language = substr($language, 1);
			}

			if ($language !== 'C') {
				if (strpos($language, '.') !== false) {
					$language = substr($language, 0, (strpos($language, '.') - 1));
				} else if (strpos($language, '@') !== false) {
					$language = substr($language, 0, (strpos($language, '@') - 1));
				}

				$splitted = explode('_', $language);
				$language = (string) $language;
				if (isset(self::$_localeData[$language]) === true) {
					$languagearray[$language] = 1;
					if (strlen($language) > 4) {
						$languagearray[substr($language, 0, 2)] = 1;
					}

					continue;
				}

				if (empty(self::$_localeTranslation[$splitted[0]]) === false) {
					if (empty(self::$_localeTranslation[$splitted[1]]) === false) {
						$languagearray[self::$_localeTranslation[$splitted[0]] . '_' .
						self::$_localeTranslation[$splitted[1]]] = 1;
					}

					$languagearray[self::$_localeTranslation[$splitted[0]]] = 1;
				}
				
			}
		}

		return $languagearray;
	}
	
	/**
	 * @deprecated
	 * @var string
	 */
	const BROWSER = 'browser';
	/**
	 * @deprecated
	 * @var string
	 */
	const ENVIRONMENT = 'environment'; 
}