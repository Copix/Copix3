<?php
/**
 * @package copix
 * @subpackage log
 * @author Landry Benguigui, Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Log avec error_log
 * 
 * @package copix
 * @subpackage log
 */
class CopixLogSystemStrategy extends CopixLogAbstractStrategy {
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
	 * Effectue un log
	 *
	 * @param string $pProfile Nom du profil
	 * @param string $pType Type de log
	 * @param int $pLevel Niveau de log, utiliser les constantes de CopixLog
	 * @param string $pDate Date et heure du log, format YmdHis
	 * @param string $pMessage Message à loger
	 * @param array $pExtras Informations supplémentaires
	 */
	public function log ($pProfile, $pType, $pLevel, $pDate, $pMessage, $pExtras) {
		$suffixe = '';
		if (isset ($pExtras['classname'])) {
			$suffixe .= '  - Class : ' . $pExtras['classname'];
		}
		if (isset ($pExtras['user'])) {
			$suffixe .= ' - User : ' . $pExtras['user'];
		}
		error_log ($log = '[CopixLogSystemStrategy] Profile : ' . $pProfile . $suffixe . ' - Level : ' . $pLevel . ' - Message : ' . $pMessage, 0);
	}
}