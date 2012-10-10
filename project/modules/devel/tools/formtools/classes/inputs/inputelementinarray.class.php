<?php

_classInclude ('formtools|inputs/inputElementText');

class InputElementInArray extends InputElementText {

	private $_arrayToCheck;
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $pIdElement
	 * @param unknown_type $pFieldName
	 */
	public function __construct ($pIdElement, $pFieldName){
		parent::__construct($pIdElement, $pFieldName);
		$this->kind .= 'inarray';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see trunk/project/modules/devel/tools/formtools/classes/iInputElement#getValidator()
	 */
	public function getValidator (){
		return _validator ('inArray', array ('values'=> $this->_arrayToCheck));
	}
	
	public function setValues ($pValues) {
		$this->_arrayToCheck = $pValues;
	}
	
}