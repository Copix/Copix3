<?php
class CopixFieldSelect extends CopixAbstractField implements ICopixField  {
	
	public function getHTML($pName, $pValue, $pMode = 'edit') {
		if ($pMode == 'edit') {
			return _tag ('select', array_merge ($this->getParams(), array ('name'=>$pName, 'selected'=>$pValue)));;
		} else {
		    $values = $this->getParam('values');
		    if ($this->getParam('objectMap') != null) {
		        list ($id,$caption) = explode(';',$this->getParam('objectMap'));
		        foreach ($values as $value) {
		            if ($value->$id == $pValue) {
		                return $value->$caption;
		            }
		        }
		        return $pValue;
		    } else {
			    return isset ($values[$pValue]) ? $values[$pValue] : $pValue;
		    }
		}
	}
	
	public function addCondition ($pDatasource, $pField, $pValue) {
		if ($pValue != null) {
				$pDatasource->addCondition ($pField, '=', $pValue);
		}
	}
	
}