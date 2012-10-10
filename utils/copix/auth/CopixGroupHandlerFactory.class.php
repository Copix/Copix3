<?php
/**
 * @package		copix
 * @subpackage	auth
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Interface de gestion des groupes
 * 
 * @package		copix
 * @subpackage	auth
 */
interface ICopixGroupHandler {
	/**
	 * Retourne les groupes auquel appartient l'utilisateur $pUserId
	 * 
	 * @param mixed $pUserId Identifiant de l'utilisateur
	 * @param string $pUserHandler Nom
	 * @return array Clefs : identifiants, valeurs : noms des groupes
	 */
	public function getUserGroups ($pUserId, $pUserHandler);
	
	/**
	 * Retourne des informations sur un groupe
	 * 
	 * @param mixed $pGroupId Identifiant du groupe
	 * @return object Les propriétés contiennent les informations sur le groupe
	 */
	public function getInformations ($pGroupId);
}


/**
 * Factory de gestion des groupes
 * 
 * @package		copix
 * @subpackage	auth
 */
class CopixGroupHandlerFactory {
	/**
	 * Handlers déjà instanciés
	 * 
	 * @var array
	 */
	static private $_handlers = array ();

	/**
	 * Création d'un handler
	 * 
	 * @param mixed $pHandlerId Identifiant du handler à créer
	 * @return CopixUserHandler
	 * @throws CopixUserException
	 */
	static public function create ($pHandlerId) {
		if (!isset (self::$_handlers[$pHandlerId])) {
			try {
				self::$_handlers[$pHandlerId] = _ioClass ($pHandlerId);
			} catch (Exception $e) {
				throw new CopixUserException (_i18n ('copix:copixuser.error.undefinedGroupHandler'));
			}
		}
		return self::$_handlers[$pHandlerId];
	}
}
?>