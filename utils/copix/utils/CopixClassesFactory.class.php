<?php
/**
* @package  	copix
* @subpackage	core
* @author		Croes Gérald, Jouanneau Laurent
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* permet d'instancier des classes via les identifiant Copix
* @package copix
* @subpackage	core
*/
class CopixClassesFactory {
	/**
    * cache des instances
    * @var array
    * @access private
    */
	private static $_cacheInstance = array ();

	/**
	 * Charge la définition d'une classe.
	 *
	 * @param string $pClassId Sélecteur de classe.
	 * @param boolean $pForceLoad Si vrai, force l'inclusion du fichier même si la classe existe.
	 * @param boolean $pStrict Si vrai, lance une exception si quelque chose se passe mal.
	 * @return mixed Le nom de la classe du sélecteur ou false si l'inclusion n'a pas fonctionné.
	 * @throws CopixException si $pStrict est vrai que l'on a pas pu charger le fichier ou que la classe n'existe pas.
	 */
	static private function _loadClass($pClassId, $pForceLoad = false, $pStrict = true) {
		$file     = CopixSelectorFactory::create ($pClassId);
		$className = $file->fileName;
		$filePath = $file->getPath () .COPIX_CLASSES_DIR.$className.'.class.php' ;
		if($pForceLoad || !class_exists($className, false)) {
			if(!Copix::RequireOnce ($filePath) && $pStrict) {
				throw new CopixException(_i18n('copix:copix.error.class.couldNotLoadClass', $pClassId));
			}
		}
		if($pStrict && !class_exists ($className, true)) {
			throw new CopixException(_i18n('copix:copix.error.class.undefinedClass', $pClassId));
		}
		return $className;
	}
	
	/**
	 * Créer une instance de la c
	 *
	 * @param string $pClassName
	 * @param array $pArgs Paramètres optionnels
	 * @return object Une instance de la classe.
	 */
	static private function _createInstance ($pClassName, $pArgs = null) {
		if (!is_null ($pArgs) && !is_array ($pArgs)) {
			return new $pClassName ($pArgs);
		}elseif (is_null ($pArgs)){
			return new $pClassName ();
		}
		$reflectionObj = new ReflectionClass ($pClassName);
		return $reflectionObj->newInstanceArgs ($pArgs);
	}

	/**
    * Création d'un objet du type de la classe demandée, via son identifiant Copix
    * @param string $pClassId l'identifiant de la classe
    * @param array $pArgs Arguments
    */
	public static function create ($pClassId, $pArgs = null){
		return self::_createInstance(self::_loadClass($pClassId), $pArgs);
	}

	/**
    * Même chose que create, à la différence que l'on gère un singleton
    * @param string $pClassId l'identifiant Copix de l'élément à créer
    * @param string $pInstanceId l'identifiant de l'instance à récupérer
    */
	public static function getInstanceOf ($pClassId, $pInstanceId = 'default'){
		// Charge la classe et récupère son nom
		$className = self::_loadClass($pClassId);
		
		//check if exists in the cache (while getting the fullIdentifier in id)
		if (! isset (self::$_cacheInstance [$className][$pInstanceId])){
			return self::$_cacheInstance[$className][$pInstanceId] = self::_createInstance($className);
		}

		return self::$_cacheInstance[$className][$pInstanceId];
	}
	
	/**
    * Inclusion des classes du module
    * @param string $pDirID l'identifiant du repertoire
    * @return array Tableau des instances chargées
    */
	static private function _dirInclude ($pDirId){
		$arReturn = array ();
		$dir     = CopixSelectorFactory::create ($pDirId);
		$directoy = dir($dir->getPath() .COPIX_CLASSES_DIR);
		while ($class = $directoy->read ()){
			if (strpos($class, '.class.php') === false){
				//on ne garde que les fichiers de classe
				continue;
			}
			try {
				$arReturn[] = self::fileInclude ($pDirId.substr($class, 0, strpos($class, '.class.php')));
			}catch (Exception $e){}
		}
		return $arReturn;
	}
	
	/**
    * Inclusion du fichier de la classe.
    * @param string $pClassID l'identifiant de la classe dont on veut inclure le fichier de définition
    * @return string Nom de la classe.
    */
	public static function fileInclude ($pClassId) {
		$file     = CopixSelectorFactory::create ($pClassId);
		if ($file->fileName == ''){
			return self::_dirInclude($pClassId);
		}		
		return self::_loadClass($pClassId, true, false);
	}
}
?>