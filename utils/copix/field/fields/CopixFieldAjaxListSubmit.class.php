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

class CopixFieldAjaxListSubmit extends CopixAbstractField {
	
	public function getHTML ($pName, $pValue, $pMode = 'edit') {
		$html = '<input id="'.$pName.'" type="button" value="chercher" />';
		$listid = $this->getParam('currentList');
		$formid = CopixListFactory::get ($listid)->getFormId ();
		$url = _url ($this->getParam('action', 'generictools|copixlist|find'), array ('currentForm'=>$formid, 'currentList'=>$listid));
		CopixHTMLHeader::addJSDOMReadyCode("
		$('$pName').addEvent ('click', function () {
			list.get('$listid').find();
		});
		");
		return $html;
	}
	
	public function addCondition ($pDatasource, $pField, $pValue) {
		//
	}
	
}