<?php
/**
 * @package		copix
 * @subpackage	validator
 * @author		Gérald Croës, Salleyron Julien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Validateur de types complexes. 
 * 
 * Ces validateurs permettent de définir une structure de données attendue et d'y attacher des validateurs
 * @package		copix
 * @subpackage	validator
 */
class CopixComplexTypeValidator extends CopixAbstractValidator implements ICopixComplexTypeValidator {
	/**
	 * Liste des validateurs associés au type complexe
	 *
	 * @var array
	 */
	private $_validators = array ();
	
	/**
	 * Liste des propriétés qui doivent être définies
	 *
	 * @var array
	 */
	private $_mustBeSet = array ();

	/**
	 * Tableau des libellés des chemins 
	 *
	 * @var array
	 */
	private $_captions = array ();

	/**
	 * Construction
	 *
	 * @param string $pMessage le message d'erreur que l'on souhaite afficher en cas de problème
	 */
	public function __construct ($pMessage = null){
		parent::__construct (array (), $pMessage);
	}

	/**
	 * Vérifie que les données dont le nom est passée sont définies
	 *
	 * @param mixed $pPropertyName	le nom de la propriété (ou un tableau de nom de propriétés)
	 * @return ICopixComplexTypeValidator
	 */
	public function required ($pPropertyName){
		if (is_array ($pPropertyName)){
			foreach ($pPropertyName as $propertyName){
				$this->required ($propertyName);
			}
		}else{
			$this->_mustBeSet[] = $pPropertyName;
		}
		return $this;
	}

	/**
	 * Attache un validateur à un chemin de propriété
	 *  
	 * @param ICopixValidator $pValidator	Le validateur à rajouter à la propriété
	 * @param string $pPropertyPath			Le chemin de la propriété a tester (séparée par des -, que ce soit un tableau ou un objet)	
	 * @return ICopixComplexTypeValidator
	 */
	public function attachTo (ICopixValidator $pValidator, $pPropertyPath){
		if (is_array ($pPropertyPath)){
			foreach ($pPropertyPath as $propertyName){
				$this->attachTo ($pValidator, $propertyName); 
			}
		}else{
			if (! isset ($this->_validators[$pPropertyPath])){
				$this->_validators[$pPropertyPath] = array ();			
			}
			$this->_validators[$pPropertyPath][] = $pValidator; 
		}
		return $this; 
	}

	/**
	 * Récupération du message d'erreur en cas de problème de validation
	 *
	 * @param mixed $pValue la valeur testée lors du message d'erreur 
	 * @param mixed $pErrors l'erreur générée lors du check qui a échoué 
	 * @return mixed 
	 */
	protected function _getMessage ($pValue, $pErrors){
		if ($this->_message === null){
			return $pErrors;
		}
		return $this->_message; 
	}

	/**
	 * Application de tous les contrôles définis sur la propriété
	 *
	 * @param string $pPropertyName le chemin de la propriété
	 * @param mixed  $pValue la valeur de la propriété
	 * @return mixed (true si ok, CopixErrorObject si non)
	 */
	private function _checkProperty ($pPropertyName, $pValue){
		$toReturn = new CopixErrorObject ();
		
		$propertyValue = $this->_getPropertyValue ($pPropertyName, $pValue);

		if (in_array ($pPropertyName, $this->_mustBeSet)){
			if (! $this->_checkSet ($pPropertyName, $pValue)){
				$toReturn->addErrors (_i18n ('copix:copixvalidator.complextype.mustBeSet'));
				return $toReturn; 
			}
		}

		if (isset ($this->_validators[$pPropertyName])){
			foreach ($this->_validators[$pPropertyName] as $validator){
				if (($result = $validator->check ($this->_getPropertyValue ($pPropertyName, $pValue))) !== true){
					$toReturn->addErrors ($result);
				}
			}
		}

		return $toReturn->isError () ? $toReturn : true;
	}
	
	/**
	 * Retourne la liste des propriétés à vérifier (qui disposent d'un validateur ou 
	 *  qui disposent d'un contrôle "isset") 
	 * @return array
	 */
	private function _propertiesToCheck (){
		return array_merge (array_keys ($this->_validators), $this->_mustBeSet);
	}

	/**
	 * Lancement des contrôles sur les propriétés, lancement d'une exception en cas
	 * d'échec de vérification
	 * @param   mixed   $pValue La valeur à contrôller
	 * @throws CopixValidatorException
	 */
	public function assert ($pValue){
		foreach ($this->_propertiesToCheck () as $propertyName){
			if (($result = $this->_checkProperty ($propertyName, $pValue)) !== true){
				$toReturn = new CopixErrorObject ();
				$toReturn->addErrors (array ($propertyName=>$result));
				throw new CopixValidatorException ($toReturn);
			}
		}
		return true;
	}

	/**
	 * On passe par toutes les méthodes de validation intermédiaire
	 * @param mixed $pValue La propriété à valider
	 * @return boolean / CopixErrorObject
	 */
	protected function _validate ($pValue){
		$toReturn = new CopixErrorObject ();
		foreach ($this->_propertiesToCheck () as $propertyName){
			if (($result = $this->_checkProperty ($propertyName, $pValue)) !== true){
				$toReturn->addErrors (array ($propertyName=>$result));
			}
		}
		return $toReturn->isError () ? new CopixErrorObject ($this->_getMessage ($pValue, $toReturn)) : true;
	}	
	
	/**
	 * Vérifie si la propriété $pPropertyName est bien définie
	 * @param string $pPropertyName Le nom de la propriété à vérfier
	 * @param mixed  $pValue La valeur qui contient la propriété donnée
	 * @return boolean (true : ok, false : pas ok)
	 */
	public function _checkSet ($pPropertyName, $pValue){
		return $this->_getPropertyValue ($pPropertyName, $pValue) !== null;
	}
	
	/**
	 * Récupération de la valeur d'une propriété
	 * @param string $pPropertyName le nom de la propriété dont on souhaite récupérer la valeur
	 * @param mixed  $pValue la valeur qui contient la propriété a vérifier
	 * @return mixed la valeur de la propriété
	 */
	public function & _getPropertyValue ($pPropertyName, $pValue){
		$copyValue = $pValue;
		foreach (explode ('-', $pPropertyName) as $name){
			if (is_object ($copyValue)){
				if (isset ($copyValue->$name)){
					$copyValue = $copyValue->$name;
				}else{
					$null = null;
					return $null;
				}
			}elseif (is_array ($copyValue)){
				if (isset ($copyValue[$name])){
					$copyValue = $copyValue[$name];					
				}else{
					$null = null;
					return $null;
				}
			}
		}
		return $copyValue;
	}

}

/**
 * Alias à CopixComplexTypeValidator
 * @package copix
 * @subpackage validator
 */
class CopixArrayValidator extends CopixComplexTypeValidator {}

/**
 * Alias à CopixComplexTypeValidator
 * @package copix
 * @subpackage validator
 */
class CopixObjectValidator extends CopixComplexTypeValidator {}