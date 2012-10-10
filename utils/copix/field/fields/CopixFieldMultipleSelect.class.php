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

class CopixFieldMultipleSelect extends CopixAbstractField implements ICopixField  {
	
	public function getHTML ($pName, $pValue, $pMode = 'edit') {
		if ($pMode == 'edit') {
			return $this->getHTMLFieldEdit($pName, $pValue);
		} else {
			return $this->getHTMLFieldView($pName, $pValue);
		}
	}
	
	public function getHTMLFieldEdit ($pName, $pValue) {
		return _tag ('multipleselect', array_merge ($this->getParams (), array ('name'=>$pName, 'selected'=>$pValue)));
	}
	
	public function getHTMLFieldView ($pName, $pValue) {
		return '';
	}
	
	public function addCondition ($pDatasource, $pField, $pValue) {
		if ($pValue != null) {
			$pDatasource->startGroup();
			foreach ($pValue as $value) {
				$pDatasource->addCondition ($pField, '=', $value, 'OR');
			}
			$pDatasource->endGroup();
		}
	}
	
}