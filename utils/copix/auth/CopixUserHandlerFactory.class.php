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
 * Factory des gestionnaires d'utilisateurs
 * @package		copix
 * @subpackage	auth
 */
class CopixUserHandlerFactory {
	/**
	 * Handlers déjà instanciés
	 * 
	 * @var array
	 */
	private static $_handlers = array ();
	
	/**
	 * Création d'un handler
	 * 
	 * @param string $pHandlerId Identifiant du handler à créer
	 * @return ICopixUserHandler
	 * @throws CopixUserException Handler d'utilisateur inconnu, code CopixUserException::UNDEFINED_USER_HANDLER
	 */
	public static function create ($pHandlerId) {
		if (!isset (self::$_handlers[$pHandlerId])) {
			try {
				self::$_handlers[$pHandlerId] = _ioClass ($pHandlerId);
			} catch (Exception $e) {
				throw new CopixUserException (
					_i18n ('copix:copixauth.error.undefinedUserHandler', $pHandlerId),
					CopixUserException::UNDEFINED_USER_HANDLER
				);
			}
		}
		return self::$_handlers[$pHandlerId];
	}

	/**
	 * Récupération de la liste des UserHandlers
	 * 
	 * @return array 
	 */
	public static function getList (){
		return CopixModule::getParsedModuleInformation ('copix|userhandlers',
													'/moduledefinition/userhandlers/userhandler', 
													array ('CopixAuthParserHandler', 'parseUserHandler'));
	}
}