<?php
/**
 * @package tools
 * @subpackage advancedehelp
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://www.copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser Public Licence, see LICENCE file
 */

/**
 * Informations sur un répertoire
 *
 * @package tools
 * @subpackage advancedhelp
 */
class AHelpFoldersFolder {
	/**
	 * Identifiant
	 *
	 * @var int
	 */
	private $_id = null;

	/**
	 * Identifiant du dossier parent
	 *
	 * @var int
	 */
	private $_idParent = null;

	/**
	 * Nom du dossier
	 *
	 * @var string
	 */
	private $_caption = null;

	/**
	 * Définit l'identifiant
	 *
	 * @param int $pId Identifiant
	 */
	public function setId ($pId) {
		$this->_id = $pId;
	}

	/**
	 * Retourne l'identifiant
	 *
	 * @return int
	 */
	public function getId () {
		return $this->_id;
	}

	/**
	 * Définit l'identifiant du dossier parent
	 *
	 * @param int $pIdParent Identifiant du dossier parent
	 */
	public function setIdParent ($pIdParent) {
		$this->_idParent = $pIdParent;
	}

	/**
	 * Retourne l'identifiant du dossier parent
	 *
	 * @return int
	 */
	public function getIdParent () {
		return $this->_idParent;
	}

	/**
	 * Retourne le dossier parent
	 *
	 * @return AFoldersFolder
	 */
	public function getParent () {
		return AHelpFoldersServices::get ($this->_idParent);
	}

	/**
	 * Définit le nom du dossier
	 *
	 * @param string $pCaption Nom du dossier
	 */
	public function setCaption ($pCaption) {
		$this->_caption = $pCaption;
	}

	/**
	 * Retourne le nom du dossier
	 *
	 * @return string
	 */
	public function getCaption () {
		return $this->_caption;
	}
}