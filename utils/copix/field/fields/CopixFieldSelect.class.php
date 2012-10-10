<?php
/**
 * @package		copix
 * @subpackage	forms
 * @author		Salleyron Julien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @experimental
 */

class CopixFieldSelect extends CopixAbstractField implements ICopixField  {
	
	public function getHTML($pName, $pValue, $pMode = 'edit') {
		if ($pMode = 'edit') {
			return _tag ('select', array_merge ($this->getParams(), array ('name'=>$pName, 'selected'=>$pValue)));;
		} else {
			return $pValue;
		}
	}
	
	public function addCondition ($pDatasource, $pField, $pValue) {
		if ($pValue != null) {
				$pDatasource->addCondition ($pField, '=', $pValue);
		}
	}
	
	public function getHTMLFieldEdit($pName, $pValue) {
		return _tag ('select', array_merge ($this->getParams(), array ('name'=>$pName, 'selected'=>$pValue)));
	}
	
}