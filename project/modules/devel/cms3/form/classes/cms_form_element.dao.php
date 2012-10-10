<?php
/**
 * @package cms3
 * @subpackage form
 * @author Nicolas Bastien
 */

/**
 * DAO Cms_form_element 
 * (liste les valeurs possibles pour les champs de type select, checkbox, radio)
 * 
 * @package cms3
 * @subpackage form
 * @author Nicolas Bastien
 */
class DAOCms_form_element extends CompiledDAOCms_form_element {
	
	/**
	 * Récupération des champs disponibles
	 * @return unknown_type
	 */
	public function getAll() {
		$sp = _daoSp ()->addCondition ('cfe_deleted_at', '=', null);
		return DAOcms_form_element::instance ()->findBy($sp);
	}
	
	/**
	 * Récupération d'élement avec la liste des valeurs qui lui sont associées
	 * @param $cfev_id
	 * @return cms_form_element
	 */
	public function getWithValues($cfev_id) {
		
		$sp = _daoSp ()->addCondition ('cfe_id', '=', $cfev_id)
			->addCondition ('cfe_deleted_at', '=', null);
		$arResult = DAOcms_form_element::instance ()->findBy($sp);
		
		if (count($arResult) != 1) {
			return null;
		}
		
		$toReturn = $arResult[0];
		
		//Affichage de la liste des valeurs
		//TODO mieux gérer les types avec liste de valeur ?
		$arTypeWithValues = array('select','checkbox','radio');
		if (in_array($toReturn->cfe_type,  $arTypeWithValues))  {
			$toReturn->arValues = DAOcms_form_element_values::instance ()->findByElement($cfev_id);
		}
		
		return $toReturn;
	}
	
	/**
	 * (soft delele) On ne supprime pas les valeurs pour garder des statistiques cohérentes 
	 * @param $record
	 * @return int
	 */
	public function delete ($record) {
		$record->cfe_deleted_at = date ('YmdHis');
		return $this->update($record);
	}
	
}