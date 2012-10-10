<?php
/**
 * @package    copix
 * @subpackage utils
 * @author     Guillaume Perréal, Gérald Croës
 * @copyright  CopixTeam
 * @link       http://copix.org
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe de base pour gérer des paramètres.
 * 
 * @package copix
 * @subpackage utils
 */
class CopixParameterHandler implements ArrayAccess, Iterator, Countable {

	/**
	 * Tableau de l'ensemble des paramètres
	 *
	 * @var array
	 */
	protected $_params = array ();
	
	/**
	 * Tableau des clés récupèré 
	 *
	 * @var array
	 */
	protected $_takenKey = array ();
	
	/**
	 * Tableau des extraParams
	 *
	 * @var array
	 */
	protected $_extraParams = false;

	/**
	 * Définit les paramètres.
	 *
	 * @param array $pParams Nouveaux paramètres.
	 */
	public function setParams ($pParams) {
		$this->_params = $pParams;
	}

	/**
	 * Assignation d'un nouveau paramètre
	 *
	 * @param string $pName Nom du paramètre
	 * @param mixed $pValue Valeur du paramètre
	 */
	public function setParam ($pName, $pValue){
		$this->_params[$pName] = $pValue;
	}

	/**
	 * Retourne l'ensemble des paramètres.
	 *
	 * @return array
	 */
	public function getParams () {
		return $this->_params;
	}
	
	//ArrayAccess
	/**
	 * Alias a getParam en accès tableau 
	 */
	public function offsetGet ($pOffset){
		return $this->getParam ($pOffset);
	}
	
	/**
	 * Alias a setParam en accès tableau
	 */
	public function offsetSet ($pOffset, $pValue){
		return $this->setParam ($pOffset, $pValue);
	}
	
	/**
	 * Indique si le paramètre est déclaré.
	 * @param string $pName le nom du paramètre
	 */
	public function offsetExists ($pName){
		return array_key_exists ($pName, $this->_params);
	}
	
	/**
	 * 
	 *
	 * @param unknown_type $pName
	 */
	public function offsetUnset ($pName){
		if ($this->offsetExists ($pName)){
			unset ($this->_params[$pName]);
		}
	}
	
	//Iterator
	private $_iterator;
	public function rewind (){
		$this->_iterator = new ArrayIterator ($this->_params);
	}
	public function valid (){
		return $this->_iterator->valid ();
	}
	public function current (){
		return $this->_iterator->current ();		
	}
	public function key (){
		return $this->_iterator->key ();		
	}
	public function next (){
		return $this->_iterator->next ();		
	}

	//Countable
	public function count (){
		return count ($this->_params);
	}

	/**
	 * Récupère un paramètre optionnel.
	 *
	 * Si $pName est un tableau, getParam() agit de façon récursive. Elle retourne un tableau
	 * de valeurs, une pour chaque entrées de $pName ; la clef étant le nom du paramètre.
	 * Il est alors possible de fournir un tableau de valeurs par défaut, qui
	 * seront utilisées dans la même ordre que $pName. Si $pDefault n'est pas un tableau, il 
	 * est utilisé comme valeur par défaut de tous les paramètres. De la même façon, $pType
	 * peut être un tableau de type.
	 * 
	 * Attention, si $pName est un tableau, il ne peut contenir qu'une seule dimension sans quoi 
	 *  une erreur surviendra.
	 * 
	 * @param mixed   $pName    Nom du paramètre, ou tableau de noms de paramètres.
	 * @param mixed   $pDefault Valeur par défaut, ou tableau de valeurs par défaut.
	 * @param mixed   $pType    Type de la valeur, ou tableau de types par défaut.
	 * @param boolean $pDefaultIfNotValidate Si vrai, retourne la valeur par défaut si la validation ne passe pas au lieu de lancer une exception
	 * 
	 * @return mixed La valeur du paramètre ou un tableau des valeurs.
	 */
	public function getParam ($pName, $pDefault = null, $pType = null, $pDefaultIfNotValidate = false) {
		if (is_array ($pName)){
			$toReturn = array ();
			foreach ($pName as $name){
				$toReturn[$name] = $this->getParam ($name, 
				                                    isset ($pDefault[$name]) ? $pDefault[$name] : null, 
				                                    is_string ($pType) ? $pType : (isset ($pType[$name]) ? $pType[$name] : null));
			}
			return $toReturn;
		}else{
			//On regarde si le paramètre existe (pour éviter de créer un validateur inutilement)
			if (!array_key_exists ($pName, $this->_params)){
				return $pDefault;
			}
	
			//On regarde le type de la variable pour déterminer le validateur à utiliser
			if ($pType !== null){
				if (is_string ($pType)){
					$validator = _validator ($pType);
				}elseif ($pType instanceof ICopixValidator){
					$validator = $pType;
				}else{
					throw new CopixException ('Le troisième paramètre a getParam doit représenter soit un identifiant de validateur, soit un validateur');
				}
	
				//on regarde si le paramètre est d'un type correct
				if ($validator->check ($this->_params[$pName]) !== true){
					if ($pDefaultIfNotValidate){
						return $pDefault;
					}
					throw new CopixParameterHandlerValidationException ($pName, $pType);
				}
			}
			
			//Remplissage du takenKey pour les extras params
			if (!in_array($pName, $this->_takenKey)) {
			    $this->_takenKey[] = $pName;
			}
			return $this->_params[$pName];
		}
	}

	/**
	 * Récupère un paramètre obligatoire.
	 *
	 * Enregistre une erreur "missing" si le paramètre n'est pas défini.
	 *
	 * @param mixed pName Nom du paramètre, ou un tableau de noms de paramètres.
	 * @param mixed $pType Type de la valeur, ou un tableau des types de paramètres.
	 * @return mixed La valeur du paramètre ou null s'il n'est pas présent.
	 */
	public function requireParam ($pName, $pType = null) {
		if (is_array ($pName)){
			foreach ($pName as $name){
				if (! array_key_exists ($name, $this->_params)){
					throw new CopixParameterHandlerMissingException ($name);			
				}				
			}
		}else{
			if (! array_key_exists ($pName, $this->_params)){
				throw new CopixParameterHandlerMissingException ($pName);			
			}
		}

		return $this->getParam ($pName, $pType);
	}
	
	/**
	 * Vérifie que les paramètres listés sont bien présents dans l'objet
	 * @param mixed $all liste des paramètres dont la présence est a vérifier 
	 */
	public function assertParams (){
		$missingKeys = array ();
		$keys = array_keys ($this->_params);
		foreach (func_get_args () as $varName) {
			if (!in_array ($varName, $keys)) {
				$missingKeys[] = $varName;
			}
		}
		if (count ($missingKeys)) {
			throw new CopixParameterHandlerMissingException ($missingKeys);
		}
	}
	
	/**
	 * Retourne l'ensemble des paramètres non récupèré.
	 *
	 * @return array
	 */
	public function getExtraParams () {
	    if ($this->_extraParams === false) {
	        $this->_extraParams = $this->_params;
	    }
	    
	    foreach ($this->_takenKey as $key) {
	        if (key_exists($key, $this->_extraParams)) {
	            unset ($this->_extraParams[$key]);
	        }
	    }
	    
	    return $this->_extraParams;
	}
}