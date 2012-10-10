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
 * Interface pour les credentials handlers
 *
 * @package copix
 * @subpackage auth
 */
interface ICopixCredentialHandler {
	/**
	 * Certifie qu'un utilisateur a un certain droit
	 *
	 * @param string $pStringType Type de droit (ex : basic, group, module, dynamic)
	 * @param string $pString Chaine de droit, qui ne doit pas contenir le type
	 * @param CopixUser L'utilisateur courant
	 * @return boolean
	 */
	public function assert ($pStringType, $pString, $pUser);
}