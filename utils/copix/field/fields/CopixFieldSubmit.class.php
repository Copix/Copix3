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

class CopixFieldSubmit extends CopixAbstractField implements ICopixField  {
	protected function _initContainer () {
		$this->_container->setLabel (false);
	}
	
	public function getHTML ($pName, $pValue, $pMode = 'edit') {
		if ($pMode != 'edit') {
			return '';
		}
		$pLibelle = $this->getParam('libelle', 'Valider');
		return '<input type="submit" value="'.$pLibelle.'" />';
	}
	
}