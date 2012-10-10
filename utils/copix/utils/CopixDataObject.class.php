<?php
/**
 * @package		copix
 * @subpackage	utils
 * @author		Guillaume Perréal
 * @copyright	2001-2008 CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Object destiné à contenir des données dans ses propriétés publiques.
 *
 * Particularités :
 *  - seules les propriétés publiqués sont sérialisées,
 *  - le constructeur par défaut accepte un tableau des propriétés initiales.
 */
class CopixDataObject {
	
	/**
	 * Cache des listes des propriétés d'instance par classe. 
	 *
	 * @var array
	 */
	static private $_propertyCache = array();
	
	/**
	 * Récupère la liste des propriétés publiques d'instance d'une classe.
	 * 
	 * @param string $pClassName Nom de la classe.
	 * @return array
	 */
	private function _getProperties() {
		$className = get_class($this);
		if(isset(self::$_propertyCache[$className])) {
			return self::$_propertyCache[$className];
		}
		
		self::$_propertyCache[$className] = array();
		$toReturn = &self::$_propertyCache[$className];
		
		$class = new ReflectionClass($className);
		foreach($class->getProperties() as $property) {
			if(!$property->isStatic() && $property->isPublic()) {
				$toReturn[] = $property->getName();
			}
		}
		
		return $toReturn;
	}
	
	/**
	 * Charge les propriétés publiques de l'instance à partir d'un tableau associatif (nom=>valeur).
	 * 
	 * Les entrées dont le nom ne correspond pas à une propriété sont ignorées.
	 * Les propriétés absents du tableau sont "unset".
	 *
	 * @param array $pValues Valeur à charger.
	 */
	public function loadFromArray($pValues) {
		foreach($this->_getProperties() as $name) {
			if(isset($pValues[$name])) {
				$this->$name = $pValues[$name]; 
			} elseif(isset($this->$name)) {
				unset($this->$name);
			}
		}
		return $this;
	}
	
	/**
	 * Ecrit les propriétés publiques de l'instance dans un tableau.
	 *
	 * @return array
	 */
	public function writeToArray(&$pArray) {
		foreach($this->_getProperties() as $name) {
			if(isset($this->$name)) {
				$pArray[$name] = $this->$name;
			} elseif(isset($pArray[$name])) {
				unset($pArray[$name]);
			}
		}
		return $this;
	}	
	
	/**
	 * Retourne un tableau des propriétés publiques de l'instance.
	 *
	 * @return array
	 */
	public function toArray() {
		$toReturn = array();
		$this->writeToArray($toReturn);
		return $toReturn;		
	}
	
	/**
	 * Constructeur par défaut. 
	 *
	 * @param mixed $pValues Tableaux de valeurs initiales ou null 
	 */
	public function __construct($pValues = null) {
		if(is_array($pValues)) {
			$this->loadFromArray($pValues);
		}
	}

	/**
	 * Méthode magique __sleep.
	 * 
	 * Renvoie la liste des propriétés à sérialiser.
	 *
	 * @return array Uniquement les propriétés magiques d'instance.
	 */
	public function __sleep() {
		return $this->_getProperties();
	}
	
}

?>