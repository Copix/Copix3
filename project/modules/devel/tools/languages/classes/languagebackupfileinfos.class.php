<?php
/**
 * @package		tools
 * @subpackage	languages
 * @author		Steevan BARBOYON
 * @copyright 	CopixTeam
 * @link 		http://copix.org
 * @license  	http://www.gnu.org/licenses/lgpl.html GNU General Lesser Public Licence, see LICENCE file
 */

_classRequire ('languages|LanguageFileInfos');
_classRequire ('languages|ModuleLanguagesException');

/**
 * Informations sur un fichier de langue
 *
 * @package		tools
 * @subpackage	languages
 */
class LanguageBackupFileInfos extends LanguageFileInfos {
	/**
	 * Module auquel appartient ce fichier
	 *
	 * @var string
	 */
	private $_module = null;
	
	/**
	 * Timestamp de la sauvegarde du fichier
	 *
	 * @var int
	 */
	private $_timestamp = null;
	
    /**
	 * Vérifie si $pFile est un nom de fichier valide, sinon lève une exception
	 * 
	 * @param string $pFile Nom du fichier uniquement (ex : myfile_fr_FR.properties)
	 * @param string $pModule Nom du module auquel appartient ce fichier
	 * @param boolean $pLangToCountry Remplit automatiquement le pays par la langue en majuscule si le pays n'est pas indiqué
	 * @throws ModuleLanguagesException Nom de fichier invalide
	 */
	public function __construct ($pFile, $pModule, $pLangToCountry = true) {
		$file = substr ($pFile, 0, strrpos ($pFile, '.'));
		$timestamp = substr ($pFile, strrpos ($pFile, '.') + 1);
		if ($timestamp != intval ($timestamp) || intval ($timestamp) == 0) {
			throw new ModuleLanguagesException (_i18n ('backup.error.invalidBackupFileName', $pFile));
		}
		$this->_timestamp = intval ($timestamp);
		$this->_module = $pModule;
		parent::__construct ($file, $pLangToCountry);
	}
	
	/**
	 * Retourne le timestamp de la sauvegarde du fichier
	 *
	 * @return int
	 */
	public function getTimestamp () {
		return $this->_timestamp;
	}
	
	/**
	 * Retourne la date au format CopixI18N::getDateTimeFormat () de la sauvegarde du fichier
	 *
	 * @return string
	 */
	public function getDate () {
		return date (CopixI18N::getDateTimeFormat (), $this->_timestamp);
	}
	
	/**
	 * Nom du module auquel appartient cette sauvegarde
	 *
	 * @return string
	 */
	public function getModule () {
		return $this->_module;
	}
}
?>