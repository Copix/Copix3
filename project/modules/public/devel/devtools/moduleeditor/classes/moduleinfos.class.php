<?php

class ModuleInfos {
	private $_name = null;
	public $description = null;
	public $descriptionI18n = null;
	public $longDescription = null;
	public $longDescriptionI18n = null;

	/**
	 * Constructeur
	 */	
	public function __construct ($pName, $pDescription = null, $pLongDescription = null, $pDescriptionI18n = null, $pLongDescriptionI18n = null) {
		$this->setName ($pName);
		$this->description = $pDescription;
		$this->descriptionI18n = $pDescriptionI18n;
		$this->longDescription = $pLongDescription;
		$this->longDescriptionI18n = $pLongDescriptionI18n;
	}
	
	/**
	 * Définition de la propriété name
	 */
	public function setName ($pName) {
		$this->_name = str_replace (' ', '_', $pName); 
	}
	
	/**
	 * Retourne la propriété name
	 */
	public function getName () {
		return $this->_name;
	}
}
?>
