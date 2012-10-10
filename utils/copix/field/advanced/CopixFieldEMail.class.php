<?php
/**
 * @package		copix
 * @subpackage	field
 * @author		Nicolas Bastien
 */

/**
 * Champs de type EMail
 * Correspond à un varchar avec un validateur
 * 
 * @package		copix
 * @subpackage	field
 * @author		Nicolas Bastien
 */
class CopixFieldEMail extends CopixFieldVarchar {
	
	public function __construct ($pType, $pParams = array ()) {
		parent::__construct($pType, $pParams);
		//Ajout du validateur
		$this->attach(_validator('email', $pParams));
	}
	
}