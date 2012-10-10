<?php
/**
 * @package copix
 * @subpackage auth
 * @author Croës Gérald
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Exceptions utilisateurs
 * 
 * @package copix
 * @subpackage auth
 */
class CopixUserException extends CopixException {
	/**
	 * Exception lorsque l'on tente de créer un handler de credential qui n'existe pas
	 */
	const UNDEFINED_CREDENTIAL_HANDLER = 1;
	
	/**
	 * Exception lorsque l'on tente de créer un handler de groupe qui n'existe pas
	 */
	const UNDEFINED_GROUP_HANDLER = 2;
	
	/**
	 * Exception lorsque l'on tente de créer un handler d'utilisateur qui n'existe pas
	 */
	const UNDEFINED_USER_HANDLER = 3;
}