<?php
/**
 * @package		copix
 * @subpackage	forms
 * @author		Salleyron Julien, Croës Gérald
 * @copyright	CopixTeam
 * @link			http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @experimental
 */

/**
 * Exceptions de base sur les formulaires
 * @package copix
 * @subpackage forms
 */
class CopixFormCheckException extends CopixFormException {
	/**
	 * Tableau d'erreurs de vérification
	 *
	 * @var array
	 */
	private $_arErrors = array ();
	
	
	/**
	 * Nouvelle exception de vérification
	 *
	 * @param mixed  $pMessage tableau ou chaine de caractère qui représente l'erreur
	 * @param string $pField   le nom du champ concerné par l'erreur 
	 */
	public function __construct ($pMessage, $pField = null) {
		if (is_array ($pMessage)) {
			$this->_arErrors = $this->_arErrors + $pMessage; 
		} else {
			if ($pField != null) {
			    $this->_arErrors[$pField] = $pMessage;
			} else {
				$this->_arErrors[] = $pMessage;
			}
		}
		parent::__construct ($this->getErrorMessage ());
	}
	
	/**
	 * Récupération du tableau d'erreur passé a la construction de l'exception
	 *
	 * @return array
	 */
	public function getErrors () {
		return $this->_arErrors;
	}
	
	/**
	 * On récupère le message d'erreur associé a l'exception
	 *
	 * @return string
	 */
	public function getErrorMessage () {
		$toReturn = '';
		foreach ($this->_arErrors as $key=>$error) {
			if (is_array($error)) {
				foreach ($error as $errorMessage) {
					$toReturn .= $key.' : '.$errorMessage."\n";
				}
			} else {
				$toReturn .= $key.' : '.$error."\n";
			}
		}
		return $toReturn;
	}
}