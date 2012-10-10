<?php
/**
 * 
 */

/**
*
*/
class CopixComplexTypeValidator extends CopixAbstractValidator implements ICopixComplexTypeValidator {
	/**
	 * Enter description here...
	 *
	 * @var unknown_type
	 */
	private $_validators = array ();
	
	/**
	 * Enter description here...
	 *
	 * @var unknown_type
	 */
	private $_mustBeSet = array ();

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

	protected function _getMessage ($pValue, $pErrors){
		if ($this->_message === null){
			return $pErrors;
		}
		return $this->_message; 
	}

	private function _checkProperty ($pPropertyName, $pValue){
		$toReturn = new CopixErrorObject ();
		
		$propertyValue = $this->_getPropertyValue ($pPropertyName, $pValue);

		if (in_array ($pPropertyName, $this->_mustBeSet)){
			if (! $this->_checkSet ($pPropertyName, $pValue)){
				$toReturn->addErrors (_i18n ('copix:copixvalidator.complextype.mustBeSet', $pPropertyName)); 
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
	
	private function _propertiesToCheck (){
		return array_merge (array_keys ($this->_validators), $this->_mustBeSet);
	}

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
	
	public function _checkSet ($pPropertyName, $pValue){
		return $this->_getPropertyValue ($pPropertyName, $pValue) !== null;
	}
	
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
 */
class CopixArrayValidator extends CopixComplexTypeValidator {}

/**
 * Alias à CopixComplexTypeValidator
 */
class CopixObjectValidator extends CopixComplexTypeValidator {}
?>