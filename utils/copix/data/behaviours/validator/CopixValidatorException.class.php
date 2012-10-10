<?php
/**
 * @package		copix
 * @subpackage	validator
 * @author		Gérald Croës, Salleyron Julien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Exception de validator
 * @package		copix
 * @subpackage	validator
 */
class CopixValidatorException extends CopixException {
	/**
	 * Erreurs qui ont donné lieu à l'exception
	 * @var CopixErrorObject
	 */
	private $_errors;
	
	/**
	 * Constructeur qui génère le message de l'exception
	 *
	 * @param array $pErrors Tableau d'erreur
	 */
	public function __construct ($pErrors) {
		$this->_errors = $pErrors;
		parent::__construct (_toString ($pErrors));
	}
	
	/**
	 * Renvoi les erreurs de l'exception
	 * @return CopixErrorObject
	 */
	public function getErrorObject () {
		return $this->_errors;
	}
}