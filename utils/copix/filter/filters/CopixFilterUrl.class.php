<?php
/**
 * @package		copix
 * @subpackage	filter
 * @author		Croës Gérald
 * @copyright	CopixTeam
 * @link 		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Filtres pour récupérer des données url
 * 
 * @package		copix
 * @subpackage	filter
 */
class CopixFilterUrl extends CopixAbstractFilter {
	/**
	 * Initialisation du filtre 
	 */
	public function __construct ($pParams = array ()){
	}

	/**
	 * Récupération d'un boolean a partir d'une chaine
	 * 
	 * @param mixed $pValue la valeur à tester 
	 * @return boolean
	 */
	public function get ($pValue){
	    $protocol = $this->getParam('protocol','http');
	    
	    if (strpos ($pValue, '://') === false) {
	        $pValue = $protocol.'://'.$pValue;
	    }
	    return $pValue;
	}
}