<?php
/**
 * @package		copix
 * @subpackage	auth
 * @author		Croës Gérald
 * @copyright	CopixTeam
 * @link		http://www.copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser Public Licence, see LICENCE file
*/

/**
 * Exception de base pour les droits insufisants
 * 
 * @package		copix
 * @subpackage	auth
 */
class CopixCredentialException extends CopixException {}

/**
 * Interface pour les credentials handlers
 * 
 * @package		copix
 * @subpackage	auth
 */
interface ICopixCredentialHandler {
	/**
	 * Certifie qu'un utilisateur a un certain droit
	 * 
	 * @param string $pStringType Type de droit (ex : basic, group, module, dynamic)
	 * @param string $pString Chaine de droit, qui ne doit pas contenir le type
	 * @param CopixUser L'utilisateur courant
	 * @return bool
	 */
	public function assert ($pStringType, $pString, $pUser);
}

/**
* @package		copix
* @subpackage	auth
*/
class CopixCredentialHandlerFactory {
	/**
	 * Handlers déjà instanciés
	 * 
	 * @var array of ICopixCredentialsHandler
	 */
	private static $_handlers = array ();

	/**
	 * Création d'un handler
	 * 
	 * @param string $pHandlerId Identifiant du handler à créer
	 * @return ICopixCredentialsHandler
	 * @throws CopixUserException
	 */
	public static function create ($pHandlerId) {
		if (!isset (self::$_handlers[$pHandlerId])) {
			try {
				self::$_handlers[$pHandlerId] = _ioClass ($pHandlerId);
			} catch (Exception $e) {
				throw new CopixUserException (_i18n ('copix:copixuser.error.undefinedCredentialHandler', $pHandlerId));
			}
		}
		return self::$_handlers[$pHandlerId];
	}
}
?>