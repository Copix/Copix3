<?php
/**
 * @package cms3
 * @subpackage form
 * @author Nicolas Bastien
 */

/**
 * Gestion des éléments disponible pour la création du formulaire
 * @package cms3
 * @subpackage form
 * @author Nicolas Bastien
 */
class ZoneFormFields extends CopixZone {
	
	/**
	 * (non-PHPdoc)
	 * @see core/CopixZone#_createContent()
	 */
	public function _createContent (&$toReturn){
		$tpl = new CopixTpl ();
		//Récupération des champs disponibles
		$arFormElement = iterator_to_array(DAOcms_form_element::instance ()->getAll());
		$tpl->assign('arFormElement', $arFormElement);
		
		//Récupération des champs ajoutés au formulaire courant
		$formService = new Form_Service();
		$arFormElementSelected = $this->_getFormFieldsIds();
		
		$tpl->assign('arFormElementSelected', $arFormElementSelected);
		
		$toReturn = $tpl->fetch ('form.fields.tpl');		
		return true;
	}
	
	/**
	 * Renvoit la liste des identifiants de champs du formulaires courant
	 * @return array
	 */
	private function _getFormFieldsIds() {
		$formService = new Form_Service();
		$formContent = $formService->getCurrentForm()->content;
		
		$toReturn = array();
		foreach ($formContent as $cmsFormContent) {
			$toReturn[] = $cmsFormContent->cfc_id_element;
		}
		return $toReturn;
	}
	
	
}