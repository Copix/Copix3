<?php
/**
 * @package		tools
 * @subpackage	languages
 * @author		Steevan BARBOYON
 * @copyright 	CopixTeam
 * @link 		http://copix.org
 * @license  	http://www.gnu.org/licenses/lgpl.html GNU General Lesser Public Licence, see LICENCE file
 */

_classRequire ('languages|ModuleLanguagesException');

/**
 * Informations sur un fichier de langue
 *
 * @package		tools
 * @subpackage	languages
 */
class LanguageFileInfos {
	/**
	 * Caption du couple langue_PAYS
	 *
	 * @var unknown_type
	 */
	private $_caption = null;
	
	/**
	 * Nom du fichier complet, avec langue_PAYS.properties comprit
	 *
	 * @var string
	 */
	private $_name = null;
	
	/**
	 * Nom du fichier avec seulement langue_PAYS
	 * 
	 * @var string
	 */
	private $_baseName = null;
	
	/**
	 * Langue, sur 2 caractères
	 * 
	 * @var string
	 */
	private $_lang = null;
	
	/**
	 * Pays, sur 2 caractères
	 * 
	 * @var string
	 */
	private $_country = null;
	
	/**
	 * Drapeau du pays, retour de _resource
	 * 
	 * @var string
	 */
	private $_flag = null;
	
	/**
	 * Association de la langue et du pays, avec un _ pour les séparer
	 * 
	 * @var string
	 */
	private $_langCountry = null;
	
    /**
	 * Vérifie si $pFile est un nom de fichier valide, sinon lève une exception
	 * 
	 * @param string $pFile Nom du fichier uniquement (ex : myfile_fr_FR.properties)
	 * @param boolean $pLangToCountry Remplit automatiquement le pays par la langue en majuscule si le pays n'est pas indiqué
	 * @throws ModuleLanguagesException Nom de fichier invalide
	 */
	public function __construct ($pFile, $pLangToCountry = true) {
		$fileBase = substr ($pFile, 0, (strlen ($pFile) - strlen ('.properties')));
		$array = explode ('_', $fileBase);
		$this->_name = $pFile;
		
		// fichier de la forme "monNom.properties" (langue par défaut)
		if (count ($array) == 1 || strlen ($array[count ($array) - 1]) <> 2) {
			$this->_baseName = implode ('_', $array);
			$this->_lang = null;
			$this->_country = null;
			$this->_flag = _resource ('img/flags/default.png');
			$this->_langCountry = null;

		// fichier de la forme "monNom_lang.properties"
		} else if (count ($array) >= 2 && strlen ($array[count ($array) - 1]) == 2 && strlen ($array[count ($array) - 2]) <> 2) {
			$arrayName = $array;
			unset ($arrayName[count ($arrayName) - 1]);
			$this->_baseName = implode ('_', $arrayName);
			$this->_lang = $array[count ($array) - 1];
			$this->_country = ($pLangToCountry) ? strtoupper ($this->_lang) : null;
			$this->_flag = _resource ('img/flags/' . $this->_lang . '.png');
			$this->_langCountry = $this->_lang;
			if ($this->_country !== null) {
				$this->_langCountry .= '_' . $this->_country;
			}
		
		// fichier de la forme "monNom_lang_COUNTRY.properties"
		} else if (count ($array) >= 3 && strlen ($array[count ($array) - 1]) == 2 && strlen ($array[count ($array) - 2]) == 2) {
			$arrayName = $array;
			unset ($arrayName[count ($arrayName) - 1]);
			unset ($arrayName[count ($arrayName) - 1]);
			$this->_baseName = implode ('_', $arrayName);
			$this->_lang = $array[count ($array) - 2];
			$this->_country = $array[count ($array) - 1];
			$this->_flag = _resource ('img/flags/' . $this->_lang . '_' . $this->_country . '.png');
			$this->_langCountry = $this->_lang . '_' . $this->_country;
			
		// format de fichier incorrect
		} else {
			throw new ModuleLanguagesException (_i18n ('global.error.invalidFileName', $pFile));
		}
		
		$i18nKey = ($this->_country === null) ? $this->_lang : $this->_lang . '_' . $this->_country;
		$this->_caption = (CopixI18N::exists ('iso3166.' . $i18nKey)) ? _i18n ('iso3166.' . $i18nKey) : _i18n ('iso3166.unknow');
	}
	
	/**
	 * Retourne le caption du couple langue_PAYS
	 *
	 * @return string
	 */
	public function getCaption () {
		return $this->_caption;
	}
	
	/**
	 * Retourne le nom du fichier complet, avec langue_PAYS.properties
	 *
	 * @return string
	 */
	public function getName () {
		return $this->_name;
	}
	
	/**
	 * Retourne le nom du fichier avec seulement langue_PAYS
	 * 
	 * @return string
	 */
	public function getBaseName () {
		return $this->_baseName;
	}
	
	/**
	 * Retourne la langue sur 2 caractères
	 *
	 * @return string
	 */
	public function getLang () {
		return $this->_lang;
	}
	
	/**
	 * Retourne le pays sur 2 caractères
	 *
	 * @return string
	 */
	public function getCountry () {
		return $this->_country;
	}
	
	/**
	 * Retourne le couple langue_PAYS
	 *
	 * @return string
	 */
	public function getLangCountry () {
		return $this->_langCountry;
	}
	
	/**
	 * Retourne le lien vers l'icone du drapeau
	 *
	 * @return string
	 */
	public function getFlagIcon () {
		return $this->_flag;
	}
}
?>