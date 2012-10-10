<?php
/**
 * @package cms3
 * @subpackage form
 * @author Nicolas Bastien
 */

/**
 * Paramétrage dyu formulaire (liste le contenu)
 * @package cms3
 * @subpackage form
 * @author Nicolas Bastien
 */
class ZoneFormParametrage extends CopixZone {
	
	public function _createContent (&$toReturn){
		$tpl = new CopixTpl ();
		
		$tpl->assign('arFormContent', $this->_getFormContent());
		$toReturn = $tpl->fetch ('form.parametrage.tpl');		
		return true;
	}
	
	/**
	 * Renvoit la liste des champs contenu dans le formulaires courant
	 * @return array
	 */
	private function _getFormContent() {
		$formService = new Form_Service();
		$formService->updateContentOrder();
		$formContent = $formService->getCurrentForm()->content;
		
		$formConfig = new Form_Config();
		$fields = $formConfig->getFields();
		
		$toReturn = array();
		foreach ($formContent as $cmsFormContent) {
			$cmsFormContent->cfe_type_label = $fields[$cmsFormContent->cfe_type];
			$toReturn[] = $cmsFormContent;
		}
		return $toReturn;
	}
	
}
