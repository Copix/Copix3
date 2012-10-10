<?php
/**
 * @package copix
 * @subpackage auth
 * @author Gérald Croës
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

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
	private static $_handlers = array ();

	/**
	 * Création d'un handler
	 * 
	 * @param mixed $pHandlerId Identifiant du handler à créer
	 * @return ICopixGroupHandler
	 * @throws CopixUserException Handler de group inconnu, code CopixUserException::UNDEFINED_GROUP_HANDLER
	 */
	public static function create ($pHandlerId) {
		if (!isset (self::$_handlers[$pHandlerId])) {
			try {
				self::$_handlers[$pHandlerId] = _ioClass ($pHandlerId);
			} catch (Exception $e) {
				throw new CopixUserException (
					_i18n ('copix:copixauth.error.undefinedGroupHandler', $pHandlerId),
					CopixUserException::UNDEFINED_GROUP_HANDLER
				);
			}
		}
		return self::$_handlers[$pHandlerId];
	}
	
	/**
	 * Récupération de la liste des GroupHandlers
	 * 
	 * @return array 
	 */
	public static function getList (){
		return CopixModule::getParsedModuleInformation ('copix|grouphandlers',
													'/moduledefinition/grouphandlers/grouphandler', 
													array ('CopixAuthParserHandler', 'parseGroupHandler'));
	}
	
	/**
	 * Récupération de la liste des Groupes dans leurs handler
	 * 
	 * @return array 
	 */
	public static function getAllGroupList ($exceptGroupHandler = array()){
		$toReturn = array();
		$groupsHandler =  self::getList();
		foreach ($groupsHandler as $groupHandlerName => $properties){
			if(!in_array($groupHandlerName, $exceptGroupHandler)){
				$groupHandler = _class($groupHandlerName);
				$list = $groupHandler->getGroupList();
				$toReturn[$groupHandlerName] = $list; 
			}
		}
		return $toReturn;
	}
	
	/**
	 * Récupération de la liste des groupse sous la forme handler~Groupes
	 * @param array $exceptGroupHandler tableau des groupes ne devant pas être retournés 
	 * @return array 
	 */
	public static function getAllGroupListSimple ($exceptGroupHandler = array()){
		$toReturn = array();
		$groupsHandler =  self::getAllGroupList();
		foreach ($groupsHandler as $groupHandlerName => $groups){
			if(!in_array($groupHandlerName, $exceptGroupHandler)){
				foreach ($groups as $groupkey => $label){
					$toReturn[$groupHandlerName.'~'.$groupkey] = $label; 
				}	
			}
		}
		return $toReturn;
	}
	
	public static function getGroupLabels(){
		$toReturn = array();
		$groupsHandler =  self::getList();
		foreach ($groupsHandler as $groupHandlerName => $properties){
			$groupHandler = _class($groupHandlerName);
			$label = $groupHandler->getLabel();
			$toReturn[$groupHandlerName] = $label; 
		}
		return $toReturn;
	}
	
	
}