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
	public function __construct ($pName, $pDescription = null, $pAuthor = null, $pPackage = null, $pSubpackage = null, $pLink = 'http://www.copix.org', $pCopyright = 'CopixTeam', $pLicense = 'http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file') {
		$this->setName ($pName);
		$this->description = (is_null ($pDescription)) ? _i18n ('createmodule.actiongroup.defaultDescription') : $pDescription;
		$this->author = $pAuthor;
		$this->package = $pPackage;
		$this->subpackage = $pSubpackage;
		$this->link = $pLink;
		$this->copyright = $pCopyright;
		$this->license = $pLicense;
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
