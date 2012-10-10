<?php

class ActiongroupInfos {
	private $_name = null;
	public $description = null;
	public $author = null;
	public $package = null;
	public $subpackage = null;
	public $link = null;
	public $copyright = null;
	public $license = null;
	
	/**
	 * Constructeur
	 */	
	public function __construct ($pName, $pDescription = null, $pAuthor = null, $pPackage = null, $pSubpackage = null, $pLink = null, $pCopyright = null, $pLicense = null) {
		$this->setName ($pName);
		$this->description = (is_null ($pDescription)) ? _i18n ('createmodule.actiongroup.defaultDescription') : $pDescription;
		$this->author = $pAuthor;
		$this->package = $pPackage;
		$this->subpackage = $pSubpackage;
		$this->link = (is_null ($pLink)) ? 'http://www.copix.org' : $pLink;
		$this->copyright = (is_null ($pCopyright)) ? 'CopixTeam' : $pCopyright;
		$this->license = (is_null ($pLicense)) ? 'http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file' : $pLicense;
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
