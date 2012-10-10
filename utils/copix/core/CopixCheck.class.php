<?php
/**
 * @package		copix
 * @subpackage	core
 * @author		Favre Brice
 * @copyright	CopixTeam
 * @link 		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe de base pour les exceptions sur les vérifications
 * 
 * @package		copix
 * @subpackage	core
 */

class CopixCheckException extends CopixException {
	
}

/**
 * Classe permettant d'effectuer des tests sur un objet 
 *
 * @package copix
 * @subpackage core
 */
class CopixCheck {
	
	/**
	 * Objet de retour d'un check
	 *
	 * @var CopixPPO
	 */
	private $_returnObject; 
	
	/**
	 * Tableaux des paramètres à vérifier
	 *
	 * @var array
	 */
	private $_arParamsToCheck = array ();
	
	/**
	 * Constructeur de classe
	 *
	 */
	function __construct (){
		$this->_returnObject = new CopixPPO ();
		$this->_returnObject->isFault = false;
	}
	
	/**
	 * Fonction de check
	 *
	 * @param object $pObject Fait le check sur l'objet, sur _request si l'objet est nul
	 * @return CopixPPO Objet contenant la liste des erreurs sur le check
	 */
	function check ($pObject = null){
		foreach ($this->_arParamsToCheck as $param) {

			if ($pObject !== null) {
				// On teste la présence du paramètre dans l'objet
				if (isset ($pObject->$param) && $pObject->$param != '') {
					$this->_returnObject->$param = true;
				} else {
					$this->_returnObject->$param = false;
					$this->_returnObject->isFault = true;
				}
			} else {
				// On test sur Request
				if (_request ($param, null) !== null) {
					$this->_returnObject->$param = true;
				} else {
					$this->_returnObject->$param = false;
					$this->_returnObject->isFault = true;
				}
			}
		}
		return $this->_returnObject;
	}
	

	/**
	 * Ajout de param de check
	 *
	 * @param array $pParams
	 * @return object renvoie l'objet courant
	 */
	function addParams ($pParams){
		foreach ($pParams as $param) {
			$this->_arParamsToCheck[] = $param;
		}
		return $this;
	}
}
?>