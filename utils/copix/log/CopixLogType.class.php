<?php
/**
 * @package copix
 * @subpackage log
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Type de log définit dans un module
 *
 * @package copix
 * @subpackage logs
 */
class CopixLogType {
	/**
	 * Identifiant du type
	 *
	 * @var string
	 */
	private $_id = null;
	
	/**
	 * Indique si le type provient de Copix
	 *
	 * @var boolean
	 */
	private $_isFromCopix = false;
	
	/**
	 * Nom du type
	 *
	 * @var string
	 */
	private $_caption = null;
	
	/**
	 * Module qui a définit ce type
	 *
	 * @var string
	 */
	private $_module = null;
	
	/**
	 * Constructeur
	 *
	 * @param string $pId Identifiant
	 */
	public function __construct ($pId) {
		if (substr ($pId, 0, 6) == 'copix:') {
			$this->_isFromCopix = true;
			$this->_id = substr ($pId, 6);
		} else {
			$this->_isFromCopix = false;
			list ($this->_module, $this->_id) = explode ('|', $pId);
		}
	}
	
	/**
	 * Retourne l'identifiant du type
	 *
	 * @return string
	 */
	public function getId () {
		return $this->_id;
	}
	
	/**
	 * Définit le nom du type
	 *
	 * @param string $pCaption Nom
	 */
	public function setCaption ($pCaption) {
		$this->_caption = $pCaption;
	}
	
	/**
	 * Retourne le nom du type, ou le type si le nom n'a pas été définit
	 *
	 * @return string
	 */
	public function getCaption () {
		return $this->_caption;
	}
	
	/**
	 * Indique si le type provient de Copix
	 *
	 * @return boolean
	 */
	public function isFromCopix () {
		return $this->_isFromCopix;
	}

	/**
	 * Retourne le nom du module dont est tiré ce type de log
	 *
	 * @return string
	 */
	public function getModule () {
		return $this->_module;
	}
}