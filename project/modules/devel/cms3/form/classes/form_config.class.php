<?php
/**
 * @package cms
 * @subpackage form
 * @author Nicolas Bastien
 */

/**
 * Chargement de la configuration des formulaires
 * @package cms
 * @subpackage form
 * @author Nicolas Bastien
 */
class Form_Config {

	/**
	 * Liste des thèmes disponibles
	 * @var array
	 */
	protected $_arThemes = null;
	
	/**
	 * Liste des routes disponibles
	 * @var array
	 */
	protected $_arRoutes = null;
	
	/**
	 * Liste des champs disponibles
	 * @var array
	 */
	protected $_arFields = null;
	
	/**
	 * Liste les données pour l'initialisation des champs
	 * @var array
	 */
	protected $_arFormData = null;
	
	/**
	 * Renvoit la liste des thèmes disponibles
	 * @return array
	 */
	public function getThemes() {
		if ($this->_arThemes == null) {
			$this->_loadThemes();
		}
		return $this->_arThemes;
	}
	
	/**
	 * Renvoit la liste des routes disponibles
	 * @return array
	 */
	public function getRoutes() {
		if ($this->_arRoutes == null) {
			$this->_loadRoutes();
		}
		return $this->_arRoutes;
	}
	
	/**
	 * Renvoit la liste des champs disponibles
	 * @return array
	 */
	public function getFields() {
		if ($this->_arFields == null) {
			$this->_loadFields();
		}
		return $this->_arFields;
	}
	
	public function getFormData() {
		if ($this->_arFormData == null) {
			$this->_loadFormData();
		}
		return $this->_arFormData;
	}
	
	/**
	 * Parse le retour de CopixModule::getParsedModuleInformation
	 * @param $moduleNode
	 * @return array
	 */
	public function parseModuleInformation($moduleNode) {
		$toReturn = array();
		foreach ($moduleNode as $moduleName=>$moduleNodes) {
			foreach ($moduleNodes as $node){
				if ($node->getName () === 'type') {
					//l'identifiant de l'élément
					$id = _toString ($node['id']);
					$toReturn[$id]['caption'] = _toString ($node['caption']);
					$toReturn[$id]['classid'] = _toString ($node['classid']);
				}
			}
		}
		return $toReturn;
	}
	
	/* *** Themes *** */
	
	/**
	 * Chargement des thèmes
	 * @return array
	 */
	private function _loadThemes() {
		$this->_arThemes = array();
		foreach ($this->_getThemes() as $key => $value) {
			$this->_arThemes[$key] = $value['caption'];
		}
		return $this->_arThemes;
	}
	
	/**
	 * Listes les différents thémes disponibles 
	 * @return array
	 */
	private function _getThemes() {
		$toReturn = CopixModule::getParsedModuleInformation (
				"cms_form_themes",
				"/moduledefinition/registry/entry[@id='FormTheme']/*",
				array($this, 'parseModuleInformation'));
			
		return ($toReturn != null) ? $toReturn : array();
	}
	
	
	/* *** Routes *** */
	
	/**
	 * Chargement des routes
	 * @return array
	 */
	private function _loadRoutes() {
		$this->_arRoutes = array();
		foreach ($this->_getRoutes() as $key => $value) {
			$this->_arRoutes[$key] = $value['caption'];
		}
		return $this->_arRoutes;
	}
	
	/**
	 * Listes les différents mode de soumission de formulaire 
	 * @return array
	 */
	private function _getRoutes() {
		$toReturn = CopixModule::getParsedModuleInformation (
				"cms_form_routes",
				"/moduledefinition/registry/entry[@id='FormRoute']/*",
				array($this, 'parseModuleInformation'));
				
		return ($toReturn != null) ? $toReturn : array();
	}
	
	
	
	/* *** Fields *** */
	
	/**
	 * Chargement des champs
	 * @return array
	 */
	private function _loadFields() {
		$this->_arFields = array();
		foreach ($this->_getFields() as $key => $value) {
			$this->_arFields[$key] = $value['caption'];
		}
		return $this->_arFields;
	}
	
	/**
	 * Listes les différents types de champs disponibles
	 * @return array
	 */
	private function _getFields() {
		$toReturn = CopixModule::getParsedModuleInformation (
				"cms_form_fields",
				"/moduledefinition/registry/entry[@id='FormField']/*",
				array($this, 'parseModuleInformation'));
				
		return ($toReturn != null) ? $toReturn : array();
	}
	
	/* *** Données de l'utilisateur *** */
	/**
	 * Chargement des données utilisateur disponibles
	 * @return array
	 */
	private function _loadFormData() {
		$this->_arFormData = array();
		foreach ($this->_getFormData() as $key => $value) {
			$formDataClassName = $value['classid'];
			$formDataClass = new $formDataClassName();
			$this->_arFormData = array_merge($this->_arFormData, $formDataClass->getUserInfos());
		}
		return $this->_arFormData;
	}
	
	/**
	 * Listes les différentes classes FormData
	 * @return array
	 */
	private function _getFormData() {
		$toReturn = CopixModule::getParsedModuleInformation (
				"cms_form_data",
				"/moduledefinition/registry/entry[@id='FormData']/*",
				array($this, 'parseModuleInformation'));
				
		return ($toReturn != null) ? $toReturn : array();
	}
	
}