<?php
interface ICopixFieldForm {
	
	public function addValidator ($pValidator, $pParams = array (), $pMessage = null);
	
	public function valid ();
	
	public function fillRecord ($pRecord, $pField, $pValue);
	
	public function fillFromRecord ($pRecord);
}
?>