<?php
/**
 * @package		copix
 * @subpackage	field
 * @author		Nicolas Bastien
 */

/**
 * Champs de type Code Postal
 * Correspond à un varchar avec un validateur
 * 
 * @package		copix
 * @subpackage	field
 * @author		Nicolas Bastien
 */
class CopixFieldCP extends CopixFieldVarchar {
	
	public function __construct ($pType, $pParams = array ()) {
		parent::__construct($pType, $pParams);
		//Ajout du validateur
		$this->attach(_validator ('string', array_merge($pParams, array('maxLength'=>5, 'minLength'=>5))));
        $this->attach(_validator ('numeric', $pParams));
	}
	
}