<?php
class ValidatorMod2 extends CopixAbstractValidator {
	protected function _validate ($pValue){
		return ($pValue % 2)  === 0;		
	}
}