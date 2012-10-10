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

class CopixFieldPassword extends CopixAbstractField implements ICopixField  {
	
	public function getHTML ($pName, $pValue, $pMode = 'edit') {
		if ($pMode == 'edit') {
			return $this->getHTMLFieldEdit($pName, $pValue);
		} else {
			return $this->getHTMLFieldView($pName, $pValue);
		}
	}
	
	public function getHTMLFieldEdit ($pName, $pValue) {
		return '<input type="password" name="'.$pName.'" value="'.$pValue.'" />';
	}
	
	public function getHTMLFieldView ($pName, $pValue) {
		return '';
	}
}