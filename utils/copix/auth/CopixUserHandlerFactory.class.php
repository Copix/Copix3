<?php
/**
 * @package		copix
 * @subpackage 	auth
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * @package		copix
 * @subpackage 	auth
 */
interface ICopixUserHandler {
	public function login  ($pParams);
	public function logout ($pParams);
	public function getInformations ($pUserId);
	public function find ($pParams = array ());
}

/**
 * Fabrique des gestionnaire d'utilisateurs
 * @package		copix
 * @subpackage 	auth
 */
class CopixUserHandlerFactory {
	/**
	 * Handlers déjà instanciés
	 *
	 * @var unknown_type
	 */
	private static $_handlers = array ();
	
	/**
	 * Création d'un handler
	 * @param string $pHandlerId identifiant du handler à créer
	 * @return CopixUserHandler
	 */
	public static function create ($pHandlerId){
		if (! isset (self::$_handlers[$pHandlerId])){
			try {
				self::$_handlers[$pHandlerId] = _ioClass ($pHandlerId);
			}catch (Exception $e){
				throw new CopixUserException ('Handler '.$pHandlerId.' non défini');
			}
		}
		return self::$_handlers[$pHandlerId];
	} 
}
?>