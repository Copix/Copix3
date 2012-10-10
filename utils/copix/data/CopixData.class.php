<?php
/**
 * @package copix
 * @subpackage core
 * @author Croes Gérald
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe de transport d'informations (utiliser pour les actions, le transfère de paramètres, etc...)
 *
 * @package copix
 * @subpackage core
 */
class CopixData implements ICopixData {
	/**
	 * Les données du tableau
	 *
	 * @var unknown_type
	 */
	private $_data = array ();

	/**
	 * Retourne l'élément où sauvegarder la donnée
	 *
	 * @param string $propertyName Nom de la propriété à récupérer
	 * @return mixed
	 */
	public function & __get ($pName) {
		return $this->get ($pName);
	}

	/**
	 * Assignation d'une valeur a l'indice _data
	 *
	 * @param unknown_type $pName
	 * @param unknown_type $pValue
	 */
	public function __set ($pName, $pValue){
		$this->set ($pName, $pValue);
	}

	/**
	 * Surcharge de isset pour que les sous éléments "CopixData" soient correctement détectés comme vide lorsqu'il y a lieu
	 *
	 * @param string $propertyName Nom de la proriété
	 * @return boolean
	 */
	public function __isset ($pName) {
		if (isset ($this->_data[$pName]) && $this->_data[$pName] instanceof CopixData) {
			return !empty ($this->_data[$pName]);
		}
		return isset ($this->_data[$pName]);
	}

	/**
	 * Constructeur
	 *
	 * @param array $pArInit Tableau de variables pour initialiser le ppo
	 */
	public function __construct ($pArInit = array ()) {
		//FIXME Je ne savais pas dans quelle classe mettre cette fonction,
		// alors je l'ai copié / collé, extraite de smarty_plugin_modifier_toarray
		if (is_string ($pArInit)){
			$exploded = explode (';', $pArInit);
			$array = array ();

			foreach ($exploded as $item){
				$item = explode ('=>', $item);
				if (count ($item) == 2){
					$array[$item[0]] = $item[1];
				}else{
					$array[] = $item[0];
				}
			}
			$pArInit = $array;
		}

		if (is_array ($pArInit) || (is_object ($pArInit) && $pArInit instanceof Iterator)) {
			foreach ($pArInit as $key => $item) {
				$this->$key = $item;
			}
		} elseif (is_object ($pArInit)) {
			foreach ($this->_getElementVars ($pArInit) as $key => $item){
				$this->$key = $item;
			}
		}
	}

	/**
	 * Implémentation de ArrayAccess, pour la récupération de $pOffset
	 *
	 * @param string $pOffset Offset à lire
	 * @return mixed
	 */
	public function offsetGet ($pOffset) {
		return $this->get ($pOffset);
	}

	/**
	 * Implémentation de ArrayAccess, pour la définition de $pOffset
	 *
	 * @param string $pOffset Offset à écrire
	 * @param mixed $pValue
	 */
	public function offsetSet ($pOffset, $pValue) {
		if ($pOffset === null) {
			$this->_data[] = $pValue;
		}else{
			$this->_data[$pOffset] = $pValue;
		}
	}

	/**
	 * Implémentation de ArrayAccess, vérifie l'existance de $pOffset
	 *
	 * @param string $pOffset Offet à vérifier
	 * @return boolean
	 */
	public function offsetExists ($pOffset) {
		return $this->exists ($pOffset);
	}

	/**
	 * Implémentation de ArrayAccess, supprime $pOffset
	 *
	 * @param string $pOffset Offset à supprimer
	 */
	public function offsetUnset ($pOffset) {
		$this->$pOffset = null;
	}

	/**
	 * En cas de demande d'affichage directe
	 *
	 * @return string
	 */
	public function __toString () {
		return '';
	}

