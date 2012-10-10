<?php
/**
 * @package		copix
 * @subpackage	forms
 * @author		Nicolas Bastien
 */

/**
 * Classe abstraite de base
 * 
 * Gère l'enregistrement des données saisies en base
 * @package copix
 * @subpackage forms
 * @author Nicolas Bastien
 */
abstract class CopixAbstractFormRoute implements ICopixFormRoute {
	
	protected $_params = null;
	protected $_arLabel = array();
	
	public function __construct($pForm) {
		$this->_params = unserialize($pForm->cf_route_params);
		$this->_arLabel = $pForm->getLabels();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see forms/ICopixFormRoute#process()
	 */
	public function process($arData) {
		//Enregistrement des données en base
		$this->saveData($arData);
		
		//Formatage des données
		$arDisplayData = array();
		foreach ($arData as $key=>$value) {
			if (isset($this->_arLabel[$key]) 
			&&  $this->_arLabel[$key] != null) {
				$arDisplayData[$this->_arLabel[$key]] = $value;
			}
		}
		
		//Traitement spécifique
		$this->_process($arDisplayData);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see forms/ICopixFormRoute#saveData()
	 */
	public function saveData($arData) {
		
		$cf_id = $arData['cf_id'];
		
		unset($arData['cf_id']);
		unset($arData['cf_theme']);
		unset($arData['submit']);
		
		$currentDate = date ('YmdHis');
		$ipUser = $_SERVER['REMOTE_ADDR'];
		
		foreach ($arData as $key => $data) {
			if (empty($data)) {
				//On n'enregistre pas les valeurs vide
				continue;
			}
			
			//Récupération de l'id du cms_form_element
			//Rq: Les données venant de l'objet CopixForm, on a pas besoin de vérifier l'existence des cms_form_element
			$id_element = substr(strrchr($key, '_'), 1);
			
			$record = _record('cms_form_values');
			$record->cfv_id_form = $cf_id;
			$record->cfv_id_element = $id_element;
			$record->cfv_value = is_array($data) ? implode(' - ', $data) : trim($data);
			
			$record->cfv_date = $currentDate;
			$record->cfv_ip_user = $ipUser;
			
			_ioDAO('cms_form_values')->insert($record);
		}
	}
	
	/**
	 * Traitement spécifique à définir dans les classes filles
	 * @param $arData
	 * @return void
	 */
	protected function _process($arData) {}
	
	/**
	 * Traitement spécifique à définir dans les classes filles
	 * @param $form
	 * @param $strCfRouteParams
	 * @return CopixFormLight
	 */
	protected function _getFormParams($form, $strCfRouteParams){}
	
}