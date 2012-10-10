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

class CopixFieldHidden extends CopixAbstractField implements ICopixField  {
	protected function _initContainer () {
		$this->_container->setLabel (false);
	}
	
	public function getHTML ($pName, $pValue, $pMode = 'edit') {
		return null;
	}
	
	public function fillFromRequest ($pName, $pDefault = null, $pValue = null) {
		return isset ($pValue) ? $pValue : $pDefault;
	}
	
	public function addCondition ($pDatasource, $pField, $pValue) {
		if ($pValue != null) {
			$pDatasource->addCondition ($pField, 'like', $pValue);
		}
	}
	
	/**
	 * Affichage de l'élement
	 * @param $pName
	 * @param $pValue
	 * @return string code html
	 */
	public function getHTMLFieldEdit ($pName, $pValue) {
		return '<input type="hidden" name="'.$pName.'" value="'.$pValue.'" />';
	}
}