	/**
	 * Applique toutes les propriétés de l'objet PPO dans les propriétés de l'objet cible
	 *
	 * @param object $pDest Objet de destination
	 * @param boolean $pCreateNew Indique si on veut créer un nouvel objet
	 */
	public function saveIn (&$pDest, $pCreateNew = true) {
		//détermine le "type" de l'objet
		if (!($array = is_array ($pDest))) {
			if (!($object = is_object ($pDest))) {
				$natural = true;
			}
		}
		$elementVars = array ();
		if ($array || $object) {
			$elementVars = array_keys ($this->_getElementVars ($pDest));
		}

		//on parcours chacune des propriétés de l'élément
		foreach ($this->_getElementVars ($this) as $name => $element) {
			//on regarde si la propriété existe dans la destination
			if (($inArray = in_array ($name, $elementVars)) || $pCreateNew) {
				if ($inArray && (is_object ($element) || is_array ($element))) {
					//la propriété existait déja et c'est un tableau ou un objet,
					//on reparcours le tout pour y appliquer les changements
					if ($array) {
						_ppo ($element)->saveIn ($pDest[$name], $pCreateNew);
					} else {
						_ppo ($element)->saveIn ($pDest->$name, $pCreateNew);
					}
					// NOTE : il n'est pas possible d'avoir recours a l'opérateur
					// ternaire pour les passages par référence
				} else {
					// la propriété n'existait pas => il faut la créer a l'identique
					// ou la propriété existait sous sa forme naturelle et il faut la remplacer
					if ($array) {
						$pDest[$name] = $element;
					} else {
						$pDest->$name = $element;
					}
				}
			}
		}

		return $this;
	}

	/**
	 * Récupération des clefs/valeur d'un tableau ou des propriétés/valeurs d'un objet
	 *
	 * @param array/object $pElement Elément dont on souhaite connaitre les clefs / valeurs
	 * @return array
	 */
	protected function _getElementVars ($pElement) {
		//tableau ?
		if (is_array ($pElement)){
			return $pElement;
		}

		//Objet qui implémente Iterator ?
		if (is_object ($pElement) && $pElement instanceof Iterator){
			$toReturn = array ();
			foreach ($pElement as $key=>$value){
				$toReturn[$key] = $value;
			}
			return $toReturn;
		}

		//sinon on tente une dernière solution
		return get_object_vars ($pElement);
	}

	private $_iterator;
	public function rewind (){
		$this->_iterator = new ArrayIterator ($this->_data);
		return $this->_iterator->rewind ();
	}
	public function key (){
		return $this->_iterator->key ();
	}
	public function current (){
		return $this->_iterator->current ();
	}
	public function valid (){
		return $this->_iterator->valid ();
	}
	public function next (){
		return $this->_iterator->next ();
	}

	/**
	 * Récupération d'une donnée
	 *
	 * @param string $pName le nom de la donné a récupérer
	 * @return mixed
	 */
	public function & get ($pName){
		if (!$this->exists ($pName)){
			$this->set ($pName, new CopixData ());
		}

		$value = $this->_data[$pName];
		if (isset ($this->_getters[$pName])){
			foreach ($this->_getters[$pName] as $pBehaviour){
				$value = $pBehaviour->get ($value);
			}
		}
		return $value;
	}

	/**
	 * Assignation d'une donnée
	 *
	 * @param string $pName le nom de la variable a assigner
	 * @param mixed $pValue la valeur a assigner
	 */
	public function set ($pName, $pValue){
		if (isset ($this->_setters[$pName])){
			foreach ($this->_setters[$pName] as $pBehaviour){
				$pValue = $pBehaviour->get ($pValue);
			}
		}

		$this->_data[$pName] = $pValue;
	}

	/**
	 * Indique si la variable existe
	 *
	 * @param unknown_type $pName
	 * @return unknown
	 */
	public function exists ($pName){
		return array_key_exists ($pName, $this->_data);
	}

	/**
	 * Indique le nombre de propriétés de l'objet
	 *
	 * @return int
	 */
	public function count (){
		return count ($this->_data);
	}

	/**
	 * Attache un comportement a la propriété donnée lors de l'écriture
	 *
	 * @param string              $pName      le nom de la propriété a qui on attache le comportement
	 * @param ICopixDataBehaviour $pBehaviour le comportement à attacher
	 */
	public function attachSetter ($pName, ICopixBehaviour $pBehaviour){
		if (!isset ($this->_setters[$pName])){
			$this->_setters[$pName] = array ();
		}
		$this->_setters[$pName][] = $pBehaviour;
		return $this;
	}

	/**
	 * Attache un comportement a la propriété donnée lors de la lecture
	 *
	 * @param string          $pName      le nom de la propriété a qui on attache le comportement
	 * @param ICopixBehaviour $pBehaviour le comportement à attacher a la propriété
	 */
	public function attachGetter ($pName, ICopixBehaviour $pBehaviour){
		if (!isset ($this->_getters[$pName])){
			$this->_getters[$pName] = array ();
		}
		$this->_getters[$pName][] = $pBehaviour;
		return $this;
	}
}