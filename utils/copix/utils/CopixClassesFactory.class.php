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
    * Création d'un objet du type de la classe demandée, via son identifiant Copix
    * @param string $pClassId l'identifiant de la classe
    * @param array $pArgs Arguments
    */
	public static function create ($pClassId, $pArgs = null){
		//Récupération des éléments critiques.
		$file     = CopixSelectorFactory::create ($pClassId);
		$filePath = $file->getPath () .COPIX_CLASSES_DIR.strtolower ($file->fileName).'.class.php' ;

		Copix::RequireOnce ($filePath);
		$fileClass = $file->fileName;
		// depuis PHP 5.1.3, ReflectionClass existe, et permet l'équivalent de call_user_func_array avec un objet
		if (!is_null ($pArgs)) {
			$reflectionObj = new ReflectionClass ($fileClass); 
			return $reflectionObj->newInstanceArgs ($pArgs);
		} else {
			return new $fileClass ();
		}
	}

	/**
    * Même chose que create, à la différence que l'on gère un singleton
    * @param string $pClassId l'identifiant Copix de l'élément à créer
    * @param string $pInstanceId l'identifiant de l'instance à récupérer
    */
	public static function getInstanceOf ($pClassId, $pInstanceId = 'default'){
		//gets the fileSelctor
		$file = CopixSelectorFactory::create ($pClassId);

		//check if exists in the cache (while getting the fullIdentifier in id)
		if (! isset (self::$_cacheInstance [$pClassId = $file->getSelector ()][$pInstanceId])){
			self::$_cacheInstance[$pClassId][$pInstanceId] = self::create ($pClassId);
		}

		return self::$_cacheInstance[$pClassId][$pInstanceId];
	}

	/**
    * Inclusion du fichier de la classe.
    * @param string $pClassID l'identifiant de la classe dont on veut inclure le fichier de définition
    * @return boolean
    */
	public static function fileInclude ($pClassId){
		$file     = CopixSelectorFactory::create ($pClassId);
		$filePath = $file->getPath() .COPIX_CLASSES_DIR.strtolower ($file->fileName).'.class.php' ;
		return Copix::RequireOnce ($filePath);
	}
}
?>