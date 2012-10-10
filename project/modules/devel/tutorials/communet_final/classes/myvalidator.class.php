<?php

class MyValidator extends CopixAbstractValidator {   
 
	protected function _validate ($pValue){
		$validator = _ctValidator ();
		
		return $validator->check ($pValue);
    }
}