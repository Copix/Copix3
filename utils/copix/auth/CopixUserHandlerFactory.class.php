<?php
/**
 * @package		copix
 * @subpackage	auth
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser Public Licence, see LICENCE file
 */

/**
 * Interface des classes décrivant un utilisateur.
 *
 */
interface ICopixUser {
	
	/**
	 * Retourne le libellé de l'utilisateur.
	 *
	 * @return string
	 */
	public function getCaption();
	
	/**
	 * Retourne le login de l'utilisateur.
	 * 
	 * @return string
	 */
	public function getLogin();

	/**
	 * Retourne l'identifiant technique de l'utilisateur. 
	 *
	 * @return mixed
	 */
	public function getId();
	
	/**
	 * Retourne le nom du handler responsable de cet utilisateur.
	 *
	 * @return string
	 */
	public function getHandler();
	
}

/**
 * Interface pour les handlers d'utilisateur
 * 
 * @package		copix
 * @subpackage	auth
 */
interface ICopixUserHandler {
	/**
	 * Demande de connexion
	 * 
	 * @param array $pParams Paramètres envoyés à la demande de login
	 */
	public function login ($pParams);
	
	/**
	 * Demande de déconnexion
	 * 
	 * @param array $pParams Paramètres envoyés à la demande de login
	 */
	public function logout ($pParams);
	
	/**
	 * Informations sur l'utilisateur
	 * 
	 * @param mixed Identifiant de l'utilisateur
	 * @return ICopixUser
	 */
	public function getInformations ($pUserId);
	
	/**
	 * Recherche d'utilisateurs
	 * 
	 * @param array $pParams Critères de recherche (id, login et caption au minimum)
	 * @return array of ICopixUser
	 */
	public function find ($pParams = array ());
}

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
	 * @throws CopixUserException
	 */
	public static function create ($pHandlerId) {
		if (!isset (self::$_handlers[$pHandlerId])) {
			try {
				self::$_handlers[$pHandlerId] = _ioClass ($pHandlerId);
			} catch (Exception $e) {
				throw new CopixUserException (_i18n ('copix:copixuser.error.undefinedUserHandler', $pHandlerId));
			}
		}
		return self::$_handlers[$pHandlerId];
	} 
}
?>