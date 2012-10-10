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
 * Exceptions pour la gestion de l'i18n
 *
 * @package copix
 * @subpackage i18n
 */
class CopixI18NException extends CopixException {
	/**
	 * Format de fichier XML pour les informations sur la langue invalide
	 */
	const INVALID_FILE_FORMAT = 1;

	/**
	 * Format du nom du fichier XML pour les informations sur la langue invalide
	 */
	const INVALID_FILENAME_FORMAT = 2;
	
	/**
	 * Langue inconnue
	 */
	const UNKONW_LANGUAGE = 3;
	
	/**
	 * Clef non trouvée
	 */
	const KEY_NOT_EXISTS = 4;
	
	/**
	 * Erreur de syntaxe dans un fichier properties
	 */
	const SYNTAX_ERROR = 5;
	
	/**
	 * Constructeur
	 *
	 * @param string $pLang Langue
	 * @param string $pCountry Pays
	 * @param string $pMessage Message
	 * @param int $pCode Code
	 */
	public function __construct ($pLang, $pCountry, $pMessage, $pCode = 0) {
		if ($pLang != null && $pCountry == null) {
			$pMessage = '[' . $pLang . '] ' . $pMessage;
		} else if ($pLang !== null && $pCountry !== null) {
			$pMessage = '[' . $pLang . '_' . $pCountry . '] ' . $pMessage;
		}
		parent::__construct ($pMessage, $pCode);
	}
}