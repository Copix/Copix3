<?php
/**
* @package copix
* @subpackage log
* @author Nicolas Bastien, Steevan BARBOYON
* @copyright CopixTeam
* @link http://copix.org
* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

_classRequire ('developerbar|DeveloperBar');

/**
 * Log tous les messages
 *
 * @package copix
 * @subpackage log
 */
class DeveloperBarLog extends CopixLogAbstractStrategy {
	/**
	 * Retourne le nom de la stratégie
	 *
	 * @return string
	 */
	public static function getCaption () {
		return 'DeveloperBar';
	}

	/**
	 * Retourne la description de la stratégie
	 *
	 * @return string
	 */
	public static function getDescription () {
		return 'DeveloperBar strategy';
	}
	
	/**
	 * Envoi le log à la DeveloperBar
	 *
	 * @param string $pProfile Nom du profil
	 * @param string $pType Type de log
	 * @param int $pLevel Niveau de log, utiliser les constantes de CopixLog
	 * @param string $pDate Date et heure du log, format YmdHis
	 * @param string $pMessage Message à loger
	 * @param array $pExtras Informations supplémentaires
	 */
	public function log ($pProfil, $pType, $pLevel, $pDate, $pMessage, $pExtras) {
		DeveloperBar::addLog ($pProfil, $pType, $pLevel, $pDate, $pMessage, $pExtras);
	}

	/**
	 * Indique si on peut lire le contenu du profil de log indiqué
	 *
	 * @param string $pProfile Nom du profil
	 * @return false
	 */
	public function isReadable ($pProfile) {
		return false;
	}

	/**
	 * Indique si on peut écrire dans le profil de log indiqué
	 *
	 * @param string $pProfile Nom du profil
	 * @return false
	 */
	public function isWritable ($pProfile) {
		return true;
	}
}