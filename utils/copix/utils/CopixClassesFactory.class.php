<?php
/**
 * @package copix
 * @subpackage core
 * @author Croes Gérald, Jouanneau Laurent, Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Permet d'instancier des classes via les identifiants Copix
 * 
 * @package copix
 * @subpackage core
 */
class CopixClassesFactory {
	/**
     * Cache des instances
     * 
     * @var array
     */
	private static $_cacheInstance = array ();

	/**
	 * Charge la définition d'une classe
	 *
	 * @param string $pClassId Sélecteur de classe
	 * @param boolean $pForceLoad Si vrai, force l'inclusion du fichier même si la classe existe
	 * @param boolean $pStrict Si vrai, lance une exception si quelque chose se passe mal
	 * @return mixed Le nom de la classe du sélecteur ou false si l'inclusion n'a pas fonctionnée
	 * @throws CopixException Si $pStrict est vrai que l'on a pas pu charger le fichier ou que la classe n'existe pas
	 */
	private static function _loadClass ($pClassId, $pForceLoad = false, $pStrict = true) {
		$pClassId = CopixSelectorFactory::purge ($pClassId);
		
		static $loaded = array ();
		if (array_key_exists ($pClassId, $loaded)){
			return $loaded[$pClassId];
		}
		
		$exists = class_exists ($pClassId) || interface_exists ($pClassId);

		if ($pForceLoad && !$exists){
			throw new CopixException (_i18n ('copix:copix.error.class.couldNotLoadClass', $pClassId), 0, self::_getExtras ($pClassId));			
		}

		return $loaded[$pClassId] = ($exists ? $pClassId : false);
	}
	
	/**
	 * Retourne des informations à ajouter dans les extras des exceptions
	 * 
	 * @param string $pClassId Chaine de chargement de classe
	 * @return array
	 */
	private static function _getExtras ($pClassId) {
		$caller = CopixDebug::getCaller (2);
		$toReturn = array ();
		$toReturn['[Caller] Fichier'] = $caller['file'];
		$toReturn['[Caller] Ligne'] = $caller['line'];
		$toReturn['[Caller] Fonction'] = $caller['function'];
		$toReturn['[Caller] ClassId'] = $pClassId;
		return $toReturn;
	}
	
	/**
	 * Retourne une instance de la classe
	 *
	 * @param string $pClassName Nom de la classe uniquement, sans aucun sélecteur
	 * @param array $pArgs Paramètres de création de la classe
	 * @return object
	 */
	private static function _createInstance ($pClassName, $pArgs = null) {
		if (!is_null ($pArgs) && !is_array ($pArgs)) {
			return new $pClassName ($pArgs);
		} else if (is_null ($pArgs)) {
			return new $pClassName ();
		}
		$reflectionObj = new ReflectionClass ($pClassName);
		return $reflectionObj->newInstanceArgs ($pArgs);
	}

	/**
     * Retourne la création d'un objet du type de la classe demandée, via son identifiant Copix
     * 
     * @param string $pClassId Identifiant de la classe
     * @param array $pArgs Paramètres de création de la classe
     * @return object
     */
	public static function create ($pClassId, $pArgs = null) {
		return self::_createInstance (self::_loadClass ($pClassId, true), $pArgs);
	}

	/**
     * Même chose que create, à la différence que l'on gère un singleton
     * 
     * @param string $pClassId Identifiant Copix de l'élément à créer
     * @param string $pInstanceId Identifiant de l'instance à récupérer
	 * @param array $pArgs Paramètres de création de la classe
	 * @return object
     */
	public static function getInstanceOf ($pClassId, $pInstanceId = 'default', $pArgs = null) {
		// Charge la classe et récupère son nom
		$className = self::_loadClass ($pClassId, true);
		
		//check if exists in the cache (while getting the fullIdentifier in id)
		if (!isset (self::$_cacheInstance[$className][$pInstanceId])) {
			return self::$_cacheInstance[$className][$pInstanceId] = self::_createInstance ($className, $pArgs);
		}

		return self::$_cacheInstance[$className][$pInstanceId];
	}
	
	/**
     * Inclusion du fichier de la classe
     * 
     * @param string $pClassID Identifiant de la classe dont on veut inclure le fichier de définition
     * @return mixed Nom de la classe si on n'inclu qu'une seule classe, tableau avec le nom des classes si on inclu un répertoire, false si on n'a rien inclu ou que la classe n'exist epas
     */
	public static function fileInclude ($pClassId) {
		return self::_loadClass ($pClassId);
	}
	
	/**
	 * Inclu le fichier d'une classe, et génère une exception si une erreur s'est produite
	 *
	 * @param string $pClassId Identifiant de la classe
	 * @return string Nom de la classe
	 */
	public static function fileRequire ($pClassId) {
		return self::_loadClass ($pClassId, true);
	}
}