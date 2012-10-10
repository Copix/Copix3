<?php
/**
 * @package		copix
 * @subpackage	field
 * @author		Nicolas Bastien
 */

/**
 * @package		copix
 * @subpackage	field
 * @author		Nicolas Bastien
 */
class CopixFieldCheckbox extends CopixAbstractField implements ICopixField  {
	
	/**
	 * (non-PHPdoc)
	 * @see field/ICopixField#getHTML()
	 */
	public function getHTML($pName, $pValue, $pMode = 'edit') {
		return null;
	}
	
	/**
	 * Affichage du champ en édition
	 * @param $pName
	 * @param $pValue
	 * @return string
	 */
	public function getHTMLFieldEdit($pName, $pValue) {
		return _tag ('checkbox', array_merge ($this->getParams(), array ('name'=>$pName, 'selected'=>$pValue)));
	}
	
}