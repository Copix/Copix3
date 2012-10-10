<?php
/**
 * @package copix
 * @subpackage i18n
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Informations sur une langue
 * 
 * @package copix
 * @subpackage i18n
 */
class CopixI18NLanguage {
	/**
	 * Langue
	 *
	 * @var string
	 */
	private $_lang = null;
	
	/**
	 * Pays
	 *
	 * @var string
	 */
	private $_country = null;
	
	/**
	 * Couple langue_PAYS
	 * Exemples :
	 * getLang () === null et getCountry () === null : ''
	 * getLang () === 'fr' et getCountry () === null : 'fr'
	 * getLang () === 'fr' et getCountry () === 'FR' : 'fr_FR'
	 *
	 * @var string
	 */
	private $_locale = null;
	
	/**
	 * Couple langue_PAYS facile à concatener à un nom de fichier de traduction .properties
	 * Exemples :
	 * getLang () === null et getCountry () === null : ''
	 * getLang () === 'fr' et getCountry () === null : '_fr'
	 * getLang () === 'fr' et getCountry () === 'FR' : '_fr_FR'
	 * 
	 * @var string
	 */
	private $_localeForFile = null;
	
	/**
	 * Liste des modules pour lesquels cette langue est disponible
	 *
	 * @var array
	 */
	private $_modules = null;
	
	/**
	 * Indique si la langue est disponible dans Copix
	 *
	 * @var boolean
	 */
	private $_isInCopix = false;
	
	/**
	 * Constructeur
	 */
	public function __construct () {
		
	}
	
	/**
	 * Retourne la langue
	 *
	 * @return string
	 */
	public function getLang () {
		return $this->_lang;
	}
	
	/**
	 * Retourne le pays
	 *
	 * @return string
	 */
	public function getCountry () {
		return $this->_country;
	}
	
	/**
	 * Retourne le couple langue_PAYS
	 * Exemples :
	 * getLang () === null et getCountry () === null : ''
	 * getLang () === 'fr' et getCountry () === null : 'fr'
	 * getLang () === 'fr' et getCountry () === 'FR' : 'fr_FR'
	 *
	 * @return string
	 */
	public function getLocale () {
		return $this->_locale;
	}
	
	/**
	 * Retourne le couple langue_PAYS facile à concatener à un nom de fichier de traduction .properties
	 * Exemples :
	 * getLang () === null et getCountry () === null : ''
	 * getLang () === 'fr' et getCountry () === null : '_fr'
	 * getLang () === 'fr' et getCountry () === 'FR' : '_fr_FR'
	 *
	 * @return string
	 */
	public function getLocaleForFile () {
		return $this->_localeForFile;
	}
	
	/**
	 * Retourne la liste des modules pour lesquels cette langue est disponible
	 *
	 * @return array
	 */
	public function getModules () {
		return $this->_modules;
	}
	
	/**
	 * Indique si cette langue est disponible pour le framework Copix
	 *
	 * @return boolean
	 */
	public function isInCopix () {
		return $this->_isInCopix;
	}
}