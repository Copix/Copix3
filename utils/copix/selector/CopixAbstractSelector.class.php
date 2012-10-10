<?php
/**
 * @package		copix
 * @subpackage	core
 * @author		Gérald Croes
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe de base des selecteurs
 * @package copix
 * @subpackage core
 */
abstract class CopixAbstractSelector {
	var $type      = null;
	var $typeValue = null;
	var $fileName  = null;

	/**
	 * Le chemin de la ressource
	 * @var string
	 */
	private $_path = null;

	/**
	 * Le chemin surchargé de la ressource
	 * @var string
	 */
	private $_overloadedPath = null;

	/**
	 * Le sélecteur complet de la ressource
	 * @var string
	 */
	private $_selector = null;

	/**
	 * Le qualificateur Copix de l'élément
	 * @var string
	 */
	private $_qualifier = null;

	/**
	 * Récupère le chemin de la ressource, gère un cache d'appel aux méthodes des fil
	 * @return string le chemin
	 */
	public function getPath ($directory = ''){
		if (!isset ($this->_path[$directory])){
			$this->_path[$directory] = $this->_getPath ($directory);
		}
		return $this->_path[$directory];
	}
	abstract protected function _getPath ($directory);

	/**
	 * Récupère le chemin surchargé d'une ressource, gère un cache d'appel aux méthodes des fils.
	 * @return string le chemin surchargé
	 */
	public function getOverloadedPath ($directory=''){
		if (!isset ($this->_overloadedPath[$directory])){
			$this->_overloadedPath[$directory] = $this->_getOverloadedPath ($directory);
		}
		return $this->_overloadedPath[$directory];
	}
	protected function _getOverloadedPath ($directory){return null;}

	/**
	 * Ré&cupération du sélecteur avec implémentation d'un cache
	 */
	public function getSelector (){
		if (!isset ($this->_selector)){
			$this->_selector = $this->_getSelector ();
		}
		return $this->_selector;
	}
	abstract protected function _getSelector ();

	/**
	 * Récupèration du qualificateur avec implémentation d'un cache
	 */
	public function getQualifier (){
		if (!isset ($this->_qualifier)){
			$this->_qualifier = $this->_getQualifier ();
		}
		return $this->_qualifier;
	}
	abstract protected function _getQualifier ();
}