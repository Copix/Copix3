<?php

_classInclude ('formtools|abstractInputElement');

class InputElementText extends abstractInputElement {

	/**
	 * Tableau des valeurs à insérer
	 * 
	 * @var array
	 */
	private $_arValues ;
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $pIdElement
	 * @param unknown_type $pFieldName
	 * @return unknown
	 */
	public function __construct ($pIdElement, $pFieldName){
		parent::__construct ($pIdElement, $pFieldName);
		$this->kind = 'select'; 
	}
	
	/**
 	 * (non-PHPdoc)
 	 * @see trunk/project/modules/devel/tools/formtools/classes/iInputElement#getForm()
 	 */
	public function getForm ($pDefaultValues = null, $class = null){
		/**
		 * @todo Ajouter les éléments javascript 
		 */
		if ($pDefaultValues == null || !isset ($pDefaultValues[$this->idElement])) {
			$pDefaultValues = array ();
			$pDefaultValues[$this->idElement] = $this->fieldName;
			
		}
		$this->defaultValues = $pDefaultValues; 
		if ($class != null) {
			$class = 'class="'.$class.'"';
		}
		return $this->fieldName.' <input '.$class.' id="'.$this->idElement.'" type="text" name="'.$this->idElement.'" value="'.$this->defaultValues[$this->idElement].'" />';
	}
	


	/**
	 * (non-PHPdoc)
	 * @see trunk/project/modules/devel/tools/formtools/classes/iInputElement#getDisplay()
	 */
	public function getDisplay ($pValue){
		return $this->fieldName. ': '.$pValue;
	}

	/**
	 * (non-PHPdoc)
	 * @see trunk/project/modules/devel/tools/formtools/classes/iInputElement#getValidator()
	 */
	public function getValidator (){
		return _validator ('string');
	}
	
}