<?php
/**
 * @package cms
 * @subpackage form
 * @author Nicolas Bastien
 */

/**
 * DAO de la table cms_form
 * @package cms
 * @subpackage form
 * @author Nicolas Bastien
 */
class DAOCms_form extends CompiledDAOCms_form {
	
	/**
	 * Renvoit le contenu d'un formulaire du CMS (la liste des champs qui le compose)
	 * @param $pIdForm
	 * @return array
	 */
	public function getContent($pIdForm) {
		//En attendant les jointures dans les DAOs	
		//$formContent = DAOcms_form_content::instance ()->findBy(_daoSp ()->addCondition ('cfc_id_form', '=', $pIdForm));
	
		$query = <<<QUERY
SELECT cms_form_content.*, cms_form_element.*
FROM cms_form_content
INNER JOIN cms_form_element on (cms_form_content.cfc_id_element = cms_form_element.cfe_id)
WHERE cfc_id_form = :idForm
AND cms_form_element.cfe_deleted_at IS NULL
ORDER BY cms_form_content.cfc_order
QUERY;
		$formContent = _doQuery($query, array(':idForm' => $pIdForm));
		
		$toReturn = array();
		foreach ($formContent as $cmsFormContent) {
			$record = DAORecordcms_form_content::create ();
			$record->initFromDBObject($cmsFormContent);
			$record->cfe_label = $cmsFormContent->cfe_label;
			$record->cfe_type = $cmsFormContent->cfe_type;
			$record->cfe_orientation = $cmsFormContent->cfe_orientation;
			$record->cfe_columns = $cmsFormContent->cfe_columns;
			$record->cfe_default = $cmsFormContent->cfe_default;
			$record->cfe_default_data = $cmsFormContent->cfe_default_data;
            $record->cfe_aide = $cmsFormContent->cfe_aide;
			$toReturn[] = $record;
		}
		return $toReturn;
	}
	
	/**
	 * Renvoit la liste des formulaires disponibles
	 * @return array
	 */
	public function getList() {
		$query = <<<QUERY
SELECT cf_id, caption_hei
FROM cms_form
INNER JOIN cms_headingelementinformations on (cms_form.public_id_hei = cms_headingelementinformations.public_id_hei)
QUERY;
		//Les formulaire supprimer ne seront pas ramener (jointure sur headingelementinformation)
		$arResult = _doQuery($query);
		
		$toReturn = array();
		foreach($arResult as $cmsform) {
			$toReturn[$cmsform->cf_id] = $cmsform->caption_hei;
		}
		return $toReturn;
	}
	
	/**
	 * (soft delele) d'un formulaire
	 * @param $record
	 * @return int
	 */
	public function delete ($record) {
		$record->cf_deleted_at = date ('YmdHis');
		return $this->update($record);
	}
	
	/**
	 * Récupération des éléments composant le formulaire
	 * (utilisé à la création de page pour l'affichage partiel de formulaire)
	 * @param $pIdForm
	 * @return array
	 */
	public function getContentByIdElement($pIdForm) {
		$query = <<<QUERY
SELECT cms_form_content.cfc_id_element
FROM cms_form_content
INNER JOIN cms_form_element on (cms_form_content.cfc_id_element = cms_form_element.cfe_id)
WHERE cfc_id_form = :idForm
AND cms_form_element.cfe_deleted_at IS NULL
QUERY;

		$arResult = _doQuery($query, array(':idForm' => $pIdForm));
		
		$toReturn = array();
		foreach($arResult as $content) {
			$toReturn[] = $content->cfc_id_element;
		}
		return $toReturn;
		
	}
	
}
