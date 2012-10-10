<?php
/**
* @package		copix
* @subpackage 	auth
* @author		Croës Gérald
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
*/

/**
 * Exception de base pour les droits insufisants
 * @package		copix
 * @subpackage 	auth
 */
class CopixCredentialException extends CopixException {}

/**
 * @package		copix
 * @subpackage 	auth
 */
interface ICopixCredentialHandler {
	public function assert ($pStringType, $pString, $pUser);
}

/**
* @package		copix
* @subpackage 	auth
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
	 * @param string $pHandlerId identifiant du handler à créer
	 * @return ICopixCredentialsHandler
	 */
	public static function create ($pHandlerId){
		if (! isset (self::$_handlers[$pHandlerId])){
			try {
				self::$_handlers[$pHandlerId] = _ioClass ($pHandlerId);
			}catch (Exception $e){
				throw new CopixUserException ('ICopixCredentialsHandler '.$pHandlerId.' non défini');
			}
		}
		return self::$_handlers[$pHandlerId];
	}
}
?>