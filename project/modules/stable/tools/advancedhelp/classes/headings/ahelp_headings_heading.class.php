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
 * Informations sur une rubrique
 *
 * @package tools
 * @subpackage advancedhelp
 */
class AHelpHeadingsHeading {
	/**
	 * Identifiant
	 *
	 * @var int
	 */
	private $_id = null;

	/**
	 * Nom
	 *
	 * @var string
	 */
	private $_caption = null;

	/**
	 * Description
	 *
	 * @var string
	 */
	private $_description = null;

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
	 * Définit le nom de la rubrique
	 *
	 * @param string $pCaption Nom de la rubrique
	 */
	public function setCaption ($pCaption) {
		$this->_caption = $pCaption;
	}

	/**
	 * Retourne le nom de la rubrique
	 *
	 * @return string
	 */
	public function getCaption () {
		return $this->_caption;
	}

	/**
	 * Définit la description de la rubrique
	 *
	 * @param string $pCaption Nom de la rubrique
	 */
	public function setDescription ($pDescription) {
		$this->_description = $pDescription;
	}

	/**
	 * Retourne la description de la rubrique
	 *
	 * @return string
	 */
	public function getDescription () {
		return $this->_description;
	}
}