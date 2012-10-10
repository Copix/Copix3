<?php
class ValidatorMod extends CopixAbstractValidator {
	public function __construct ($pParams = array (), $pMessage = null){
		if (!is_array ($pParams)){
			$pParams = array ('mod'=>$pParams);
		}
		parent::__construct ($pParams, $pMessage);
	}

	protected function _validate ($pValue){
		$mod = $this->requireParam ('mod');
		$this->validateParams ();
		return ($pValue % $mod) === 0;
	} 
}

?>