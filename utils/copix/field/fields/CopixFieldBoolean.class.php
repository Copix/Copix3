<?php
class CopixFieldBoolean extends CopixAbstractField implements ICopixField  {
	
	public function getHTML ($pName, $pValue, $pMode = 'edit') {
		return '<input type="checkbox" value="OUI" name="'.$pName.'" id="'.$pName.'" '.(($pValue === 'OUI') ? 'checked="checked"' : null ).' />';
	}
	
	public function fillFromRequest ($pName, $pDefault = null, $pValue = null) {
		return _request ($pName, 'NON');
	}
}
?>
