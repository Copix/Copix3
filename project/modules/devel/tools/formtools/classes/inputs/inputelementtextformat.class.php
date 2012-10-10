<?php

_classInclude ('formtools|inputs/inputElementText');

class InputElementTextFormat extends InputElementText {

	private $_format_validator = '';
	
	/**
	 * 
	 *
	 */
	public function __construct ($pIdElement, $pFieldName){
		parent::__construct($pIdElement, $pFieldName);
		$this->kind .= 'format:';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see trunk/project/modules/devel/tools/formtools/classes/iInputElement#getValidator()
	 */
	public function getValidator (){
		return _validator ($this->_format_validator);
	}
	
	/**
	 * Fonction permettant d'ajotuer un validateur
	 *
	 * @param string $pLibValidator
	 * @return objet courant
	 */
	public function setFormat ($pLibValidator){
		$this->_format_validator = $pLibValidator;
		$this->kind .= $pLibValidator;
		return $this;
	}
	
}