<?php

class ActionInfos {
	private $_name = null;
	public $description = null;
	
	/**
	 * Constructeur
	 */
	public function __construct ($pName, $pDescription = null) {
		$this->setName ($pName);
		$this->description = (is_null ($pDescription)) ? _i18n ('createmodule.action.defaultDescription') : $pDescription;
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
