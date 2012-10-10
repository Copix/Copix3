<?php
/**
 * @package cms3
 * @subpackage form
 * @author Nicolas Bastien
 */

/**
 * DAO Cms_form_element_values 
 * (liste les valeurs possibles pour les champs de type select, checkbox, radio)
 * 
 * @package cms3
 * @subpackage form
 * @author Nicolas Bastien
 */
class DAOCms_form_element_values extends CompiledDAOCms_form_element_values {
	
	/**
	 * Renvoit la liste des valeurs possibles pour un élément
	 * @param $cfev_id
	 * @return unknown_type
	 */
	public function findByElement($cfev_id) {
		$sp = _daoSp ()->addCondition ('cfev_id_element', '=', $cfev_id)
					->addCondition ('cfev_deleted_at', '=', null);
		
		return DAOcms_form_element_values::instance ()->findBy($sp);
	}
	
	
	/**
	 * (soft delele) On ne supprime pas les valeurs pour garder des statistiques cohérentes 
	 * @param $record
	 * @return int
	 */
	public function delete ($record) {
		$record->cfev_deleted_at = date ('YmdHis');
		return $this->update($record);
	}
	
}