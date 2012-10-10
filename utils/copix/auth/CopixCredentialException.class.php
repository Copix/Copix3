<?php
/**
 * @package copix
 * @subpackage auth
 * @author Croës Gérald
 * @copyright CopixTeam
 * @link http://www.copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser Public Licence, see LICENCE file
 */

/**
 * Exception de base pour les droits insufisants
 *
 * @package copix
 * @subpackage auth
 */
class CopixCredentialException extends CopixException {
	/**
	 * Constructeur
	 *
	 * @param string $pCredential Credential non respecté. Le message d'erreur sera généré automatiquement
	 */
	public function __construct ($pCredential, $pCode = 0, $pExtras = array ()) {
		parent::__construct ($pCredential, $pCode, $pExtras);
	}
}