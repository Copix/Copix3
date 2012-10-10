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
 * @package copix
 * @subpackage auth
 */
class CopixCredentialHandlerFactory {
	/**
	 * Handlers déjà instanciés
	 *
	 * @var ICopixCredentialsHandler[]
	 */
	private static $_handlers = array ();

	/**
	 * Création d'un handler
	 *
	 * @param string $pHandlerId Identifiant du handler à créer
	 * @return ICopixCredentialsHandler
	 * @throws CopixUserException Handler de credential inconnu, code CopixUserException::UNDEFINED_CREDENTIAL_HANDLER
	 */
	public static function create ($pHandlerId) {
		if (!isset (self::$_handlers[$pHandlerId])) {
			try {
				self::$_handlers[$pHandlerId] = _ioClass ($pHandlerId);
			} catch (Exception $e) {
				throw new CopixUserException (
				_i18n ('copix:copixauth.error.undefinedCredentialHandler', $pHandlerId),
				CopixUserException::UNDEFINED_CREDENTIAL_HANDLER
				);
			}
		}
		return self::$_handlers[$pHandlerId];
	}

	/**
	 * Récupération de la liste des CredentialHandlers
	 * 
	 * @return array 
	 */
	public static function getList (){
		return CopixModule::getParsedModuleInformation ('copix|credentialhandlers',
													'/moduledefinition/credentialhandlers/credentialhandler', 
													array ('CopixAuthParserHandler', 'parseCredentialHandler'));
	}	
}