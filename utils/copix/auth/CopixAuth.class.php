<?php
/**
 * @package copix
 * @subpackage auth
 * @author Gérald Croës
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser Public Licence, see LICENCE file
*/

/**
 * Gestion des informations sur l'authentification
 * 
 * @package copix
 * @subpackage auth
 */
class CopixAuth {
	/**
	 * Récupération de l'utilisateur courant
	 * 
	 * @return CopixUser
	 */
	public static function getCurrentUser () {
		if (($user = CopixSession::get ('copix|auth|user')) === null) {
			CopixSession::set ('copix|auth|user', $user = new CopixUser ());
		} else if (!($user instanceof ICopixUser)) {
			CopixSession::set ('copix|auth|user', $user = new CopixUser ());
		}
		return $user;
	}

	/**
	 * Destruction de l'utilisateur courant
	 */
	public static function destroyCurrentUser () {
		CopixSession::set ('copix|auth|user', null);
	}
}