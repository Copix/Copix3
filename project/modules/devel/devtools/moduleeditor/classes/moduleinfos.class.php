<?php
/**
 * Informations sur un module
 */
class ModuleInfos {
	private $_name = null;
	public $description = null;
	public $descriptionI18n = null;
	public $longDescription = null;
	public $longDescriptionI18n = null;
	public $version = null;
	public $group = null;

	/**
	 * Constructeur
	 */	
	public function __construct ($pName, $pDescription = null, $pLongDescription = null, $pDescriptionI18n = null, $pLongDescriptionI18n = null, $pVersion = null) {
		$this->setName ($pName);
		$this->description = $pDescription;
		$this->descriptionI18n = $pDescriptionI18n;
		$this->longDescription = $pLongDescription;
		$this->longDescriptionI18n = $pLongDescriptionI18n;
		$this->version = $pVersion;
		$this->group = new ModuleGroupInfos ();
	}
	
	/**
	 * Définition de la propriété name
	 */
	public function setName ($pName) {
		// validité du nom
		if (!CopixModule::isValidName ($pName)) {
			throw new CopixException (_i18n ('copixmodule.error.invalidModuleName'));
		}
		$this->_name = $pName;
	}
	
	/**
	 * Retourne la propriété name
	 */
	public function getName () {
		return $this->_name;
	}
}

/**
 * Informations sur le groupe auquel appartient un module
 */
class ModuleGroupInfos {
	public $id = null;
	public $caption = null;
	public $captionI18n = null;
}
?>
