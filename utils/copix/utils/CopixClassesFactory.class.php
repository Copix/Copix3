<?php
/**
 * @package copix
 * @subpackage core
 * @author Croes Gérald, Jouanneau Laurent
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Permet d'instancier des classes via les identifiant Copix
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
	 * @return mixed Le nom de la classe du sélecteur
	 * @throws CopixException Si $pStrict est vrai que l'on n'a pas pu charger le fichier ou que la classe n'existe pas
	 */
	private static function _loadClass ($pClassId, $pForceLoad = false, $pStrict = true, $pSelector = null) {
		$file = ($pSelector === null) ? CopixSelectorFactory::create ($pClassId) : $pSelector;
		$className = CopixFile::extractFileName ($file->fileName);
		$filePath = $file->getPath () . COPIX_CLASSES_DIR . $file->fileName . '.class.php' ;

		if ($pForceLoad || (!class_exists ($className, false) && !interface_exists ($className, false))) {
			if (!Copix::RequireOnce ($filePath) && $pStrict) {
				throw new CopixException (_i18n ('copix:copix.error.class.couldNotLoadClass', $pClassId), 0, self::_getExtras ($pClassId));
			}
		}
		if ($pStrict && (!class_exists ($className, true) && !interface_exists ($className, true))) {
			throw new CopixException (_i18n ('copix:copix.error.class.undefinedClass', $pClassId), 0, self::_getExtras ($pClassId));
		}
		return $className;
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
	 * @param string $pClassName Nom de la classe uniquement (sans aucun selecteur)
	 * @param array $pArgs Paramètres à passer au constructeur de la classe
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
	 * @param array $pArgs Arguments Paramètres à passer au constructeur de la classe
	 * @return object
	 */
	public static function create ($pClassId, $pArgs = null) {
		return self::_createInstance (self::_loadClass ($pClassId), $pArgs);
	}

	/**
	 * Même chose que create, à la différence que l'on gère un singleton
	 * 
	 * @param string $pClassId Identifiant Copix de l'élément à créer
	 * @param string $pInstanceId Identifiant de l'instance à récupérer
	 * @param array $pArgs Paramètres à passer au constructeur de la classe
	 * @return object
	 */
	public static function getInstanceOf ($pClassId, $pInstanceId = 'default', $pArgs = null) {
		// Charge la classe et récupère son nom
		$className = self::_loadClass ($pClassId);
		
		//check if exists in the cache (while getting the fullIdentifier in id)
		if (!isset (self::$_cacheInstance [$className][$pInstanceId])) {
			return self::$_cacheInstance[$className][$pInstanceId] = self::_createInstance ($className, $pArgs);
		}

		return self::$_cacheInstance[$className][$pInstanceId];
	}
	
	/**
	 * Inclusion des classes du module
	 * 
	 * @param string $pDirID l'identifiant du repertoire
	 * @param CopixSelector $pSelector le sélecteur à utiliser si donné
	 * @return array Tableau des instances chargées
	 */
	private static function _dirInclude ($pDirId, $pSelector = null) {
		static $arCache = array ();
		if (array_key_exists ($pDirId, $arCache)) {
			return $arCache[$pDirId];
		}

		$arReturn = array ();
		$dir = ($pSelector === null) ? CopixSelectorFactory::create ($pDirId) : $pSelector;
		$directoy = dir ($dir->getPath () . COPIX_CLASSES_DIR);
		while ($class = $directoy->read ()) {
			if (substr ($class, strlen ($class) - 10) !== '.class.php') {
				//on ne garde que les fichiers de classe
				continue;
			}
			try {
				$arReturn[] = self::fileInclude ($pDirId . substr ($class, 0, strlen ($class) - 10));
			} catch (Exception $e) {}
		}
		
		return $arCache[$pDirId] = $arReturn;
	}
	
	/**
	 * Inclusion du fichier de la classe
	 * 
	 * @param string $pClassID Identifiant de la classe dont on veut inclure le fichier de définition
	 * @return mixed Nom de la classe si on n'inclu qu'une seule classe, tableau avec le nom des classes si on inclu un répertoire
	 */
	public static function fileInclude ($pClassId) {
		$file = CopixSelectorFactory::create ($pClassId);
		if ($file->fileName == '') {
			return self::_dirInclude ($pClassId, $file);
		}		
		return self::_loadClass ($pClassId, false, false, $file);
	}
}